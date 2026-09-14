<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Services\NotificationService;
use App\Services\PushNotificationService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
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
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $transaction->id,
            'status' => $transaction->status,
            'total_amount' => $transaction->total_amount,
            'motorbike_registration' => $transaction->motorbike_registration,
            'dispatched_at' => $transaction->dispatched_at,
            'delivered_at' => $transaction->delivered_at,
            'delivery_time_minutes' => $transaction->delivery_time_minutes,
            'items' => $transaction->items->load('product'),
        ]);
    }

    public function store(Request $request, NotificationService $notifier, StockService $stockService, LoyaltyService $loyalty)
    {
        $customer = $request->user('customer');

        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'payment_method' => 'required|in:cash,wallet',
            'fulfillment_type' => 'nullable|string|in:pickup,delivery',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_latitude' => 'nullable|numeric|between:-90,90',
            'delivery_longitude' => 'nullable|numeric|between:-180,180',
            'delivery_address_note' => 'nullable|string|max:255',
            'delivery_address' => 'nullable|string|max:255',
            'delivery_notes' => 'nullable|string|max:255',
        ]);

        $shortfalls = $stockService->checkAvailabilityDetailed($validated['station_id'], $validated['items']);
        if ($shortfalls) {
            $notifier->send($customer, 'stock_shortfall', $this->buildShortfallMessage($shortfalls));
            return response()->json(['message' => "We couldn't fulfill your full order.", 'shortfalls' => $shortfalls], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $customer, $loyalty) {
                $itemsData = [];
                $discountItems = [];
                $grossTotal = 0.0;

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if (! $product->is_active) {
                        throw new \RuntimeException("{$product->name} is currently unavailable.");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $grossTotal += $subtotal;

                    $discountItems[] = [
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                        'is_water' => (bool) ($product->is_water ?? false),
                    ];

                    $itemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ];
                }

                $discountAmount = $loyalty->calculateDiscount($customer, $discountItems);
                $grossTotal = round($grossTotal, 2);
                $discountAmount = round($discountAmount, 2);
                $finalAmount = max(0, round($grossTotal - $discountAmount, 2));

                if ($validated['payment_method'] === 'wallet') {
                    if ($customer->wallet_balance < $finalAmount) {
                        throw new \RuntimeException('Insufficient wallet balance. Please top up or pay cash.');
                    }
                    $customer->decrement('wallet_balance', $finalAmount);
                }

                $transaction = Transaction::create([
                    'station_id' => $validated['station_id'],
                    'customer_id' => $customer->id,
                    'gross_amount' => $grossTotal,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $finalAmount,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'pending',
                    'delivery_latitude' => $validated['delivery_latitude'] ?? null,
                    'delivery_longitude' => $validated['delivery_longitude'] ?? null,
                    'delivery_address_note' => $validated['delivery_address_note'] ?? null,
                ]);

                // Create Delivery record & auto-assign workload driver
                $isDelivery = ($validated['fulfillment_type'] ?? null) === 'delivery' || ! empty($validated['delivery_address']);
                if ($isDelivery) {
                    $assignedDriver = User::where('role', 'driver')
                        ->withCount(['deliveries' => function ($query) {
                            $query->whereIn('status', ['assigned', 'out_for_delivery']);
                        }])
                        ->orderBy('deliveries_count', 'asc')
                        ->first();

                    Delivery::create([
                        'transaction_id'   => $transaction->id,
                        'driver_id'        => $assignedDriver?->id,
                        'status'           => $assignedDriver ? 'assigned' : 'pending',
                        'delivery_address' => $validated['delivery_address'] ?? $customer->address,
                        'delivery_notes'   => $validated['delivery_notes'] ?? null,
                        'scheduled_at'     => now(),
                    ]);
                }

                foreach ($itemsData as $item) {
                    $transaction->items()->create($item);
                }

                if ($discountAmount > 0) {
                    $loyalty->markDiscountAsUsed($customer);
                }

                return $transaction;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // Trigger push notification to customer PWA
        PushNotificationService::sendToCustomer(
            $customer,
            'Order Confirmed! 🚚',
            "Your delivery #{$transaction->id} has been received and scheduled.",
            ['transaction_id' => $transaction->id]
        );

        $notifier->orderConfirmation($customer, $transaction->total_amount);

        return new TransactionResource($transaction->load(['station', 'items.product']));
    }

    public function cancel(Request $request, Transaction $transaction, StockService $stockService)
    {
        $customer = $request->user('customer');

        if ($transaction->customer_id !== $customer->id) {
            abort(403, 'This order does not belong to you.');
        }

        if (! in_array($transaction->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Orders that are dispatched or completed cannot be cancelled.'], 422);
        }

        DB::transaction(function () use ($transaction, $customer, $stockService) {
            $stockLeftStation = in_array($transaction->status, ['dispatched', 'completed']);

            $transaction->update(['status' => 'refunded']);

            if ($transaction->payment_method === 'wallet') {
                $customer->increment('wallet_balance', $transaction->total_amount);
            }

            if ($stockLeftStation) {
                $items = $transaction->items->map(fn ($i) => [
                    'product_id' => $i->product_id,
                    'quantity' => $i->quantity,
                ])->toArray();

                $stockService->restore($transaction->station_id, $items);
            }
        });

        return response()->json(['message' => 'Order cancelled and funds refunded to wallet.']);
    }

    public function topUpWallet(Request $request)
    {
        $customer = $request->user('customer');
        $validated = $request->validate(['amount' => 'required|numeric|min:1']);

        $customer->increment('wallet_balance', $validated['amount']);

        return response()->json([
            'message' => 'Wallet topped up (simulated — connect M-Pesa for real payments)',
            'wallet_balance' => $customer->fresh()->wallet_balance,
        ]);
    }

    private function buildShortfallMessage(array $shortfalls): string
    {
        $lines = collect($shortfalls)->map(function ($s) {
            return $s['max_available'] > 0
                ? "{$s['product_name']}: only {$s['max_available']} available (you requested {$s['requested']})"
                : "{$s['product_name']}: currently out of stock";
        });

        return "We couldn't fulfill your full order. " . $lines->join('; ') . '.';
    }
}