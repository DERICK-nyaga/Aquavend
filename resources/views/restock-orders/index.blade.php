@extends('layouts.app')

@section('title', 'Restock Orders')

@section('content')
@php
    $totalOrders  = $totalOrders  ?? $orders->total();
    $pendingCount  = $pendingCount  ?? $orders->where('status', 'pending')->count();
    $receivedCount = $receivedCount ?? $orders->where('status', 'received')->count();
    $totalVolume   = $totalVolume   ?? $orders->sum('liters_added');
    
    $avatarColors = [
        'bg-blue-500', 'bg-pink-500', 'bg-amber-500', 'bg-emerald-500', 'bg-indigo-500', 'bg-purple-500'
    ];
@endphp

<div class="min-h-screen bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl space-y-6">

        {{-- Page Header --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-md shadow-indigo-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">Restock Orders</h1>
                    <p class="text-xs text-slate-500">Manage supplier bulk water deliveries and incoming stock</p>
                </div>
            </div>

            <a href="{{ route('restock-orders.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-full bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-md shadow-indigo-200 transition-all hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                New Restock Order
            </a>
        </header>

        {{-- Flash Notification --}}
        @if(session('success'))
            <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800 shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="text-emerald-600 hover:text-emerald-800" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        {{-- Top Summary Metric Cards --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Total Orders
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">{{ $totalOrders }}</div>
                <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-400">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5 5 5M7 13l5 5 5-5"/></svg>
                    All time
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">{{ $pendingCount }}</div>
                <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-amber-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Awaiting delivery
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Received
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">{{ $receivedCount }}</div>
                <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Completed
                </div>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Volume In
                </div>
                <div class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">
                    {{ number_format($totalVolume, 0) }} <span class="text-xs font-normal text-slate-400">L</span>
                </div>
                <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-slate-400">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Cumulative
                </div>
            </div>
        </div>

        {{-- Main Table Container --}}
        <div class="rounded-3xl border border-slate-100 bg-white shadow-sm">
            
            {{-- Toolbar --}}
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="inline-flex rounded-full bg-slate-100/80 p-1">
                    <button type="button" class="filter-tab active rounded-full bg-white px-4 py-1 text-xs font-semibold text-indigo-600 shadow-sm transition-all" data-filter="all">
                        All
                    </button>
                    <button type="button" class="filter-tab rounded-full px-4 py-1 text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all" data-filter="pending">
                        Pending
                    </button>
                    <button type="button" class="filter-tab rounded-full px-4 py-1 text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all" data-filter="received">
                        Received
                    </button>
                </div>

                <div class="relative w-full sm:w-64">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="roSearch" placeholder="Search supplier, station, or ID..." 
                           class="w-full rounded-full border border-slate-200 bg-white py-1.5 pl-9 pr-3 text-xs text-slate-700 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
            </div>

            {{-- Table without horizontal scrollbar --}}
            <div class="w-full overflow-x-auto lg:overflow-x-visible">
                <table class="w-full table-auto text-left text-xs text-slate-600" id="roTable">
                    <thead class="bg-slate-50/70 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-y border-slate-100">
                        <tr>
                            <th class="px-3 py-3 xl:px-4">Order</th>
                            <th class="px-3 py-3 xl:px-4">Supplier</th>
                            <th class="px-3 py-3 xl:px-4">Station</th>
                            <th class="px-3 py-3 xl:px-4">Volume</th>
                            <th class="px-3 py-3 xl:px-4">Total Cost</th>
                            <th class="px-3 py-3 xl:px-4">Status</th>
                            <th class="px-3 py-3 xl:px-4">Received By</th>
                            <th class="px-3 py-3 text-right xl:px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($orders as $index => $order)
                            @php
                                $bgColor = $avatarColors[$index % count($avatarColors)];
                                $supplierName = $order->supplier->name ?? 'N/A';
                                $firstLetter = strtoupper(substr($supplierName, 0, 1));
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors" 
                                data-status="{{ $order->status }}"
                                data-search="{{ strtolower($supplierName.' '.($order->station->name ?? '').' #'.sprintf('%04d', $order->id)) }}">
                                
                                {{-- Order ID --}}
                                <td class="px-3 py-3 xl:px-4 whitespace-nowrap">
                                    <span class="inline-flex rounded-md bg-indigo-50/70 px-2 py-0.5 text-xs font-semibold text-indigo-600">
                                        #{{ sprintf('%04d', $order->id) }}
                                    </span>
                                </td>

                                {{-- Supplier --}}
                                <td class="px-3 py-3 xl:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $bgColor }} text-[11px] font-bold text-white shadow-sm">
                                            {{ $firstLetter }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate font-bold text-slate-900">{{ $supplierName }}</div>
                                            @if(!empty($order->supplier->phone))
                                                <div class="hidden xl:flex items-center gap-1 text-[10px] text-slate-400">
                                                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    {{ $order->supplier->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Station --}}
                                <td class="px-3 py-3 xl:px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                        <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $order->station->name ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Volume --}}
                                <td class="px-3 py-3 xl:px-4 font-bold text-slate-900 whitespace-nowrap">
                                    {{ number_format($order->liters_added, 2) }} <span class="font-normal text-slate-400">L</span>
                                </td>

                                {{-- Total Cost --}}
                                <td class="px-3 py-3 xl:px-4 font-bold text-slate-900 whitespace-nowrap">
                                    ${{ number_format($order->total_cost, 2) }}
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-3 xl:px-4 whitespace-nowrap">
                                    @if($order->status === 'pending')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-200/50">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @elseif($order->status === 'received')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-200/50">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Received
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-semibold text-slate-700">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Received By --}}
                                <td class="px-3 py-3 xl:px-4 whitespace-nowrap">
                                    @if($order->receiver)
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[9px] font-bold text-indigo-600">
                                                {{ strtoupper(substr($order->receiver->name, 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-slate-700">{{ $order->receiver->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400">&mdash;</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-3 py-3 text-right xl:px-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        @if($order->status === 'pending')
                                            <form action="{{ route('restock-orders.receive', $order->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Confirm delivery?')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-0.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-600 hover:bg-emerald-100 transition">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Receive
                                                </button>
                                            </form>

                                            <a href="{{ route('restock-orders.edit', $order->id) }}" class="inline-flex items-center gap-0.5 rounded-lg border border-indigo-100 bg-indigo-50/60 px-2 py-1 text-[11px] font-semibold text-indigo-600 hover:bg-indigo-100 transition">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Edit
                                            </a>
                                        @endif

                                        <a href="{{ route('restock-orders.show', $order->id) }}" class="inline-flex items-center gap-0.5 rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 hover:bg-slate-50 transition">
                                            <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View
                                        </a>

                                        <form action="{{ route('restock-orders.destroy', $order->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Delete this restock order?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white p-1 text-red-500 hover:bg-red-50 transition" title="Delete">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                    No restock orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Pagination --}}
            @if($orders->hasPages())
                <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('roSearch');
        const tableRows = document.querySelectorAll('#roTable tbody tr[data-search]');
        const tabs = document.querySelectorAll('.filter-tab');
        let currentFilter = 'all';

        function filterTable() {
            const query = (searchInput?.value || '').toLowerCase().trim();

            tableRows.forEach(row => {
                const matchesSearch = !query || row.dataset.search.includes(query);
                const matchesTab = currentFilter === 'all' || row.dataset.status === currentFilter;
                row.style.display = (matchesSearch && matchesTab) ? '' : 'none';
            });
        }

        if (searchInput) searchInput.addEventListener('input', filterTable);

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(t => {
                    t.classList.remove('active', 'bg-white', 'text-indigo-600', 'shadow-sm');
                    t.classList.add('text-slate-500');
                });
                this.classList.add('active', 'bg-white', 'text-indigo-600', 'shadow-sm');
                this.classList.remove('text-slate-500');

                currentFilter = this.dataset.filter;
                filterTable();
            });
        });
    });
</script>
@endpush
@endsection