@extends('layouts.app')
@section('title', 'New Customer — Aquavend')

@section('content')
<div class="max-w-2xl mx-auto py-4">

    {{-- Breadcrumb Navigation --}}
    <div class="mb-6 flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="/customers" class="hover:text-sky-600 transition-colors no-underline flex items-center gap-1">
            <i class="bi bi-people-fill text-sky-500 text-sm"></i>
            <span>Customers</span>
        </a>
        <i class="bi bi-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-700 font-semibold">New Customer</span>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-200/80">
        
        {{-- Header --}}
        <div class="flex items-center gap-3.5 mb-6 pb-6 border-b border-slate-100">
            <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-600 text-lg font-bold shadow-xs shrink-0">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight m-0">Add New Customer</h1>
                <p class="text-xs text-slate-500 mt-0.5 m-0">Enter the customer details to register them in the system</p>
            </div>
        </div>

        {{-- Error Alert Banner --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2.5">
                <i class="bi bi-exclamation-circle-fill text-rose-500 text-base shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="/customers" class="space-y-5">
            @csrf
            
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
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        placeholder="e.g. Jane Doe"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('name') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
                @error('name')
                    <p class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
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
                        value="{{ old('phone') }}" 
                        required 
                        placeholder="e.g. +254 700 000000"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('phone') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
                @error('phone')
                    <p class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
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
                        value="{{ old('email') }}" 
                        placeholder="jane@example.com"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
                @error('email')
                    <p class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
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
                        value="{{ old('address') }}" 
                        placeholder="e.g. Westlands, Nairobi"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('address') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
                @error('address')
                    <p class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
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
                    <span>Create Customer</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection