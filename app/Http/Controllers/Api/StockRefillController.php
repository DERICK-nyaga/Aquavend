<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockRefillResource;
use App\Models\Station;
use App\Models\StockRefill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockRefillController extends Controller
{
    public function index()
    {
        return StockRefillResource::collection(StockRefill::latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'liters_added' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $refill = DB::transaction(function () use ($validated) {
            $refill = StockRefill::create($validated);

            $station = Station::find($validated['station_id']);
            $station->increment('current_level_liters', $validated['liters_added']);

            return $refill;
        });

        return new StockRefillResource($refill);
    }

    public function show(StockRefill $stockRefill)
    {
        return new StockRefillResource($stockRefill);
    }

    public function destroy(StockRefill $stockRefill)
    {
        $stockRefill->delete();

        return response()->json(['message' => 'Refill record deleted'], 200);
    }
}