<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StationResource;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        return StationResource::collection(Station::latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'in:active,inactive,maintenance',
            'tank_capacity_liters' => 'nullable|numeric|min:0',
            'current_level_liters' => 'nullable|numeric|min:0',
        ]);

        $station = Station::create($validated);

        return new StationResource($station);
    }

    public function show(Station $station)
    {
        return new StationResource($station);
    }

    public function update(Request $request, Station $station)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'in:active,inactive,maintenance',
            'tank_capacity_liters' => 'nullable|numeric|min:0',
            'current_level_liters' => 'nullable|numeric|min:0',
        ]);

        $station->update($validated);

        return new StationResource($station);
    }

    public function destroy(Station $station)
    {
        $station->delete();

        return response()->json(['message' => 'Station deleted'], 200);
    }
}