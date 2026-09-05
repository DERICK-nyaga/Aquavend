@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<h1>Business Reports</h1>

<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="/reports" style="display:flex; gap:1rem; align-items:end;">
        <div>
            <label>From</label>
            <input type="date" name="from" value="{{ $from }}">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="to" value="{{ $to }}">
        </div>
        <button class="btn btn-primary" type="submit" style="height:fit-content;">Filter</button>
    </form>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Revenue</div>
        <div style="font-size:1.6rem; font-weight:700; color:#0369a1;">{{ number_format($revenue, 2) }}</div>
    </div>
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Expenses</div>
        <div style="font-size:1.6rem; font-weight:700; color:#dc2626;">{{ number_format($expenses, 2) }}</div>
    </div>
    <div class="card">
        <div style="font-size:0.85rem; color:#64748b;">Net Profit</div>
        <div style="font-size:1.6rem; font-weight:700; color:{{ $profit >= 0 ? '#16a34a' : '#dc2626' }};">{{ number_format($profit, 2) }}</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
    <div class="card">
        <h2 style="font-size:1.05rem; margin-bottom:1rem;">Sales by Station</h2>
        <table>
            <thead><tr><th>Station</th><th>Revenue</th></tr></thead>
            <tbody>
                @forelse ($salesByStation as $row)
                <tr><td>{{ $row->station->name ?? '—' }}</td><td>{{ number_format($row->total, 2) }}</td></tr>
                @empty
                <tr><td colspan="2">No sales in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 style="font-size:1.05rem; margin-bottom:1rem;">Expenses by Category</h2>
        <table>
            <thead><tr><th>Category</th><th>Amount</th></tr></thead>
            <tbody>
                @forelse ($expensesByCategory as $row)
                <tr><td>{{ ucfirst($row->category) }}</td><td>{{ number_format($row->total, 2) }}</td></tr>
                @empty
                <tr><td colspan="2">No expenses in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <h2 style="font-size:1.05rem; margin-bottom:1rem;">Top Products</h2>
    <table>
        <thead><tr><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr></thead>
        <tbody>
            @forelse ($topProducts as $row)
            <tr>
                <td>{{ $row->product->name ?? '—' }} ({{ $row->product->size_value ?? '' }}{{ $row->product->unit ?? '' }})</td>
                <td>{{ $row->total_qty }}</td>
                <td>{{ number_format($row->total_revenue, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">No sales in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection