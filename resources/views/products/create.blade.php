@extends('layouts.app')
@section('title', 'New Product')

@section('content')
<h1>New Product</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/products">
        @csrf
        <label>Category</label>
        <select name="category" id="category" required onchange="updateUnits()">
            <option value="">-- Select category --</option>
            <option value="water">Water</option>
            <option value="oil">Cooking Oil</option>
            <option value="milk">Milk</option>
            <option value="yoghurt">Yoghurt</option>
            <option value="eggs">Eggs</option>
            <option value="bottles">Bottles</option>
        </select>

        <label>Product Name</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Fresh Milk" required>

        <div style="display:flex; gap:0.5rem;">
            <div style="flex:1">
                <label>Size</label>
                <input type="number" step="0.01" name="size_value" value="{{ old('size_value') }}" placeholder="e.g. 500, 1, 5, 20" required>
            </div>
            <div style="flex:1">
                <label>Unit</label>
                <select name="unit" id="unit" required>
                    <option value="">-- Select unit --</option>
                </select>
            </div>
        </div>

        <label>Price</label>
        <input type="number" step="0.01" name="price" value="{{ old('price') }}" required>

        <label><input type="checkbox" name="is_active" checked style="width:auto;display:inline-block;margin-right:0.5rem;"> Active</label>

        <button class="btn btn-primary" type="submit">Create Product</button>
        <a href="/products" class="btn">Cancel</a>
    </form>
</div>

<script>
const unitsByCategory = {
    water: ['ml', 'l'],
    oil: ['ml', 'l'],
    milk: ['ml', 'l'],
    yoghurt: ['ml', 'l'],
    bottles: ['ml', 'l', 'pcs'],
    eggs: ['pcs', 'dozen', 'tray'],
};

const unitLabels = { ml: 'ml', l: 'Liters (L)', pcs: 'Pieces', dozen: 'Dozen', tray: 'Tray (30pcs)' };

function updateUnits() {
    const category = document.getElementById('category').value;
    const unitSelect = document.getElementById('unit');
    unitSelect.innerHTML = '<option value="">-- Select unit --</option>';

    (unitsByCategory[category] || []).forEach(u => {
        const opt = document.createElement('option');
        opt.value = u;
        opt.textContent = unitLabels[u];
        unitSelect.appendChild(opt);
    });
}
</script>
@endsection