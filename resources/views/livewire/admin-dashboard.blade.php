<div wire:poll.10s class="space-y-8">

    {{-- Top Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-900 via-sky-800 to-indigo-900 p-6 sm:p-8 text-white shadow-xl shadow-sky-950/10">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md text-sky-200 border border-white/10 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Live Monitoring Active (Polling every 10s)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white m-0">
                    Welcome back, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-sky-100/80 text-xs sm:text-sm mt-1.5 max-w-xl font-normal leading-relaxed m-0">
                    Here is an overview of your water stations and sales activity today.
                </p>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- Today's Revenue --}}
        <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">Today's Revenue</span>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shadow-sm">
                    <i class="bi bi-wallet2 text-lg"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-sm font-black text-sky-600">KES</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($todaysSales, 2) }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Sales Today</span>
                <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">{{ $todaysSalesCount }}</span>
            </div>
        </div>

        {{-- Total Customers --}}
        <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">Total Customers</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                    <i class="bi bi-people text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-900 tracking-tight m-0">{{ $totalCustomers }}</p>
            <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Added Today</span>
                <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">+{{ $newCustomersToday }}</span>
            </div>
        </div>

        {{-- Active Stations --}}
        <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">Active Stations</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                    <i class="bi bi-geo-alt text-lg"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $stations->where('status', 'active')->count() }}</span>
                <span class="text-xs font-semibold text-slate-400">/ {{ $stations->count() }} total</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Status</span>
                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                </span>
            </div>
        </div>

        {{-- Low Stock Alerts Card --}}
        <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border {{ $lowStockStations->count() ? 'border-rose-300 ring-4 ring-rose-50/50' : 'border-slate-100' }}">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">Low Stock Alerts</span>
                <div class="w-10 h-10 rounded-xl {{ $lowStockStations->count() ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center">
                    <i class="bi {{ $lowStockStations->count() ? 'bi-exclamation-triangle-fill animate-bounce' : 'bi-check-circle-fill' }} text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-black tracking-tight m-0 {{ $lowStockStations->count() ? 'text-rose-600' : 'text-slate-900' }}">
                {{ $lowStockStations->count() }}
            </p>
            <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Threshold</span>
                <span class="font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">below 20%</span>
            </div>
        </div>

    </div>

    {{-- Low Stock Alert Banner --}}
    @if ($lowStockStations->count())
    <div class="bg-gradient-to-r from-rose-500/10 via-rose-50 to-orange-50 border border-rose-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-rose-950 uppercase tracking-wider m-0">
                        Stations Needing Immediate Refill
                    </h2>
                    <p class="text-xs text-rose-700/80 m-0">Action required for tanks running low on volume.</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-rose-200/60">
            @foreach ($lowStockStations as $s)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-sm">
                    <div class="flex items-center gap-2.5 text-slate-800 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        <span>{{ $s->name }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-mono text-xs font-bold text-rose-700 bg-rose-100/80 px-2.5 py-1 rounded-md border border-rose-200">
                            {{ $s->current_level_liters }}L / {{ $s->tank_capacity_liters }}L
                        </span>
                        <a href="/stock-refills/create" class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 hover:text-rose-900 transition-colors no-underline">
                            Refill Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent Transactions Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 m-0">Recent Transactions</h2>
                <p class="text-xs text-slate-400 mt-0.5 m-0">Latest customer sales recorded across stations</p>
            </div>
            <a href="/transactions" class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors no-underline">
                <span>View all transactions</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/60 text-[0.68rem] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Station</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4 text-right">Amount</th>
                        <th class="py-3.5 px-6 pl-8 text-left">Payment</th>
                        <th class="py-3.5 px-6 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse ($recentTransactions as $t)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-6 font-semibold text-slate-900">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-building text-slate-400 text-xs"></i>
                                <span>{{ $t->station->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            {{ $t->customer->name ?? 'Walk-in' }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-slate-900 font-mono">
                            {{ number_format($t->total_amount, 2) }}
                        </td>
                        <td class="py-3.5 px-6 pl-8">
                            @php
                                $pm = strtolower($t->payment_method ?? '');
                                $badgeStyle = match($pm) {
                                    'cash' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'mpesa', 'mobile' => 'bg-green-50 text-green-700 border-green-200',
                                    'card', 'wallet' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                                {{ ucfirst($t->payment_method) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-right text-xs text-slate-400 font-medium">
                            {{ $t->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400 bg-slate-50/20">
                            <i class="bi bi-inbox text-3xl opacity-40 block mb-2"></i>
                            <p class="m-0 text-sm italic">No recent transactions recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>