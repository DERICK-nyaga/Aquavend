<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Station;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionWebController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['station', 'customer', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $stations = Station::all();
        $products = Product::where('is_active', true)->get();
        $customers = Customer::all();

        return view('transactions.create', compact('stations', 'products', 'customers'));
    }

    public function store(Request $request, NotificationService $notifier)
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

        if ($validated['payment_method'] === 'credit' && empty($validated['customer_id'])) {
            return back()->withErrors('A customer is required for credit sales.')->withInput();
        }

        if ($validated['payment_method'] === 'wallet' && empty($validated['customer_id'])) {
            return back()->withErrors('A customer is required for wallet payments.')->withInput();
        }

        try {
            $transaction = DB::transaction(function () use ($validated) {
                $totalAmount = 0;
                $itemsData = [];
                $litersUsed = 0;

                foreach ($validated['product_id'] as $i => $productId) {
                    $product = Product::find($productId);
                    $qty = $validated['quantity'][$i];
                    $subtotal = $product->price * $qty;
                    $totalAmount += $subtotal;

                    $itemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ];

                    // Only liquid units affect tank stock; pcs/dozen/tray (eggs, bottles) don't
                    if ($product->unit === 'l') {
                        $litersUsed += $product->size_value * $qty;
                    } elseif ($product->unit === 'ml') {
                        $litersUsed += ($product->size_value / 1000) * $qty;
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
                        throw new \RuntimeException('Insufficient wallet balance for this customer.');
                    }

                    $customer->decrement('wallet_balance', $totalAmount);
                }

                $transaction = Transaction::create([
                    'station_id' => $validated['station_id'],
                    'customer_id' => $validated['customer_id'] ?? null,
                    'total_amount' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed', // staff-recorded sales are always immediately completed
                ]);

                foreach ($itemsData as $item) {
                    $transaction->items()->create($item);
                }

                $station = Station::find($validated['station_id']);
                if ($station && $station->current_level_liters !== null && $litersUsed > 0) {
                    $station->decrement('current_level_liters', $litersUsed);
                }

                return $transaction;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        $transaction->load(['station', 'customer', 'items.product']);

        if ($transaction->customer) {
            $notifier->orderConfirmation($transaction->customer, $transaction->total_amount);

            if ($transaction->payment_method === 'credit') {
                $notifier->creditReminder($transaction->customer->fresh());
            }
        }

        return redirect('/transactions')->with('success', 'Sale recorded.');
    }

    public function confirmPickup(Transaction $transaction, NotificationService $notifier)
    {
        if ($transaction->status !== 'pending') {
            return back()->withErrors('Only pending orders can be confirmed.');
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

        return redirect("/transactions/{$transaction->id}")->with('success', 'Order confirmed and stock updated.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['station', 'customer', 'items.product']);
        return view('transactions.show', compact('transaction'));
    }

    // Hard delete only allowed for cancelled/refunded transactions — use cancel() otherwise
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status !== 'refunded') {
            return back()->withErrors('Only cancelled/refunded transactions can be permanently deleted. Cancel it first.');
        }

        $transaction->delete();
        return redirect('/transactions')->with('success', 'Transaction deleted.');
    }

    public function cancel(Transaction $transaction)
    {
        if ($transaction->status === 'refunded') {
            return back()->withErrors('This order is already cancelled.');
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

        return redirect("/transactions/{$transaction->id}")->with('success', 'Order cancelled and reversed.');
    }
}