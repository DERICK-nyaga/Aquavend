@extends('layouts.app')
@section('title', 'New Customer')

@section('content')
<h1>New Customer</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/customers">
        @csrf
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>

        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}" required>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Address</label>
        <input type="text" name="address" value="{{ old('address') }}">

        <button class="btn btn-primary" type="submit">Create Customer</button>
        <a href="/customers" class="btn">Cancel</a>
    </form>
</div>
@endsection