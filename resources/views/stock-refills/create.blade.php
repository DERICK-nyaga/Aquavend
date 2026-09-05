@extends('layouts.app')
@section('title', 'Record Refill')

@section('content')
<h1>Record Stock Refill</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/stock-refills">
        @csrf
        <label>Station</label>
        <select name="station_id" required>
            @foreach ($stations as $station)
                <option value="{{ $station->id }}">{{ $station->name }}</option>
            @endforeach
        </select>

        <label>Liters Added</label>
        <input type="number" step="0.01" name="liters_added" required>

        <label>Supplier</label>
        <input type="text" name="supplier">

        <label>Cost</label>
        <input type="number" step="0.01" name="cost">

        <button class="btn btn-primary" type="submit">Record Refill</button>
        <a href="/stock-refills" class="btn">Cancel</a>
    </form>
</div>
@endsection