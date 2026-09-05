<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpenseWebController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('station')->latest('expense_date')->paginate(15);
        $totalThisMonth = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'totalThisMonth'));
    }

    public function create()
    {
        $stations = Station::all();
        return view('expenses.create', compact('stations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_id' => 'nullable|exists:stations,id',
            'category' => ['required', Rule::in(Expense::CATEGORIES)],
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $validated['recorded_by'] = Auth::id();

        Expense::create($validated);

        return redirect('/expenses')->with('success', 'Expense recorded.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect('/expenses')->with('success', 'Expense deleted.');
    }
}