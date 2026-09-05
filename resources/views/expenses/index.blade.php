@extends('layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="top-actions">
    <h1>Expenses</h1>
    <a href="/expenses/create" class="btn btn-primary">+ Record Expense</a>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div style="font-size:0.85rem; color:#64748b;">This Month's Total</div>
    <div style="font-size:1.8rem; font-weight:700; color:#dc2626;">{{ number_format($totalThisMonth, 2) }}</div>
</div>

<div class="card">
    <table>
        <thead><tr><th>Date</th><th>Category</th><th>Station</th><th>Description</th><th>Amount</th><th></th></tr></thead>
        <tbody>
            @forelse ($expenses as $e)
            <tr>
                <td>{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</td>
                <td>{{ ucfirst($e->category) }}</td>
                <td>{{ $e->station->name ?? 'General' }}</td>
                <td>{{ $e->description ?? '—' }}</td>
                <td>{{ number_format($e->amount, 2) }}</td>
                <td>
                    <form class="inline" method="POST" action="/expenses/{{ $e->id }}" onsubmit="return confirm('Delete this expense?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No expenses recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $expenses->links('partials.pagination') }}</div>
@endsection