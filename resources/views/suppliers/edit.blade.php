@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<h1>Edit Supplier</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/suppliers/{{ $supplier->id }}">
        @csrf @method('PUT')
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required>

        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}">

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email) }}">

        <label>Category</label>
        <input type="text" name="category" value="{{ old('category', $supplier->category) }}">

        <label>Notes</label>
        <input type="text" name="notes" value="{{ old('notes', $supplier->notes) }}">

        <button class="btn btn-primary" type="submit">Update Supplier</button>
        <a href="/suppliers" class="btn">Cancel</a>
    </form>
</div>
@endsection