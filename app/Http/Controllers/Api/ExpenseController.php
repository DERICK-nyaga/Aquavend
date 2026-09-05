<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['station', 'recordedBy']);

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return ExpenseResource::collection($query->latest('expense_date')->paginate(15));
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

        $validated['recorded_by'] = $request->user()->id;

        return new ExpenseResource(Expense::create($validated));
    }

    public function show(Expense $expense)
    {
        return new ExpenseResource($expense->load(['station', 'recordedBy']));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'station_id' => 'nullable|exists:stations,id',
            'category' => [Rule::in(Expense::CATEGORIES)],
            'description' => 'nullable|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'expense_date' => 'sometimes|required|date',
        ]);

        $expense->update($validated);
        return new ExpenseResource($expense);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['message' => 'Expense deleted'], 200);
    }
}