<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerWebController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect('/customers')->with('success', 'Customer created.');
    }

    public function adjustWallet(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'action' => 'required|in:add,deduct',
        ]);

        if ($validated['action'] === 'add') {
            $customer->increment('wallet_balance', $validated['amount']);
        } else {
            if ($validated['amount'] > $customer->wallet_balance) {
                return back()->withErrors('Cannot deduct more than current balance.');
            }
            $customer->decrement('wallet_balance', $validated['amount']);
        }

        return redirect('/customers')->with('success', 'Wallet updated.');
    }

    public function setCreditLimit(Request $request, Customer $customer)
    {
        $validated = $request->validate(['credit_limit' => 'required|numeric|min:0']);

        $customer->update(['credit_limit' => $validated['credit_limit']]);

        return redirect('/customers')->with('success', 'Credit limit updated.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect('/customers')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect('/customers')->with('success', 'Customer deleted.');
    }
}