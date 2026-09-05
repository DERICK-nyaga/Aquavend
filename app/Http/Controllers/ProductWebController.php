<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductWebController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('category')->orderBy('size_value')->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect('/products')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validated($request);
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect('/products')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect('/products')->with('success', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', Rule::in(Product::CATEGORIES)],
            'size_value' => 'required|numeric|min:0',
            'unit' => ['required', Rule::in(Product::UNITS)],
            'price' => 'required|numeric|min:0',
        ]);
    }
}