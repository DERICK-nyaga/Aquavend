@extends('layouts.app')
@section('title', 'Staff Dashboard')

@section('content')
<div class="top-actions">
    <h1>Staff Dashboard</h1>
</div>

<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div class="card">
        <p style="color:#64748b;font-size:0.85rem;margin:0 0 0.3rem;">Pending Orders</p>
        <p style="font-size:1.5rem;font-weight:700;margin:0;">{{ $pendingOrders }}</p>
    </div>
</div>

<div class="top-actions" style="margin-bottom:0.75rem;">
    <h2 style="font-size:1.1rem;margin:0;">Quick Links</h2>
</div>
<div style="display:flex;gap:0.6rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    <a href="{{ route('transactions.index') }}" class="btn btn-primary">Transactions</a>
    <a href="{{ route('customers.index') }}" class="btn btn-primary">Customers</a>
    <a href="{{ route('products.index') }}" class="btn btn-primary">Products</a>
    <a href="{{ route('stations.index') }}" class="btn btn-primary">Stations</a>
</div>

<div class="card">
    <h2 style="font-size:1.05rem;margin:0 0 0.75rem;">Recent Transactions</h2>
    <table>
        <thead><tr><th>#</th><th>Station</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse ($recentTransactions as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->station->name ?? '—' }}</td>
                <td>{{ $t->customer->name ?? 'Walk-in' }}</td>
                <td>{{ number_format($t->total_amount, 2) }}</td>
                <td>{{ ucfirst($t->status) }}</td>
                <td>
                    @if ($t->status === 'pending')
                        <form class="inline" method="POST" action="{{ route('transactions.confirm-pickup', $t->id) }}">
                            @csrf
                            <button class="btn btn-edit">Confirm Pickup</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection