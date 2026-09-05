@extends('layouts.app')
@section('title', 'Record Sale')

@section('content')
<h1>Record Sale</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/transactions">
        @csrf
        <label>Station</label>
        <select name="station_id" required>
            @foreach ($stations as $station)
                <option value="{{ $station->id }}">{{ $station->name }}</option>
            @endforeach
        </select>

        <label>Customer (optional)</label>
        <select name="customer_id">
            <option value="">Walk-in</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>

        <label>Payment Method</label>
        <select name="payment_method" required>
            <option value="cash">Cash</option>
            <option value="mpesa">Mpesa</option>
            <option value="wallet">Wallet</option>
            <option value="card">Card</option>
            <option value="credit">Credit</option>
        </select>

        <div id="items">
            <div style="display:flex; gap:0.5rem;">
                <div style="flex:2">
                    <label>Product</label>
                    <select name="product_id[]" required>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} — {{ number_format($product->price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:1">
                    <label>Qty</label>
                    <input type="number" name="quantity[]" min="1" value="1" required>
                </div>
            </div>
        </div>

        <button type="button" class="btn" onclick="addItem()" style="margin-bottom:1rem;">+ Add another item</button>
        <br>
        <button class="btn btn-primary" type="submit">Complete Sale</button>
        <a href="/transactions" class="btn">Cancel</a>
    </form>
</div>

<script>
function addItem() {
    const container = document.getElementById('items');
    const row = container.children[0].cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = 1);
    container.appendChild(row);
}
</script>
@endsection