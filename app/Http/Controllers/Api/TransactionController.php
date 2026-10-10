<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\LoyaltyService;
use App\Services\NotificationService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['station', 'customer', 'items.product', 'deliveryStaff']);

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return TransactionResource::collection($query->latest()->paginate(15));
    }

    public function store(Request $request, LoyaltyService $loyaltyService, NotificationService $notifier)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'station_id'  => 'required|exists:stations,id',
            'items'       => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'payment_method'     => 'required|in:cash,mpesa,wallet,card,credit',
        ]);

        $customer = $validated['customer_id'] ? Customer::find($validated['customer_id']) : null;

        // Structure items and calculate gross total
        $formattedItems = [];
        $grossTotal = 0.00;

        foreach ($validated['items'] as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            $itemSubtotal = $product->price * $itemData['quantity'];
            $grossTotal += $itemSubtotal;

            $formattedItems[] = [
                'product_id' => $product->id,
                'price'      => $product->price,
                'quantity'   => $itemData['quantity'],
                'subtotal'   => $itemSubtotal,
                'is_water'   => strtolower($product->category->name ?? '') === 'water' || (bool) $product->is_water,
            ];
        }

        // Calculate Loyalty Discount using formattedItems
        $discountAmount = (float) $loyaltyService->calculateDiscount($customer, $formattedItems);
        $finalTotal = max(0, $grossTotal - $discountAmount);

        // Verify credit limits if paying with credit
        if ($validated['payment_method'] === 'credit') {
            if (!$customer) {
                return response()->json(['message' => 'A registered customer is required for credit transactions.'], 422);
            }

            $creditLimit = (float) ($customer->credit_limit ?? 0);
            $creditBalance = (float) ($customer->credit_balance ?? 0);

            if (($creditBalance + $finalTotal) > $creditLimit) {
                return response()->json([
                    'message'          => 'Credit limit exceeded.',
                    'credit_limit'     => $creditLimit,
                    'current_balance'  => $creditBalance,
                    'available_credit' => max(0, $creditLimit - $creditBalance),
                ], 422);
            }
        }

        $transaction = DB::transaction(function () use ($validated, $customer, $discountAmount, $finalTotal, $formattedItems, $loyaltyService) {
            $transaction = Transaction::create([
                'customer_id'     => $customer?->id,
                'station_id'      => $validated['station_id'],
                'total_amount'    => $finalTotal,
                'discount_amount' => $discountAmount,
                'payment_method'  => $validated['payment_method'],
                'status'          => 'pending',
            ]);

            foreach ($formattedItems as $item) {
                $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }

            // Adjust Customer Balance for Credit Sales
            if ($customer && $validated['payment_method'] === 'credit') {
                $customer->increment('credit_balance', $finalTotal);
            }

            // Mark loyalty discount as redeemed
            if ($customer && $discountAmount > 0) {
                $loyaltyService->markDiscountAsUsed($customer);
            }

            return $transaction;
        });

        if ($customer && $discountAmount > 0) {
            $notifier->notifyLoyaltyDiscount($customer, $discountAmount, $transaction);
        }

        return response()->json([
            'message'         => 'Order created successfully!',
            'discount_amount' => $discountAmount,
            'final_total'     => $finalTotal,
            'transaction'     => $transaction,
        ], 201);
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load(['station', 'customer', 'items.product', 'deliveryStaff']));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'payment_method' => 'sometimes|in:cash,mpesa,wallet,card,credit',
        ]);

        $transaction->update($validated);

        return new TransactionResource($transaction->load(['station', 'customer', 'items.product', 'deliveryStaff']));
    }

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

        $transaction->update(['status' => 'confirmed']);
        $transaction = $transaction->fresh()->load(['station', 'customer', 'items.product']);

        if ($transaction->customer) {
            $notifier->orderConfirmation($transaction->customer, $transaction->total_amount);
        }

        return new TransactionResource($transaction);
    }

    public function confirmDispatch(Request $request, Transaction $transaction, StockService $stockService, NotificationService $notifier)
    {
        if ($transaction->status === 'dispatched') {
            return response()->json([
                'message' => 'This order has already been dispatched.',
                'status' => 'failed'
            ], 400);
        }

        if ($transaction->status !== 'confirmed') {
            return response()->json([
                'message' => 'Order must be in confirmed status before it can be dispatched. Current status: ' . $transaction->status,
                'status' => 'failed'
            ], 400);
        }

        $validated = $request->validate([
            'delivery_staff_id' => 'required|exists:users,id',
            'motorbike_registration' => 'required|string|max:20',
        ]);

        $formattedReg = strtoupper(trim($validated['motorbike_registration']));

        $activeMotorbikeUsage = Transaction::where('motorbike_registration', $formattedReg)
            ->where('status', 'dispatched')
            ->where('id', '!=', $transaction->id)
            ->first();

        if ($activeMotorbikeUsage) {
            return response()->json([
                'message' => "Motorbike {$formattedReg} is currently active on order #{$activeMotorbikeUsage->id} and cannot be assigned.",
                'status' => 'failed'
            ], 422);
        }

        $items = $transaction->items->map(fn ($i) => [
            'product_id' => $i->product_id,
            'quantity' => $i->quantity,
        ])->toArray();

        $shortfalls = $stockService->checkAvailabilityDetailed($transaction->station_id, $items);
        if ($shortfalls) {
            return response()->json([
                'message' => 'Insufficient stock at the station to dispatch order.',
                'status' => 'failed'
            ], 400);
        }

        DB::transaction(function () use ($transaction, $validated, $formattedReg, $items, $stockService) {
            $transaction->update([
                'status' => 'dispatched',
                'delivery_staff_id' => $validated['delivery_staff_id'],
                'motorbike_registration' => $formattedReg,
                'dispatched_at' => now(),
            ]);

            $stockService->deduct($transaction->station_id, $items);
        });

        $transaction->refresh();
        if ($transaction->customer) {
            $notifier->orderDispatched($transaction->customer, $transaction);
        }

        return response()->json([
            'message' => 'Order dispatched successfully.',
            'transaction' => $transaction
        ], 200);
    }

    public function markDelivered(Transaction $transaction, NotificationService $notifier)
    {
        if ($transaction->status !== 'dispatched') {
            return response()->json(['message' => 'Only dispatched orders can be marked as delivered.'], 422);
        }

        $deliveredAt = now();
        $dispatchedAt = $transaction->dispatched_at ? Carbon::parse($transaction->dispatched_at) : Carbon::parse($transaction->created_at);
        $timeSpentMinutes = $dispatchedAt->diffInMinutes($deliveredAt);

        $benchmarkMinutes = 30;
        $score = max(0, 100 - max(0, $timeSpentMinutes - $benchmarkMinutes) * 2);

        $transaction->update([
            'status' => 'completed',
            'delivered_at' => $deliveredAt,
            'delivery_time_minutes' => $timeSpentMinutes,
            'delivery_score' => $score,
        ]);

        $transaction = $transaction->fresh()->load(['station', 'customer', 'items.product', 'deliveryStaff']);

        if ($transaction->customer) {
            $notifier->orderDelivered($transaction->customer, $transaction);
        }

        return new TransactionResource($transaction);
    }

    public function cancel(Transaction $transaction, StockService $stockService, NotificationService $notifier)
    {
        if ($transaction->status === 'refunded') {
            return response()->json(['message' => 'This order is already cancelled.'], 422);
        }

        DB::transaction(function () use ($transaction, $stockService) {
            $stockLeftStation = in_array($transaction->status, ['dispatched', 'completed']);

            $transaction->update(['status' => 'refunded']);

            if ($transaction->payment_method === 'wallet' && $transaction->customer) {
                $transaction->customer->increment('wallet_balance', $transaction->total_amount);
            }

            if ($transaction->payment_method === 'credit' && $transaction->customer) {
                $transaction->customer->decrement('credit_balance', $transaction->total_amount);
            }

            if ($stockLeftStation) {
                $items = $transaction->items->map(fn ($i) => [
                    'product_id' => $i->product_id,
                    'quantity' => $i->quantity,
                ])->toArray();

                $stockService->restore($transaction->station_id, $items);
            }
        });

        $transaction = $transaction->fresh()->load(['station', 'customer', 'items.product', 'deliveryStaff']);

        if ($transaction->customer) {
            $notifier->orderCancelled($transaction->customer, $transaction->total_amount);
        }

        return new TransactionResource($transaction);
    }
}