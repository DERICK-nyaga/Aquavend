@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="top-actions">
    <h1>Products</h1>
    <a href="/products/create" class="btn btn-primary">+ New Product</a>
</div>

<div class="card">
    <table>
        <thead><tr><th>Category</th><th>Name</th><th>Size</th><th>Price</th><th>Active</th><th></th></tr></thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td>{{ ucfirst($product->category) }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ rtrim(rtrim($product->size_value, '0'), '.') }}{{ $product->unit }}</td>
                <td>{{ number_format($product->price, 2) }}</td>
                <td>{{ $product->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="/products/{{ $product->id }}/edit" class="btn btn-edit">Edit</a>
                    <form class="inline" method="POST" action="/products/{{ $product->id }}" onsubmit="return confirm('Delete this product?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No products yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $products->links('partials.pagination') }}</div>
@endsection