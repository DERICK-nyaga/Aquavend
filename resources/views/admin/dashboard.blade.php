@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="flex justify-between items-center mb-5">
    <h1 class="text-2xl font-bold text-sky-800 m-0">Admin Dashboard</h1>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-[repeat(auto-fit,minmax(180px,1fr))] gap-4 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Total Revenue</p>
        <p class="text-2xl font-bold text-slate-900 m-0">{{ number_format($totalRevenue, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Pending Orders</p>
        <p class="text-2xl font-bold text-slate-900 m-0">{{ $pendingOrders }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Low Stock Products</p>
        <p class="text-2xl font-bold m-0 {{ $lowStockCount > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $lowStockCount }}</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80">
        <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Stations</p>
        <p class="text-2xl font-bold text-slate-900 m-0">{{ $stationCount }}</p>
    </div>
</div>

{{-- Quick Links --}}
<div class="mb-3">
    <h2 class="text-base font-semibold text-slate-800 m-0">Quick Links</h2>
</div>
<div class="flex flex-wrap gap-2.5 mb-6">
    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150 no-underline">Suppliers</a>
    <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150 no-underline">Expenses</a>
    <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150 no-underline">Customers</a>
    <a href="{{ route('reports') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150 no-underline">Reports</a>
    <a href="{{ route('stations.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150 no-underline">Stations</a>
</div>

{{-- Recent Transactions Table --}}
<div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80">
    <h2 class="text-base font-semibold text-slate-800 mb-4">Recent Transactions</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Station</th>
                    <th class="py-3 px-4">Customer</th>
                    <th class="py-3 px-4">Total</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse ($recentTransactions as $t)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3.5 px-4 font-mono text-xs font-semibold text-sky-700">#{{ $t->id }}</td>
                    <td class="py-3.5 px-4 font-medium text-slate-900">{{ $t->station->name ?? '—' }}</td>
                    <td class="py-3.5 px-4">{{ $t->customer->name ?? 'Walk-in' }}</td>
                    <td class="py-3.5 px-4 font-semibold text-slate-900">{{ number_format($t->total_amount, 2) }}</td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-100">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400 italic">No transactions yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection