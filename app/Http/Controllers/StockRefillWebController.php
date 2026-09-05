<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StockRefill;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'liters_added' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            StockRefill::create($validated);

            $station = Station::find($validated['station_id']);
            $station->increment('current_level_liters', $validated['liters_added']);
        });

        return redirect('/stock-refills')->with('success', 'Refill recorded.');
    }

    public function destroy(StockRefill $stockRefill)
    {
        $stockRefill->delete();
        return redirect('/stock-refills')->with('success', 'Refill record deleted.');
    }
}