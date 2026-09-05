@extends('layouts.app')
@section('title', 'Transactions')

@section('content')
<div class="top-actions">
    <h1>Transactions</h1>
    <a href="/transactions/create" class="btn btn-primary">+ Record Sale</a>
</div>

<div class="card">
    <table>
        <thead><tr><th>#</th><th>Station</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse ($transactions as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td>{{ $t->station->name ?? '—' }}</td>
                <td>{{ $t->customer->name ?? 'Walk-in' }}</td>
                <td>{{ number_format($t->total_amount, 2) }}</td>
                <td>{{ ucfirst($t->payment_method) }}</td>
                <td>{{ ucfirst($t->status) }}</td>
                <td>
                    <a href="/transactions/{{ $t->id }}" class="btn btn-edit">View</a>
                    <form class="inline" method="POST" action="/transactions/{{ $t->id }}" onsubmit="return confirm('Delete this transaction?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $transactions->links('partials.pagination') }}</div>
@endsection