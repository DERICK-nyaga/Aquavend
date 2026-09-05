@extends('layouts.app')
@section('title', 'Customers')

@section('content')
<div class="top-actions">
    <h1>Customers</h1>
    <a href="/customers/create" class="btn btn-primary">+ New Customer</a>
</div>

<div class="card">
    <table>
        <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Wallet</th><th></th></tr></thead>
        <tbody>
            @forelse ($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email ?? '—' }}</td>
                <td>{{ number_format($customer->wallet_balance, 2) }}</td>
                <td>
                    <a href="/customers/{{ $customer->id }}/edit" class="btn btn-edit">Edit</a>
                    <form class="inline" method="POST" action="/customers/{{ $customer->id }}" onsubmit="return confirm('Delete this customer?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5">No customers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $customers->links('partials.pagination') }}</div>
@endsection