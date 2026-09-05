<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Station;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->agentDashboard($user);
    }

    private function adminDashboard()
    {
        $today = now()->startOfDay();

        $todaysSales = Transaction::where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $todaysSalesCount = Transaction::where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->count();

        $stations = Station::all();

        $lowStockStations = $stations->filter(function ($s) {
            return $s->tank_capacity_liters
                && $s->current_level_liters !== null
                && ($s->current_level_liters / $s->tank_capacity_liters) < 0.2;
        });

        $recentTransactions = Transaction::with(['station', 'customer'])
            ->latest()
            ->take(8)
            ->get();

        $newCustomersToday = Customer::where('created_at', '>=', $today)->count();
        $totalCustomers = Customer::count();

        return view('dashboard.admin', compact(
            'todaysSales',
            'todaysSalesCount',
            'stations',
            'lowStockStations',
            'recentTransactions',
            'newCustomersToday',
            'totalCustomers'
        ));
    }

    private function agentDashboard(User $user)
    {
        $station = $user->station;
        $today = now()->startOfDay();

        $todaysSales = 0;
        $todaysSalesCount = 0;
        $recentTransactions = collect();

        if ($station) {
            $todaysSales = Transaction::where('station_id', $station->id)
                ->where('created_at', '>=', $today)
                ->where('status', 'completed')
                ->sum('total_amount');

            $todaysSalesCount = Transaction::where('station_id', $station->id)
                ->where('created_at', '>=', $today)
                ->where('status', 'completed')
                ->count();

            $recentTransactions = Transaction::with('customer')
                ->where('station_id', $station->id)
                ->latest()
                ->take(8)
                ->get();
        }

        return view('dashboard.agent', compact('station', 'todaysSales', 'todaysSalesCount', 'recentTransactions'));
    }
}