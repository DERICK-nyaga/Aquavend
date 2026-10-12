@extends('layouts.app')
@section('title', 'Edit Expense — Aquavend')

@section('content')
<div class="max-w-2xl mx-auto py-4">

    <div class="mb-6 flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="/expenses" class="hover:text-sky-600 transition-colors no-underline flex items-center gap-1">
            <i class="bi bi-wallet2 text-sky-500 text-sm"></i>
            <span>Expenses</span>
        </a>
        <i class="bi bi-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-700 font-semibold">Edit Expense</span>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-200/80">
        
        <div class="flex items-center gap-3.5 mb-6 pb-6 border-b border-slate-100">
            <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600 text-lg font-bold shadow-xs shrink-0">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight m-0">Edit Expense Details</h1>
                <p class="text-xs text-slate-500 mt-0.5 m-0">Update information for this recorded expense</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2.5">
                <i class="bi bi-exclamation-circle-fill text-rose-500 text-base shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="/expenses/{{ $expense->id }}" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Category <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-tag text-base"></i>
                        </div>
                        <select 
                            id="category"
                            name="category" 
                            required
                            class="w-full pl-10 pr-8 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none cursor-pointer appearance-none"
                        >
                            @foreach (\App\Models\Expense::CATEGORIES as $cat)
                                <option value="{{ $cat }}" {{ $expense->category === $cat ? 'selected' : '' }}>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="station_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Station <span class="text-slate-400 font-normal lowercase">(optional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-building text-base"></i>
                        </div>
                        <select 
                            id="station_id"
                            name="station_id"
                            class="w-full pl-10 pr-8 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none cursor-pointer appearance-none"
                        >
                            <option value="">-- General / Business-wide --</option>
                            @foreach ($stations as $station)
                                <option value="{{ $station->id }}" {{ $expense->station_id == $station->id ? 'selected' : '' }}>
                                    {{ $station->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Description <span class="text-slate-400 font-normal lowercase">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-card-text text-base"></i>
                    </div>
                    <input 
                        id="description"
                        type="text" 
                        name="description" 
                        value="{{ old('description', $expense->description) }}" 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Amount <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-cash-stack text-base"></i>
                        </div>
                        <input 
                            id="amount"
                            type="number" 
                            step="0.01" 
                            name="amount" 
                            value="{{ old('amount', $expense->amount) }}" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                        >
                    </div>
                </div>

                <div>
                    <label for="expense_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Date <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-calendar-event text-base"></i>
                        </div>
                        <input 
                            id="expense_date"
                            type="date" 
                            name="expense_date" 
                            value="{{ old('expense_date', $expense->expense_date) }}" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                        >
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a 
                    href="/expenses" 
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
                    <span>Update Expense</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection