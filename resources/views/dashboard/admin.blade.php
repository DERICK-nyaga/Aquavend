@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h1>Welcome back, {{ auth()->user()->name }} 👋</h1>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin:1.5rem 0;">
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Today's Revenue</div>
        <div style="font-size:1.8rem; font-weight:700; color:#0369a1;">{{ number_format($todaysSales, 2) }}</div>
        <div style="font-size:0.8rem; color:#64748b;">{{ $todaysSalesCount }} sales today</div>
    </div>
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Total Customers</div>
        <div style="font-size:1.8rem; font-weight:700; color:#0369a1;">{{ $totalCustomers }}</div>
        <div style="font-size:0.8rem; color:#64748b;">+{{ $newCustomersToday }} today</div>
    </div>
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Active Stations</div>
        <div style="font-size:1.8rem; font-weight:700; color:#0369a1;">{{ $stations->where('status', 'active')->count() }}</div>
        <div style="font-size:0.8rem; color:#64748b;">of {{ $stations->count() }} total</div>
    </div>
    <div class="card" style="{{ $lowStockStations->count() ? 'border:2px solid #fca5a5;' : '' }}">
        <div style="font-size:0.85rem; color:#64748b;">Low Stock Alerts</div>
        <div style="font-size:1.8rem; font-weight:700; color:{{ $lowStockStations->count() ? '#dc2626' : '#0369a1' }};">{{ $lowStockStations->count() }}</div>
        <div style="font-size:0.8rem; color:#64748b;">stations below 20%</div>
    </div>
</div>

@if ($lowStockStations->count())
<div class="card" style="background:#fef2f2; border:1px solid #fecaca; margin-bottom:1.5rem;">
    <h2 style="font-size:1rem; color:#991b1b; margin-bottom:0.75rem;">⚠️ Stations needing refill</h2>
    @foreach ($lowStockStations as $s)
        <div style="padding:0.4rem 0; font-size:0.9rem;">
            {{ $s->name }} — {{ $s->current_level_liters }}L / {{ $s->tank_capacity_liters }}L
            <a href="/stock-refills/create" style="color:#0369a1; margin-left:0.5rem;">Refill →</a>
        </div>
    @endforeach
</div>
@endif

<div class="card">
    <h2 style="font-size:1.1rem; margin-bottom:1rem;">Recent Transactions</h2>
    <table>
        <thead><tr><th>Station</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Time</th></tr></thead>
        <tbody>
            @forelse ($recentTransactions as $t)
            <tr>
                <td>{{ $t->station->name ?? '—' }}</td>
                <td>{{ $t->customer->name ?? 'Walk-in' }}</td>
                <td>{{ number_format($t->total_amount, 2) }}</td>
                <td>{{ ucfirst($t->payment_method) }}</td>
                <td>{{ $t->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="5">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <a href="/transactions" style="display:inline-block; margin-top:1rem; color:#0369a1; font-size:0.9rem;">View all transactions →</a>
</div>
@endsection