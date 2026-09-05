@extends('layouts.app')
@section('title', 'Stock Transfers')

@section('content')
<div class="top-actions">
    <h1>Stock Transfers</h1>
    <a href="/stock-transfers/create" class="btn btn-primary">+ New Transfer</a>
</div>

<div class="card">
    <table>
        <thead><tr><th>From</th><th>To</th><th>Liters</th><th>Initiated By</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse ($transfers as $t)
            <tr>
                <td>{{ $t->fromStation->name ?? '—' }}</td>
                <td>{{ $t->toStation->name ?? '—' }}</td>
                <td>{{ $t->liters_transferred }}L</td>
                <td>{{ $t->initiatedBy->name ?? '—' }}</td>
                <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                <td>
                    <form class="inline" method="POST" action="/stock-transfers/{{ $t->id }}" onsubmit="return confirm('Delete and reverse this transfer?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Undo</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No transfers recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $transfers->links('partials.pagination') }}</div>
@endsection