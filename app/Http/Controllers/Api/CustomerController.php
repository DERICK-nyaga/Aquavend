<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return CustomerResource::collection(Customer::latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return new CustomerResource($customer);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['message' => 'Customer deleted'], 200);
    }

    public function repayCredit(Request $request, Customer $customer)
    {
        $validated = $request->validate(['amount' => 'required|numeric|min:0.01']);

        if ($validated['amount'] > $customer->credit_balance) {
            return response()->json(['message' => 'Amount exceeds outstanding balance.'], 422);
        }

        $customer->decrement('credit_balance', $validated['amount']);

        return response()->json(['message' => 'Repayment recorded', 'customer' => $customer->fresh()]);
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
                return response()->json(['message' => 'Cannot deduct more than current balance.'], 422);
            }
            $customer->decrement('wallet_balance', $validated['amount']);
        }

        return new CustomerResource($customer->fresh());
    }

    public function setCreditLimit(Request $request, Customer $customer)
    {
        $validated = $request->validate(['credit_limit' => 'required|numeric|min:0']);
        $customer->update(['credit_limit' => $validated['credit_limit']]);

        return new CustomerResource($customer->fresh());
    }
}