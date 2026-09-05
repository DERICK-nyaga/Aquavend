@extends('layouts.app')
@section('title', 'Stock Refills')

@section('content')
<div class="top-actions">
    <h1>Stock Refills</h1>
    <a href="/stock-refills/create" class="btn btn-primary">+ Record Refill</a>
</div>

<div class="card">
    <table>
        <thead><tr><th>Station</th><th>Liters Added</th><th>Supplier</th><th>Cost</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse ($refills as $refill)
            <tr>
                <td>{{ $refill->station->name ?? '—' }}</td>
                <td>{{ $refill->liters_added }}</td>
                <td>{{ $refill->supplier ?? '—' }}</td>
                <td>{{ $refill->cost ? number_format($refill->cost, 2) : '—' }}</td>
                <td>{{ $refill->created_at->format('d M Y') }}</td>
                <td>
                    <form class="inline" method="POST" action="/stock-refills/{{ $refill->id }}" onsubmit="return confirm('Delete this refill record?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No refills recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $refills->links('partials.pagination') }}</div>
@endsection