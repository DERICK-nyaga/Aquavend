@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h1>Welcome, {{ auth()->user()->name }} 👋</h1>

@if (!$station)
<div class="card" style="background:#fef9c3; border:1px solid #fde68a;">
    You're not assigned to a station yet. Contact your admin.
</div>
@else
<div class="card" style="margin-bottom:1.5rem;">
    <div style="font-size:0.85rem; color:#64748b;">Your Station</div>
    <div style="font-size:1.3rem; font-weight:700; color:#0369a1;">{{ $station->name }}</div>
    <span class="badge badge-{{ $station->status }}">{{ ucfirst($station->status) }}</span>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Today's Sales</div>
        <div style="font-size:1.8rem; font-weight:700; color:#0369a1;">{{ number_format($todaysSales, 2) }}</div>
        <div style="font-size:0.8rem; color:#64748b;">{{ $todaysSalesCount }} transactions</div>
    </div>
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Current Stock</div>
        <div style="font-size:1.8rem; font-weight:700; color:#0369a1;">{{ $station->current_level_liters ?? '—' }}L</div>
        <div style="font-size:0.8rem; color:#64748b;">of {{ $station->tank_capacity_liters ?? '—' }}L capacity</div>
    </div>
</div>

<a href="/transactions/create" class="btn btn-primary" style="margin-bottom:1.5rem; display:inline-block;">+ Record New Sale</a>

<div class="card">
    <h2 style="font-size:1.1rem; margin-bottom:1rem;">Your Recent Sales</h2>
    <table>
        <thead><tr><th>Customer</th><th>Amount</th><th>Payment</th><th>Time</th></tr></thead>
        <tbody>
            @forelse ($recentTransactions as $t)
            <tr>
                <td>{{ $t->customer->name ?? 'Walk-in' }}</td>
                <td>{{ number_format($t->total_amount, 2) }}</td>
                <td>{{ ucfirst($t->payment_method) }}</td>
                <td>{{ $t->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="4">No sales recorded yet today.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
@endsection