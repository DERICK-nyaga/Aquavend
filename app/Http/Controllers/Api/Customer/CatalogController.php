<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StationResource;
use App\Models\Product;
use App\Models\Station;

class CatalogController extends Controller
{
    public function products()
    {
        return ProductResource::collection(
            Product::where('is_active', true)->orderBy('category')->orderBy('size_value')->get()
        );
    }

    public function stations()
    {
        return StationResource::collection(
            Station::where('status', 'active')->get()
        );
    }
}