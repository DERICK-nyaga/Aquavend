<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Station;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Customer's own order history and order management (place new order, cancel pending order, view order details)
    public function index(Request $request)
    {
        $customer = $request->user('customer');

        $orders = Transaction::with(['station', 'items.product'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return TransactionResource::collection($orders);
    }

    public function show(Request $request, Transaction $transaction)
    {
        $customer = $request->user('customer');

        if ($transaction->customer_id !== $customer->id) {
            abort(403, 'This order does not belong to you.');
        }

        return new TransactionResource($transaction->load(['station', 'items.product']));
    }

    // Place a new order — payment_method locked to cash or wallet only
    public function store(Request $request, NotificationService $notifier)
    {
        $customer = $request->user('customer');

        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'payment_method' => 'required|in:cash,wallet',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $transaction = DB::transaction(function () use ($validated, $customer) {
                $totalAmount = 0;
                $itemsData = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if (! $product->is_active) {
                        abort(422, "{$product->name} is currently unavailable.");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $itemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                        'liters' => $product->unit === 'l' ? $product->size_value * $item['quantity']
                                  : ($product->unit === 'ml' ? ($product->size_value / 1000) * $item['quantity'] : 0),
                    ];
                }

                if ($validated['payment_method'] === 'wallet') {
                    if ($customer->wallet_balance < $totalAmount) {
                        abort(422, 'Insufficient wallet balance. Please top up or pay cash.');
                    }
                    $customer->decrement('wallet_balance', $totalAmount);
                }

                $transaction = Transaction::create([
                    'station_id' => $validated['station_id'],
                    'customer_id' => $customer->id,
                    'total_amount' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'status' => $validated['payment_method'] === 'cash' ? 'pending' : 'completed',
                ]);

                foreach ($itemsData as $item) {
                    $transaction->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                $station = Station::find($validated['station_id']);
                if ($station && $station->current_level_liters !== null && $validated['payment_method'] === 'wallet') {
                    // Only deduct stock immediately for confirmed (wallet-paid) orders.
                    // Cash orders deduct stock when staff mark them completed at pickup.
                    $litersUsed = collect($itemsData)->sum('liters');
                    $station->decrement('current_level_liters', $litersUsed);
                }

                return $transaction;
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $notifier->orderConfirmation($customer, $transaction->total_amount);

        return new TransactionResource($transaction->load(['station', 'items.product']));
    }

    public function cancel(Request $request, Transaction $transaction)
    {
        $customer = $request->user('customer');

        if ($transaction->customer_id !== $customer->id) {
            abort(403, 'This order does not belong to you.');
        }

        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled.'], 422);
        }

        $transaction->update(['status' => 'refunded']); // reuse existing enum value to mean "cancelled"

        // If it was a wallet payment already deducted, refund it
        if ($transaction->payment_method === 'wallet') {
            $customer->increment('wallet_balance', $transaction->total_amount);
        }

        return response()->json(['message' => 'Order cancelled']);
    }

    // Wallet top-up — stubbed until M-Pesa integration
    public function topUpWallet(Request $request)
    {
        $customer = $request->user('customer');

        $validated = $request->validate(['amount' => 'required|numeric|min:1']);

        // TODO: replace with real M-Pesa STK Push flow.
        $customer->increment('wallet_balance', $validated['amount']);

        return response()->json([
            'message' => 'Wallet topped up (simulated — connect M-Pesa for real payments)',
            'wallet_balance' => $customer->fresh()->wallet_balance,
        ]);
    }
}