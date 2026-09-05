@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="top-actions">
    <h1>Suppliers</h1>
    <a href="/suppliers/create" class="btn btn-primary">+ New Supplier</a>
</div>

<div class="card">
    <table>
        <thead><tr><th>Name</th><th>Phone</th><th>Category</th><th>Outstanding</th><th></th></tr></thead>
        <tbody>
            @forelse ($suppliers as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->phone ?? '—' }}</td>
                <td>{{ $s->category ?? '—' }}</td>
                <td style="{{ $s->outstanding_balance > 0 ? 'color:#dc2626;font-weight:600;' : '' }}">
                    {{ number_format($s->outstanding_balance, 2) }}
                </td>
                <td>
                    <a href="/suppliers/{{ $s->id }}/edit" class="btn btn-edit">Edit</a>
                    <form class="inline" method="POST" action="/suppliers/{{ $s->id }}" onsubmit="return confirm('Delete this supplier?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5">No suppliers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $suppliers->links('partials.pagination') }}</div>
@endsection