@extends('layouts.app')
@section('title', 'Edit Customer')

@section('content')
<h1>Edit Customer</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/customers/{{ $customer->id }}">
        @csrf @method('PUT')
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required>

        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $customer->email) }}">

        <label>Address</label>
        <input type="text" name="address" value="{{ old('address', $customer->address) }}">

        <button class="btn btn-primary" type="submit">Update Customer</button>
        <a href="/customers" class="btn">Cancel</a>
    </form>

    <div class="card" style="margin-top:1.5rem;">
    <h2 style="font-size:1rem; margin-bottom:1rem;">Wallet Balance: {{ number_format($customer->wallet_balance, 2) }}</h2>
    <form method="POST" action="{{ route('customers.wallet', $customer) }}" style="display:flex; gap:0.5rem; align-items:end;">
        @csrf
        <div style="flex:1">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" required>
        </div>
        <div style="flex:1">
            <label>Action</label>
            <select name="action">
                <option value="add">Add (top-up)</option>
                <option value="deduct">Deduct</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit" style="height:fit-content;">Apply</button>
    </form>
</div>

<div class="card" style="margin-top:1.5rem;">
    <h2 style="font-size:1rem; margin-bottom:1rem;">
        Credit: {{ number_format($customer->credit_balance, 2) }} / {{ number_format($customer->credit_limit, 2) }} limit
    </h2>
    <form method="POST" action="{{ route('customers.credit-limit', $customer) }}" style="display:flex; gap:0.5rem; align-items:end;">
        @csrf
        <div style="flex:1">
            <label>New Credit Limit</label>
            <input type="number" step="0.01" name="credit_limit" value="{{ $customer->credit_limit }}" required>
        </div>
        <button class="btn btn-primary" type="submit" style="height:fit-content;">Update Limit</button>
    </form>
</div>
</div>
@endsection