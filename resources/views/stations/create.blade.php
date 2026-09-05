@extends('layouts.app')
@section('title', 'New Station')

@section('content')
<h1>New Station</h1>

<div class="card">
    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/stations">
        @csrf
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>

        <label>Location</label>
        <input type="text" name="location" value="{{ old('location') }}">

        <label>Status</label>
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="maintenance">Maintenance</option>
        </select>

        <label>Tank Capacity (Liters)</label>
        <input type="number" step="0.01" name="tank_capacity_liters" value="{{ old('tank_capacity_liters') }}">

        <label>Current Level (Liters)</label>
        <input type="number" step="0.01" name="current_level_liters" value="{{ old('current_level_liters') }}">

        <button class="btn btn-primary" type="submit">Create Station</button>
        <a href="/stations" class="btn">Cancel</a>
    </form>
</div>
@endsection