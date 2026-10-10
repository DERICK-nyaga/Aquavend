<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class StationWebController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    public function index(): View
    {
        $stations = Station::withCount('stocks')
            ->latest()
            ->paginate(10);

        return view('stations.index', compact('stations'));
    }

    public function create(): View
    {
        return view('stations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStation($request);

        Station::create($validated);

        return redirect()->route('stations.index')
            ->with('success', 'Station created successfully.');
    }

    public function show(Station $station): View
    {
        $station->load(['stocks.product']);

        return view('stations.show', compact('station'));
    }

    public function edit(Station $station): View
    {
        return view('stations.edit', compact('station'));
    }

    public function update(Request $request, Station $station): RedirectResponse
    {
        $validated = $this->validateStation($request);

        $station->update($validated);

        return redirect()->route('stations.index')
            ->with('success', 'Station updated successfully.');
    }

    public function destroy(Station $station): RedirectResponse
    {
        $station->delete();

        return redirect()->route('stations.index')
            ->with('success', 'Station deleted successfully.');
    }

    protected function validateStation(Request $request): array
    {
        return $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'location'             => ['nullable', 'string', 'max:255'],
            'status'               => ['required', 'in:active,inactive,maintenance'],
            'tank_capacity_liters' => ['nullable', 'numeric', 'min:0'],
            'current_level_liters' => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold'  => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}