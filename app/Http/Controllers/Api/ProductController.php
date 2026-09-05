<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        return ProductResource::collection($query->latest()->paginate(15));
    }

    public function forSale(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return ProductResource::collection($query->orderBy('category')->orderBy('size_value')->get());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $product = Product::create($validated);

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validated($request, true);
        $product->update($validated);

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted'], 200);
    }

    private function validated(Request $request, bool $isUpdate = false): array
    {
        $rulePrefix = $isUpdate ? 'sometimes|required' : 'required';

        return $request->validate([
            'name' => "{$rulePrefix}|string|max:255",
            'category' => [$isUpdate ? 'sometimes' : 'required', Rule::in(Product::CATEGORIES)],
            'size_value' => "{$rulePrefix}|numeric|min:0",
            'unit' => [$isUpdate ? 'sometimes' : 'required', Rule::in(Product::UNITS)],
            'price' => "{$rulePrefix}|numeric|min:0",
            'is_active' => 'boolean',
        ]);
    }
}