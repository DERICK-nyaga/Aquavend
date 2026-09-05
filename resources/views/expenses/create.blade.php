@extends('layouts.app')
@section('title', 'Record Expense')

@section('content')
<h1>Record Expense</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/expenses">
        @csrf
        <label>Category</label>
        <select name="category" required>
            @foreach (\App\Models\Expense::CATEGORIES as $cat)
                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
            @endforeach
        </select>

        <label>Station (optional — leave blank for general/business-wide expense)</label>
        <select name="station_id">
            <option value="">-- General --</option>
            @foreach ($stations as $station)
                <option value="{{ $station->id }}">{{ $station->name }}</option>
            @endforeach
        </select>

        <label>Description</label>
        <input type="text" name="description" value="{{ old('description') }}">

        <label>Amount</label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required>

        <label>Date</label>
        <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>

        <button class="btn btn-primary" type="submit">Record Expense</button>
        <a href="/expenses" class="btn">Cancel</a>
    </form>
</div>
@endsection