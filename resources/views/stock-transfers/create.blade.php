@extends('layouts.app')
@section('title', 'New Stock Transfer')

@section('content')
<h1>Transfer Stock Between Stations</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif

    <form method="POST" action="/stock-transfers">
        @csrf
        <label>From Station</label>
        <select name="from_station_id" required>
            <option value="">-- Select --</option>
            @foreach ($stations as $station)
                <option value="{{ $station->id }}">{{ $station->name }} ({{ $station->current_level_liters ?? 0 }}L available)</option>
            @endforeach
        </select>

        <label>To Station</label>
        <select name="to_station_id" required>
            <option value="">-- Select --</option>
            @foreach ($stations as $station)
                <option value="{{ $station->id }}">{{ $station->name }}</option>
            @endforeach
        </select>

        <label>Liters to Transfer</label>
        <input type="number" step="0.01" name="liters_transferred" value="{{ old('liters_transferred') }}" required>

        <label>Notes</label>
        <input type="text" name="notes" value="{{ old('notes') }}">

        <button class="btn btn-primary" type="submit">Transfer Stock</button>
        <a href="/stock-transfers" class="btn">Cancel</a>
    </form>
</div>
@endsection