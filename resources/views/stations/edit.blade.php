@extends('layouts.app')
@section('title', 'Edit Station')

@section('content')
<h1>Edit Station</h1>

<div class="card">
    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/stations/{{ $station->id }}">
        @csrf @method('PUT')
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $station->name) }}" required>

        <label>Location</label>
        <input type="text" name="location" value="{{ old('location', $station->location) }}">

        <label>Status</label>
        <select name="status">
            @foreach (['active', 'inactive', 'maintenance'] as $status)
                <option value="{{ $status }}" {{ $station->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
            @endforeach
        </select>

        <label>Tank Capacity (Liters)</label>
        <input type="number" step="0.01" name="tank_capacity_liters" value="{{ old('tank_capacity_liters', $station->tank_capacity_liters) }}">

        <label>Current Level (Liters)</label>
        <input type="number" step="0.01" name="current_level_liters" value="{{ old('current_level_liters', $station->current_level_liters) }}">

        <button class="btn btn-primary" type="submit">Update Station</button>
        <a href="/stations" class="btn">Cancel</a>
    </form>
</div>
@endsection