<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockTransferResource;
use App\Models\Station;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromStation', 'toStation', 'initiatedBy']);

        if ($request->filled('station_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_station_id', $request->station_id)
                  ->orWhere('to_station_id', $request->station_id);
            });
        }

        return StockTransferResource::collection($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_station_id' => 'required|exists:stations,id|different:to_station_id',
            'to_station_id' => 'required|exists:stations,id',
            'liters_transferred' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        try {
            $transfer = DB::transaction(function () use ($validated, $request) {
                $fromStation = Station::findOrFail($validated['from_station_id']);
                $toStation = Station::findOrFail($validated['to_station_id']);

                if ($fromStation->current_level_liters === null || $fromStation->current_level_liters < $validated['liters_transferred']) {
                    abort(422, "Insufficient stock at {$fromStation->name}. Available: " . ($fromStation->current_level_liters ?? 0) . 'L');
                }

                $fromStation->decrement('current_level_liters', $validated['liters_transferred']);
                $toStation->increment('current_level_liters', $validated['liters_transferred']);

                return StockTransfer::create([
                    'from_station_id' => $validated['from_station_id'],
                    'to_station_id' => $validated['to_station_id'],
                    'liters_transferred' => $validated['liters_transferred'],
                    'status' => 'completed',
                    'initiated_by' => $request->user()->id,
                    'notes' => $validated['notes'] ?? null,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new StockTransferResource($transfer->load(['fromStation', 'toStation', 'initiatedBy']));
    }

    public function show(StockTransfer $stockTransfer)
    {
        return new StockTransferResource($stockTransfer->load(['fromStation', 'toStation', 'initiatedBy']));
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        // Reverse the stock movement if this transfer is being deleted/undone
        DB::transaction(function () use ($stockTransfer) {
            $fromStation = Station::find($stockTransfer->from_station_id);
            $toStation = Station::find($stockTransfer->to_station_id);

            if ($fromStation) {
                $fromStation->increment('current_level_liters', $stockTransfer->liters_transferred);
            }
            if ($toStation) {
                $toStation->decrement('current_level_liters', $stockTransfer->liters_transferred);
            }

            $stockTransfer->delete();
        });

        return response()->json(['message' => 'Transfer deleted and stock reversed'], 200);
    }
}