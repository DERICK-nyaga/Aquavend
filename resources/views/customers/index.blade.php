@extends('layouts.app')
@section('title', 'Customers — Aquavend')

@section('content')
<div class="space-y-6">

    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight m-0">Customers</h1>
            <p class="text-xs text-slate-500 mt-1 m-0">Manage customer accounts, wallet balances, and contact information</p>
        </div>

        <a 
            href="/customers/create" 
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/20 active:scale-[0.98] transition-all no-underline shrink-0"
        >
            <i class="bi bi-person-plus-fill text-sm"></i>
            <span>New Customer</span>
        </a>
    </div>

    {{-- Customers Data Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-6">Customer Name</th>
                        <th class="py-3 px-6">Phone Number</th>
                        <th class="py-3 px-6">Email Address</th>
                        <th class="py-3 px-6 text-right">Wallet Balance</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center text-xs shrink-0">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ $customer->name }}</span>
                                </div>
                            </td>

                            <td class="py-3 px-6 text-slate-600 font-normal">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-telephone text-slate-400 text-xs"></i>
                                    <span>{{ $customer->phone }}</span>
                                </div>
                            </td>

                            <td class="py-3 px-6 text-slate-600 font-normal">
                                @if($customer->email)
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-envelope text-slate-400 text-xs"></i>
                                        <span>{{ $customer->email }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-300 font-light">—</span>
                                @endif
                            </td>

                            <td class="py-3 px-6 text-right">
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200/60 inline-block font-mono">
                                    KSh {{ number_format($customer->wallet_balance, 2) }}
                                </span>
                            </td>

                            <td class="py-3 px-6 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a 
                                        href="/customers/{{ $customer->id }}/edit" 
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-sky-600 hover:bg-sky-50 transition-all no-underline"
                                        title="Edit Customer"
                                    >
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>

                                    <form method="POST" action="/customers/{{ $customer->id }}" onsubmit="return confirm('Are you sure you want to delete {{ $customer->name }}?')" class="m-0 inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all border-0 bg-transparent cursor-pointer"
                                            title="Delete Customer"
                                        >
                                            <i class="bi bi-trash3-fill text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty State --}}
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 m-0">No customers found</p>
                                    <p class="text-xs text-slate-400 mt-1 m-0">Get started by creating your first customer account.</p>
                                    <a href="/customers/create" class="mt-4 text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors no-underline">
                                        + Add New Customer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Wrapper --}}
    @if ($customers->hasPages())
        <div class="pt-2">
            {{ $customers->links('partials.pagination') }}
        </div>
    @endif

</div>
@endsection