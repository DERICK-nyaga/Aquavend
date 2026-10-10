<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\Station;
use App\Models\Transaction;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Services\NotificationService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionWebController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['station', 'customer', 'items.product', 'deliveryStaff'])
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function create(Request $request, LoyaltyService $loyaltyService)
    {
        $stations = Station::all();
        $products = Product::where('is_active', true)->get();
        $customers = Customer::all();

        $selectedCustomerId = old('customer_id', $request->query('customer_id'));
        $selectedCustomer = $selectedCustomerId ? Customer::find($selectedCustomerId) : null;
        $isLoyaltyEligible = $selectedCustomer ? $loyaltyService->isEligible($selectedCustomer) : false;

        return view('transactions.create', compact('stations', 'products', 'customers', 'isLoyaltyEligible'));
    }

    public function store(Request $request, NotificationService $notifier, StockService $stockService, LoyaltyService $loyaltyService)
    {
        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,mpesa,wallet,card,credit',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'integer|min:1',
        ]);

        if (in_array($validated['payment_method'], ['credit', 'wallet']) && empty($validated['customer_id'])) {
            return back()->withErrors('A customer is required for credit or wallet payments.')->withInput();
        }

        $items = [];
        foreach ($validated['product_id'] as $i => $productId) {
            $items[] = ['product_id' => $productId, 'quantity' => $validated['quantity'][$i]];
        }

        $shortfalls = $stockService->checkAvailabilityDetailed($validated['station_id'], $items);
        if ($shortfalls) {
            return back()->withErrors(['stock' => 'Some items exceed available stock.'])->withInput();
        }

        $requiresDeliveryFlow = in_array($validated['payment_method'], ['cash', 'wallet']);

        try {
            $transaction = DB::transaction(function () use ($validated, $items, $stockService, $requiresDeliveryFlow, $loyaltyService) {
                $grossTotal = 0;
                $itemsData = [];
                $cartForLoyalty = [];

                foreach ($validated['product_id'] as $i => $productId) {
                    $product = Product::find($productId);
                    $qty = $validated['quantity'][$i];
                    $subtotal = $product->price * $qty;
                    $grossTotal += $subtotal;

                    $itemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ];

                    $cartForLoyalty[] = [
                        'price' => $product->price,
                        'quantity' => $qty,
                        'is_water' => strtolower($product->category->name ?? '') === 'water' || (bool) $product->is_water,
                    ];
                }

                $customer = $validated['customer_id'] ? Customer::findOrFail($validated['customer_id']) : null;

                $discountAmount = (float) $loyaltyService->calculateDiscount($customer, $cartForLoyalty);
                $finalTotal = max(0, $grossTotal - $discountAmount);

                if ($validated['payment_method'] === 'credit' && $customer) {
                    $available = $customer->credit_limit - $customer->credit_balance;
                    if ($finalTotal > $available) {
                        throw new \RuntimeException("Credit limit exceeded. Available credit: KES {$available}");
                    }
                    $customer->increment('credit_balance', $finalTotal);
                }

                if ($validated['payment_method'] === 'wallet' && $customer) {
                    if ($customer->wallet_balance < $finalTotal) {
                        throw new \RuntimeException('Insufficient wallet balance for this customer.');
                    }
                    $customer->decrement('wallet_balance', $finalTotal);
                }

                $transaction = Transaction::create([
                    'station_id' => $validated['station_id'],
                    'customer_id' => $validated['customer_id'] ?? null,
                    'total_amount' => $finalTotal,
                    'discount_amount' => $discountAmount,
                    'payment_method' => $validated['payment_method'],
                    'status' => $requiresDeliveryFlow ? 'pending' : 'completed',
                ]);

                // Create Delivery record for web orders requiring delivery
                if ($requiresDeliveryFlow) {
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
                        'delivery_address' => $customer?->address ?? 'N/A',
                        'scheduled_at'     => now(),
                    ]);
                }

                foreach ($itemsData as $item) {
                    $transaction->items()->create($item);
                }

                if (!$requiresDeliveryFlow) {
                    $stockService->deduct($validated['station_id'], $items);
                }

                return $transaction;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['stock' => $e->getMessage()])->withInput();
        }

        $transaction->refresh();
        $customer = $transaction->customer;
        $discountAmount = (float) $transaction->discount_amount;

        if ($customer && $discountAmount > 0) {
            $loyaltyService->markDiscountAsUsed($customer);
            $notifier->notifyLoyaltyDiscount($customer, $discountAmount, $transaction);
        }

        if ($customer) {
            if ($requiresDeliveryFlow) {
                $notifier->orderReceived($customer, $transaction);
            } else {
                $notifier->orderConfirmation($customer, $transaction->total_amount);
            }
        }

        $message = $requiresDeliveryFlow
            ? 'Order created and pending confirmation.'
            : 'Sale recorded and completed.';

        if ($discountAmount > 0) {
            $message .= " Loyalty discount of KES " . number_format($discountAmount, 2) . " applied!";
        }

        return redirect('/transactions')->with('success', $message);
    }

    public function confirmPickup(Transaction $transaction, NotificationService $notifier)
    {
        if ($transaction->status !== 'pending') {
            return back()->withErrors('Only pending orders can be confirmed.');
        }

        $transaction->update(['status' => 'confirmed']);

        $transaction->refresh();
        if ($transaction->customer) {
            $notifier->orderConfirmation($transaction->customer, $transaction->total_amount);
        }

        return redirect()->back()->with('success', 'Order confirmed. Ready for dispatch.');
    }
    
    public function confirmDispatch(Request $request, Transaction $transaction, StockService $stockService, NotificationService $notifier)
    {
        if ($transaction->status !== 'confirmed') {
            return back()->withErrors([
                'dispatch' => 'This order cannot be dispatched because it has not been confirmed yet.'
            ]);
        }

        $validated = $request->validate([
            'delivery_staff_id' => 'required|exists:users,id',
            'motorbike_registration' => 'required|string|max:20',
        ], [
            'delivery_staff_id.required' => 'Please select a delivery staff member.',
            'motorbike_registration.required' => 'Please provide a motorbike registration.',
        ]);

        $formattedReg = strtoupper(trim($validated['motorbike_registration']));

        $activeMotorbikeUsage = Transaction::where('motorbike_registration', $formattedReg)
            ->where('status', 'dispatched')
            ->where('id', '!=', $transaction->id)
            ->first();

        if ($activeMotorbikeUsage) {
            return back()->withErrors([
                'motorbike_registration' => "Motorbike {$formattedReg} is currently active on order #{$activeMotorbikeUsage->id} and cannot be assigned."
            ])->withInput();
        }

        $items = $transaction->items->map(fn ($i) => [
            'product_id' => $i->product_id,
            'quantity' => $i->quantity,
        ])->toArray();

        $shortfalls = $stockService->checkAvailabilityDetailed($transaction->station_id, $items);
        if ($shortfalls) {
            return back()->withErrors(['stock' => 'Insufficient stock at the station to dispatch order.']);
        }

        DB::transaction(function () use ($transaction, $validated, $formattedReg, $items, $stockService) {
            $transaction->update([
                'status' => 'dispatched',
                'delivery_staff_id' => $validated['delivery_staff_id'],
                'motorbike_registration' => $formattedReg,
                'dispatched_at' => now(),
            ]);

            // Sync delivery record status to out_for_delivery
            Delivery::updateOrCreate(
                ['transaction_id' => $transaction->id],
                [
                    'driver_id' => $validated['delivery_staff_id'],
                    'status' => 'out_for_delivery',
                ]
            );

            $stockService->deduct($transaction->station_id, $items);
        });

        $transaction->refresh();
        if ($transaction->customer) {
            $notifier->orderDispatched($transaction->customer, $transaction);
        }

        return redirect()->back()->with('success', 'Order dispatched successfully.');
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

        DB::transaction(function () use ($transaction, $deliveredAt, $timeSpentMinutes, $score) {
            $transaction->update([
                'status' => 'completed',
                'delivered_at' => $deliveredAt,
                'delivery_time_minutes' => $timeSpentMinutes,
                'delivery_score' => $score,
            ]);

            Delivery::where('transaction_id', $transaction->id)->update([
                'status' => 'delivered',
                'delivered_at' => $deliveredAt,
            ]);
        });

        $transaction->refresh();
        if ($transaction->customer) {
            $notifier->orderDelivered($transaction->customer, $transaction);
        }

        return redirect()->back()->with('success', 'Order marked as delivered.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['station', 'customer', 'items.product', 'deliveryStaff']);
        $deliveryStaffList = User::where('role', 'staff')->with('motorbike')->get();

        return view('transactions.show', compact('transaction', 'deliveryStaffList'));
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->status !== 'refunded') {
            return back()->withErrors('Only cancelled/refunded transactions can be permanently deleted. Cancel it first.');
        }

        $transaction->delete();

        return redirect('/transactions')->with('success', 'Transaction deleted.');
    }

    public function cancel(Transaction $transaction, StockService $stockService, NotificationService $notifier)
    {
        if ($transaction->status === 'refunded') {
            return back()->withErrors('This order is already cancelled.');
        }

        DB::transaction(function () use ($transaction, $stockService) {
            $stockLeftStation = in_array($transaction->status, ['dispatched', 'completed']);

            $transaction->update(['status' => 'refunded']);

            Delivery::where('transaction_id', $transaction->id)->update([
                'status' => 'failed',
            ]);

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

        $transaction->refresh();
        if ($transaction->customer) {
            $notifier->orderCancelled($transaction->customer, $transaction->total_amount);
        }

        return redirect("/transactions/{$transaction->id}")->with('success', 'Order cancelled and reversed.');
    }
}