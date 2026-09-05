<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class StationWebController extends Controller
{
    public function index()
    {
        $stations = Station::latest()->paginate(10);
        return view('stations.index', compact('stations'));
    }

    public function create()
    {
        return view('stations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'tank_capacity_liters' => 'nullable|numeric|min:0',
            'current_level_liters' => 'nullable|numeric|min:0',
        ]);

        Station::create($validated);

        return redirect('/stations')->with('success', 'Station created.');
    }

    public function edit(Station $station)
    {
        return view('stations.edit', compact('station'));
    }

    public function update(Request $request, Station $station)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'tank_capacity_liters' => 'nullable|numeric|min:0',
            'current_level_liters' => 'nullable|numeric|min:0',
        ]);

        $station->update($validated);

        return redirect('/stations')->with('success', 'Station updated.');
    }

    public function destroy(Station $station)
    {
        $station->delete();
        return redirect('/stations')->with('success', 'Station deleted.');
    }
}