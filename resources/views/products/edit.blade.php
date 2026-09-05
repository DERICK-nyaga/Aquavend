@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
<h1>Edit Product</h1>
<div class="card">
    @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="/products/{{ $product->id }}">
        @csrf @method('PUT')

        <label>Category</label>
        <select name="category" id="category" required onchange="updateUnits('{{ $product->unit }}')">
            @foreach (\App\Models\Product::CATEGORIES as $cat)
                <option value="{{ $cat }}" {{ $product->category === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>

        <label>Product Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" required>

        <div style="display:flex; gap:0.5rem;">
            <div style="flex:1">
                <label>Size</label>
                <input type="number" step="0.01" name="size_value" value="{{ old('size_value', $product->size_value) }}" required>
            </div>
            <div style="flex:1">
                <label>Unit</label>
                <select name="unit" id="unit" required></select>
            </div>
        </div>

        <label>Price</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required>

        <label><input type="checkbox" name="is_active" {{ $product->is_active ? 'checked' : '' }} style="width:auto;display:inline-block;margin-right:0.5rem;"> Active</label>

        <button class="btn btn-primary" type="submit">Update Product</button>
        <a href="/products" class="btn">Cancel</a>
    </form>
</div>

<script>
const unitsByCategory = {
    water: ['ml', 'l'], oil: ['ml', 'l'], milk: ['ml', 'l'], yoghurt: ['ml', 'l'],
    bottles: ['ml', 'l', 'pcs'], eggs: ['pcs', 'dozen', 'tray'],
};
const unitLabels = { ml: 'ml', l: 'Liters (L)', pcs: 'Pieces', dozen: 'Dozen', tray: 'Tray (30pcs)' };

function updateUnits(selected = null) {
    const category = document.getElementById('category').value;
    const unitSelect = document.getElementById('unit');
    unitSelect.innerHTML = '';
    (unitsByCategory[category] || []).forEach(u => {
        const opt = document.createElement('option');
        opt.value = u;
        opt.textContent = unitLabels[u];
        if (u === selected) opt.selected = true;
        unitSelect.appendChild(opt);
    });
}

updateUnits('{{ $product->unit }}');
</script>
@endsection