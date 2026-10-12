@extends('layouts.app')
@section('title', 'Delivery Management — Aquavend')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight m-0">Delivery & Route Management</h1>
            <p class="text-xs text-slate-500 mt-1 m-0">Assign riders, track route progress, and update delivery statuses</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-xs">
            <i class="bi bi-check-circle-fill text-emerald-500 text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Order #</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Address</th>
                        <th class="py-3.5 px-6">Rider</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($deliveries as $delivery)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                #{{ $delivery->transaction_id }}
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center text-xs shrink-0">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ $delivery->transaction->customer->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-slate-600 max-w-xs truncate">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-geo-alt text-slate-400 text-xs shrink-0"></i>
                                    <span title="{{ $delivery->delivery_address }}">{{ $delivery->delivery_address }}</span>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <form method="POST" action="{{ route('deliveries.assign', $delivery) }}" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <div class="relative min-w-[160px]">
                                        <select 
                                            name="driver_id" 
                                            onchange="this.form.submit()"
                                            {{ $delivery->status == 'delivered' ? 'disabled' : '' }}
                                            class="w-full pl-3 pr-8 py-1.5 rounded-lg border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none cursor-pointer disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed appearance-none"
                                        >
                                            <option value="">-- Assign Rider --</option>
                                            @foreach($drivers as $driver)
                                                <option value="{{ $driver->id }}" {{ $delivery->driver_id == $driver->id ? 'selected' : '' }}>
                                                    {{ $driver->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="bi bi-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </form>
                            </td>

                            <td class="py-4 px-6">
                                @php
                                    $statusClasses = match($delivery->status) {
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                        'assigned' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                                        'out_for_delivery' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                        'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        'failed' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200/80',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border {{ $statusClasses }}">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <form method="POST" action="{{ route('deliveries.updateStatus', $delivery) }}" class="m-0 inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <div class="relative min-w-[150px]">
                                        <select 
                                            name="status" 
                                            onchange="this.form.submit()"
                                            {{ $delivery->status == 'delivered' ? 'disabled' : '' }}
                                            class="w-full pl-3 pr-8 py-1.5 rounded-lg border border-slate-200 bg-slate-50/50 text-xs font-semibold text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none cursor-pointer disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed appearance-none"
                                        >
                                            <option value="pending" {{ $delivery->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="assigned" {{ $delivery->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                            <option value="out_for_delivery" {{ $delivery->status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                            <option value="delivered" {{ $delivery->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="failed" {{ $delivery->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="bi bi-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 m-0">No delivery routes found</p>
                                    <p class="text-xs text-slate-400 mt-1 m-0">Active and past deliveries will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($deliveries->hasPages())
        <div class="pt-2">
            {{ $deliveries->links() }}
        </div>
    @endif

</div>
@endsection