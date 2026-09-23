@extends('layouts.app')
@section('title', 'Edit Customer — Aquavend')

@section('content')
<div class="max-w-3xl mx-auto py-4 space-y-6">

    {{-- Breadcrumb Navigation --}}
    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="/customers" class="hover:text-sky-600 transition-colors no-underline flex items-center gap-1">
            <i class="bi bi-people-fill text-sky-500 text-sm"></i>
            <span>Customers</span>
        </a>
        <i class="bi bi-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-700 font-semibold">Edit Customer</span>
    </div>

    {{-- Error Alert Banner --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2.5">
            <i class="bi bi-exclamation-circle-fill text-rose-500 text-base shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Main Customer Information Form --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-200/80">
        
        {{-- Header --}}
        <div class="flex items-center gap-3.5 mb-6 pb-6 border-b border-slate-100">
            <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-600 text-lg font-bold shadow-xs shrink-0">
                <i class="bi bi-person-gear"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight m-0">Edit Customer Details</h1>
                <p class="text-xs text-slate-500 mt-0.5 m-0">Update profile details for {{ $customer->name }}</p>
            </div>
        </div>

        <form method="POST" action="/customers/{{ $customer->id }}" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-person text-base"></i>
                        </div>
                        <input 
                            id="name"
                            type="text" 
                            name="name" 
                            value="{{ old('name', $customer->name) }}" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('name') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                        >
                    </div>
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Phone Number <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-telephone text-base"></i>
                        </div>
                        <input 
                            id="phone"
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $customer->phone) }}" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('phone') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                        >
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Email Address <span class="text-slate-400 font-normal lowercase">(optional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-envelope text-base"></i>
                        </div>
                        <input 
                            id="email"
                            type="email" 
                            name="email" 
                            value="{{ old('email', $customer->email) }}" 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                        >
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Physical Address <span class="text-slate-400 font-normal lowercase">(optional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-geo-alt text-base"></i>
                        </div>
                        <input 
                            id="address"
                            type="text" 
                            name="address" 
                            value="{{ old('address', $customer->address) }}" 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('address') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                        >
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a 
                    href="/customers" 
                    class="px-4 py-2.5 rounded-xl font-bold text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all no-underline flex items-center gap-1.5"
                >
                    <i class="bi bi-x-circle text-sm"></i>
                    <span>Cancel</span>
                </a>

                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/20 active:scale-[0.98] transition-all cursor-pointer flex items-center gap-2"
                >
                    <i class="bi bi-check-lg text-sm"></i>
                    <span>Update Customer</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Wallet Balance Management Card --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-200/80">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600 text-base font-bold shrink-0">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 m-0">Wallet Balance</h2>
                    <p class="text-xs text-slate-500 m-0 mt-0.5">Top-up or deduct balance from customer wallet</p>
                </div>
            </div>
            <span class="text-lg font-extrabold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                KSh {{ number_format($customer->wallet_balance, 2) }}
            </span>
        </div>

        <form method="POST" action="{{ route('customers.wallet', $customer) }}" class="flex flex-col sm:flex-row items-end gap-4">
            @csrf
            <div class="w-full sm:flex-1">
                <label for="wallet_amount" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Amount</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-currency-dollar text-base"></i>
                    </div>
                    <input 
                        id="wallet_amount"
                        type="number" 
                        step="0.01" 
                        name="amount" 
                        required 
                        placeholder="0.00"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
            </div>

            <div class="w-full sm:flex-1">
                <label for="wallet_action" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Action</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-arrow-left-right text-base"></i>
                    </div>
                    <select 
                        id="wallet_action"
                        name="action"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none cursor-pointer"
                    >
                        <option value="add">Add (Top-up)</option>
                        <option value="deduct">Deduct</option>
                    </select>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 active:scale-[0.98] transition-all cursor-pointer flex items-center justify-center gap-2 shrink-0"
            >
                <i class="bi bi-arrow-down-up text-sm"></i>
                <span>Apply Action</span>
            </button>
        </form>
    </div>

    {{-- Credit Limit Management Card --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-200/80">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600 text-base font-bold shrink-0">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 m-0">Credit Limit Settings</h2>
                    <p class="text-xs text-slate-500 m-0 mt-0.5">Adjust max allowable credit threshold</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-semibold text-slate-500 block">Credit Used / Limit</span>
                <span class="text-sm font-extrabold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 inline-block mt-0.5">
                    KSh {{ number_format($customer->credit_balance, 2) }} / {{ number_format($customer->credit_limit, 2) }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('customers.credit-limit', $customer) }}" class="flex flex-col sm:flex-row items-end gap-4">
            @csrf
            <div class="w-full sm:flex-1">
                <label for="credit_limit" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">New Credit Limit</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-shield-check text-base"></i>
                    </div>
                    <input 
                        id="credit_limit"
                        type="number" 
                        step="0.01" 
                        name="credit_limit" 
                        value="{{ $customer->credit_limit }}" 
                        required 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-amber-600 hover:bg-amber-500 shadow-md shadow-amber-600/20 active:scale-[0.98] transition-all cursor-pointer flex items-center justify-center gap-2 shrink-0"
            >
                <i class="bi bi-sliders text-sm"></i>
                <span>Update Limit</span>
            </button>
        </form>
    </div>

</div>
@endsection