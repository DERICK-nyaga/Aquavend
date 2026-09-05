@extends('layouts.app')
@section('title', 'Transaction #' . $transaction->id)

@section('content')
<h1>Transaction #{{ $transaction->id }}</h1>

@if ($transaction->status === 'pending')
<div class="card" style="background:#fef9c3; border:1px solid #fde68a; margin-bottom:1.5rem;">
    <p style="margin-bottom:0.75rem;">This order is awaiting cash pickup confirmation.</p>
    <form method="POST" action="{{ route('transactions.confirm-pickup', $transaction) }}">
        @csrf
        <button class="btn btn-primary" type="submit">Confirm Pickup & Deduct Stock</button>
    </form>
</div>
@endif

@if (in_array($transaction->status, ['pending', 'completed']))
<div class="card" style="background:#fee2e2; border:1px solid #fecaca; margin-bottom:1.5rem;">
    <p style="margin-bottom:0.75rem;">Need to cancel this order? This will reverse any wallet/credit charges and restock if already completed.</p>
    <form method="POST" action="{{ route('transactions.cancel', $transaction) }}" onsubmit="return confirm('Cancel this order? This cannot be undone.')">
        @csrf
        <button class="btn btn-delete" type="submit">Cancel & Refund Order</button>
    </form>
</div>
@endif

@if ($transaction->status === 'refunded')
<div class="card" style="background:#f1f5f9; border:1px solid #cbd5e1; margin-bottom:1.5rem;">
    <p style="color:#64748b;">This order was cancelled/refunded.</p>
</div>
@endif

<div class="card" style="margin-bottom:1.5rem;">
    <p><strong>Station:</strong> {{ $transaction->station->name ?? '—' }}</p>
    <p><strong>Customer:</strong> {{ $transaction->customer->name ?? 'Walk-in' }}</p>
    <p><strong>Payment:</strong> {{ ucfirst($transaction->payment_method) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
    <p><strong>Total:</strong> {{ number_format($transaction->total_amount, 2) }}</p>
</div>

<div class="card">
    <table>
        <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
        <tbody>
            @foreach ($transaction->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<a href="/transactions" class="btn" style="margin-top:1rem;">Back to Transactions</a>
@endsection