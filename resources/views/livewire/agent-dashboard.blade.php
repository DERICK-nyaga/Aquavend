<div wire:poll.10s class="space-y-8">

    {{-- Top Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-900 via-sky-800 to-indigo-900 p-6 sm:p-8 text-white shadow-xl shadow-sky-950/10">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md text-sky-200 border border-white/10 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Station Agent Portal (Live Active)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white m-0">
                    Welcome back, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-sky-100/80 text-xs sm:text-sm mt-1.5 max-w-xl font-normal leading-relaxed m-0">
                    Here is your station overview and real-time sales activity for today.
                </p>
            </div>

            @if ($station)
                <div class="shrink-0">
                    <a href="/transactions/create" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-xs text-sky-950 bg-white hover:bg-sky-50 shadow-lg shadow-black/10 active:scale-95 transition-all no-underline">
                        <i class="bi bi-plus-lg text-sm font-black"></i>
                        <span>Record New Sale</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if (!$station)
        {{-- Unassigned Station Alert Banner --}}
        <div class="bg-gradient-to-r from-amber-500/10 via-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl p-6 shadow-sm flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 text-lg font-bold shadow-sm shadow-amber-500/20">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-amber-950 uppercase tracking-wider m-0">No Station Assigned</h3>
                <p class="text-xs text-amber-800/90 mt-1 m-0 leading-relaxed">
                    You are currently not assigned to an active station. Please contact your system administrator or director to get station access.
                </p>
            </div>
        </div>
    @else

        {{-- Station Status & Quick Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

            {{-- Assigned Station Card --}}
            <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">Your Station</span>
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shadow-sm">
                        <i class="bi bi-building text-lg"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight m-0 truncate">{{ $station->name }}</h2>
                <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Station Status</span>
                    @php
                        $status = strtolower($station->status ?? '');
                        $statusBadge = match($status) {
                            'active' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                            'maintenance' => 'text-amber-600 bg-amber-50 border-amber-100',
                            default => 'text-rose-600 bg-rose-50 border-rose-100'
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 font-bold text-xs px-2 py-0.5 rounded-md border {{ $statusBadge }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-current' }}"></span>
                        {{ ucfirst($station->status) }}
                    </span>
                </div>
            </div>

            {{-- Today's Sales Card --}}
            <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">Today's Revenue</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                        <i class="bi bi-wallet2 text-lg"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-sm font-black text-sky-600">KES</span>
                    <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($todaysSales, 2) }}</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Completed Orders</span>
                    <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">{{ $todaysSalesCount }}</span>
                </div>
            </div>

            {{-- Current Stock Card --}}
            <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">Current Stock Level</span>
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center shadow-sm">
                        <i class="bi bi-droplet-half text-lg"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-black text-slate-900 tracking-tight font-mono">{{ $station->current_level_liters ?? '—' }}</span>
                    <span class="text-sm font-extrabold text-slate-400">L</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Tank Capacity</span>
                    <span class="font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                        {{ $station->tank_capacity_liters ?? '—' }}L
                    </span>
                </div>
            </div>

        </div>

        {{-- Recent Sales Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 m-0">Your Recent Sales</h2>
                    <p class="text-xs text-slate-400 mt-0.5 m-0">Live log of completed transactions at your station today</p>
                </div>
                <a href="/transactions" class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors no-underline">
                    <span>View full activity log</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/60 text-[0.68rem] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-6">Customer</th>
                            <th class="py-3.5 px-4 text-right">Amount (KES)</th>
                            <th class="py-3.5 px-6 pl-8 text-left">Payment Method</th>
                            <th class="py-3.5 px-6 text-right">Time Recorded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse ($recentTransactions as $t)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-semibold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-[0.65rem]">
                                        {{ substr($t->customer->name ?? 'W', 0, 1) }}
                                    </div>
                                    <span>{{ $t->customer->name ?? 'Walk-in' }}</span>
                                </div>
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
                            <td colspan="4" class="py-12 text-center text-slate-400 bg-slate-50/20">
                                <i class="bi bi-receipt text-3xl opacity-40 block mb-2"></i>
                                <p class="m-0 text-sm italic">No sales recorded at your station today yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @endif

</div>