@extends('layouts.app')
@section('title', 'New Supplier')

@section('content')
<h1>New Supplier</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/suppliers">
        @csrf
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>

        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}">

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Category (what they supply)</label>
        <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g. water, oil">

        <label>Notes</label>
        <input type="text" name="notes" value="{{ old('notes') }}">

        <button class="btn btn-primary" type="submit">Add Supplier</button>
        <a href="/suppliers" class="btn">Cancel</a>
    </form>
</div>
@endsection