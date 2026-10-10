<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StockRefill;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockRefillWebController extends Controller
{
    public function index()
    {
        $refills = StockRefill::with('station')->latest()->paginate(10);
        return view('stock-refills.index', compact('refills'));
    }

    public function create()
    {
        $stations = Station::all();
        return view('stock-refills.create', compact('stations'));
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
            return back()
                ->withInput()
                ->withErrors([
                    'liters_added' => "Exceeds tank capacity! Station can only hold "
                        . $station->availableLiters() . "L more. "
                        . "({$station->capacitySummary()})",
                ]);
        }

        DB::transaction(function () use ($validated, $station) {
            StockRefill::create($validated);
            $station->increment('current_level_liters', $validated['liters_added']);
        });

        $station = $station->fresh();
        $notifier->notifyStationRestock($station);

        if ($station->isFull()) {
            return redirect('/stock-refills')
                ->with('success', 'Refill recorded.')
                ->with('warning', "Tank is now FULL: {$station->current_level_liters}L / {$station->tank_capacity_liters}L — no remaining capacity.");
        }

        return redirect('/stock-refills')->with(
            'success',
            "Refill recorded. Tank level: {$station->current_level_liters}L / {$station->tank_capacity_liters}L "
            . "(Remaining: {$station->availableLiters()}L)."
        );
    }

    public function destroy(StockRefill $stockRefill)
    {
        $stockRefill->delete();
        return redirect('/stock-refills')->with('success', 'Refill record deleted.');
    }
}