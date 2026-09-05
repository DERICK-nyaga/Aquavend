<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferWebController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['fromStation', 'toStation', 'initiatedBy'])
            ->latest()
            ->paginate(15);

        return view('stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $stations = Station::all();
        return view('stock-transfers.create', compact('stations'));
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
            DB::transaction(function () use ($validated) {
                $fromStation = Station::findOrFail($validated['from_station_id']);
                $toStation = Station::findOrFail($validated['to_station_id']);

                if ($fromStation->current_level_liters === null || $fromStation->current_level_liters < $validated['liters_transferred']) {
                    abort(422, "Insufficient stock at {$fromStation->name}. Available: " . ($fromStation->current_level_liters ?? 0) . 'L');
                }

                $fromStation->decrement('current_level_liters', $validated['liters_transferred']);
                $toStation->increment('current_level_liters', $validated['liters_transferred']);

                StockTransfer::create([
                    'from_station_id' => $validated['from_station_id'],
                    'to_station_id' => $validated['to_station_id'],
                    'liters_transferred' => $validated['liters_transferred'],
                    'status' => 'completed',
                    'initiated_by' => Auth::id(),
                    'notes' => $validated['notes'] ?? null,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        return redirect('/stock-transfers')->with('success', 'Stock transferred successfully.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
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

        return redirect('/stock-transfers')->with('success', 'Transfer deleted and stock reversed.');
    }
}