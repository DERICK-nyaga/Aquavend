<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockRefillResource;
use App\Models\Station;
use App\Models\StockRefill;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockRefillController extends Controller
{
    public function index()
    {
        return StockRefillResource::collection(StockRefill::latest()->paginate(15));
    }

    public function store(Request $request, NotificationService $notifier)
    {
        $validated = $request->validate([
            'station_id'   => 'required|exists:stations,id',
            'liters_added' => 'required|numeric|min:0.01',
            'supplier'     => 'nullable|string|max:255',
            'cost'         => 'nullable|numeric|min:0',
        ]);

        $station = Station::findOrFail($validated['station_id']);

        if (! $station->canFit($validated['liters_added'])) {
            throw ValidationException::withMessages([
                'liters_added' => "Exceeds tank capacity! Available: "
                    . $station->availableLiters() . "L ({$station->capacitySummary()}).",
            ]);
        }

        [$refill, $station] = DB::transaction(function () use ($validated, $station) {
            $refill = StockRefill::create($validated);
            $station->increment('current_level_liters', $validated['liters_added']);
            return [$refill, $station->fresh()];
        });

        $notifier->notifyStationRestock($station);

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