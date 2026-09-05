<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierWebController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        Supplier::create($validated);
        return redirect('/suppliers')->with('success', 'Supplier added.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);
        return redirect('/suppliers')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect('/suppliers')->with('success', 'Supplier deleted.');
    }
}