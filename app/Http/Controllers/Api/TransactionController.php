<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Station;
use App\Models\Customer;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['station', 'customer', 'items.product']);

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(15);

        return TransactionResource::collection($transactions);
    }

    public function store(Request $request, NotificationService $notifier)
    {
        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,mpesa,wallet,card,credit',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validated['payment_method'] === 'credit' && empty($validated['customer_id'])) {
            return response()->json(['message' => 'A customer is required for credit sales.'], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($validated) {
                $totalAmount = 0;
                $itemsData = [];
                $litersUsed = 0;

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $subtotal = $product->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $itemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ];

                    // Only liquid units affect tank stock; pcs/dozen/tray (eggs, bottles) don't
                    if ($product->unit === 'l') {
                        $litersUsed += $product->size_value * $item['quantity'];
                    } elseif ($product->unit === 'ml') {
                        $litersUsed += ($product->size_value / 1000) * $item['quantity'];
                    }
                }

                if ($validated['payment_method'] === 'credit') {
                    $customer = Customer::findOrFail($validated['customer_id']);
                    $available = $customer->credit_limit - $customer->credit_balance;

                    if ($totalAmount > $available) {
                        throw new \RuntimeException("Credit limit exceeded. Available credit: {$available}");
                    }

                    $customer->increment('credit_balance', $totalAmount);
                }

                if ($validated['payment_method'] === 'wallet') {
                    $customer = Customer::findOrFail($validated['customer_id']);

                    if ($customer->wallet_balance < $totalAmount) {
                        throw new \RuntimeException('Insufficient wallet balance.');
                    }

                    $customer->decrement('wallet_balance', $totalAmount);
                }

                $transaction = Transaction::create([
                    'station_id' => $validated['station_id'],
                    'customer_id' => $validated['customer_id'] ?? null,
                    'total_amount' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed', // staff-recorded sales to complete immediately
                ]);

                foreach ($itemsData as $data) {
                    $transaction->items()->create($data);
                }

                $station = Station::find($validated['station_id']);
                if ($station && $station->current_level_liters !== null && $litersUsed > 0) {
                    $station->decrement('current_level_liters', $litersUsed);
                }

                return $transaction;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $transaction->load(['station', 'customer', 'items.product']);

        if ($transaction->customer) {
            $notifier->orderConfirmation($transaction->customer, $transaction->total_amount);

            if ($transaction->payment_method === 'credit') {
                $notifier->creditReminder($transaction->customer->fresh());
            }
        }

        return new TransactionResource($transaction);
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load(['station', 'customer', 'items.product']));
    }

    // Deliberately NOT allowing status changes here — use confirmPickup()/cancel() instead,
    // since those correctly reverse wallet/credit/stock. This only allows correcting metadata.
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'payment_method' => 'sometimes|in:cash,mpesa,wallet,card,credit',
        ]);

        $transaction->update($validated);

        return new TransactionResource($transaction->load(['station', 'customer', 'items.product']));
    }

    // Hard delete only allowed for transactions that never affected stock/money (rare — e.g. test data)
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status !== 'refunded') {
            return response()->json([
                'message' => 'Only cancelled/refunded transactions can be permanently deleted. Cancel it first.',
            ], 422);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted'], 200);
    }

    public function confirmPickup(Transaction $transaction, NotificationService $notifier)
    {
        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be confirmed.'], 422);
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update(['status' => 'completed']);

            $station = Station::find($transaction->station_id);
            if ($station && $station->current_level_liters !== null) {
                $litersUsed = $transaction->items->sum(function ($item) {
                    $product = $item->product;
                    if (!$product) return 0;
                    if ($product->unit === 'l') return $product->size_value * $item->quantity;
                    if ($product->unit === 'ml') return ($product->size_value / 1000) * $item->quantity;
                    return 0;
                });
                $station->decrement('current_level_liters', $litersUsed);
            }
        });

        $transaction = $transaction->fresh()->load(['station', 'customer', 'items.product']);

        if ($transaction->customer) {
            $notifier->orderConfirmation($transaction->customer, $transaction->total_amount);
        }

        return new TransactionResource($transaction);
    }

    public function cancel(Request $request, Transaction $transaction)
    {
        if ($transaction->status === 'refunded') {
            return response()->json(['message' => 'This order is already cancelled.'], 422);
        }

        DB::transaction(function () use ($transaction) {
            $wasCompleted = $transaction->status === 'completed';

            $transaction->update(['status' => 'refunded']);

            if ($transaction->payment_method === 'wallet' && $transaction->customer) {
                $transaction->customer->increment('wallet_balance', $transaction->total_amount);
            }

            if ($transaction->payment_method === 'credit' && $transaction->customer) {
                $transaction->customer->decrement('credit_balance', $transaction->total_amount);
            }

            if ($wasCompleted) {
                $station = Station::find($transaction->station_id);
                if ($station && $station->current_level_liters !== null) {
                    $litersUsed = $transaction->items->sum(function ($item) {
                        $product = $item->product;
                        if (!$product) return 0;
                        if ($product->unit === 'l') return $product->size_value * $item->quantity;
                        if ($product->unit === 'ml') return ($product->size_value / 1000) * $item->quantity;
                        return 0;
                    });
                    $station->increment('current_level_liters', $litersUsed);
                }
            }
        });

        return new TransactionResource($transaction->fresh()->load(['station', 'customer', 'items.product']));
    }
}