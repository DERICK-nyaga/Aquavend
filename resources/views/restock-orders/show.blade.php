@extends('layouts.app')

@section('title', 'Restock Order #' . $restockOrder->id)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            Restock Order <span class="text-gray-500">#{{ $restockOrder->id }}</span>
        </h1>
        <a href="/restock-orders"
           class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-sm font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition">
            ← Back to List
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('warning') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Order Details</h2>

            @php
                $statusStyles = [
                    'received' => 'bg-green-100 text-green-800 ring-green-200',
                    'pending'  => 'bg-amber-100 text-amber-800 ring-amber-200',
                ];
                $statusClass = $statusStyles[$restockOrder->status] ?? 'bg-gray-100 text-gray-800 ring-gray-200';
            @endphp
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                {{ ucfirst($restockOrder->status) }}
            </span>
        </div>

        <dl class="divide-y divide-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ $restockOrder->supplier->name ?? 'N/A' }}
                </dd>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4 bg-gray-50">
                <dt class="text-sm font-medium text-gray-500">Destination Station</dt>
                <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ $restockOrder->station->name ?? 'N/A' }}
                    @if ($restockOrder->station)
                        <span class="text-gray-500">
                            — {{ $restockOrder->station->capacitySummary() }}
                        </span>
                    @endif
                </dd>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <dt class="text-sm font-medium text-gray-500">Volume</dt>
                <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ number_format($restockOrder->liters_added, 2) }} Liters
                </dd>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4 bg-gray-50">
                <dt class="text-sm font-medium text-gray-500">Total Cost</dt>
                <dd class="text-sm text-gray-900 sm:col-span-2 font-medium">
                    ${{ number_format($restockOrder->total_cost, 2) }}
                </dd>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <dt class="text-sm font-medium text-gray-500">Received By</dt>
                <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ $restockOrder->receiver->name ?? 'Not received yet' }}
                </dd>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4 bg-gray-50">
                <dt class="text-sm font-medium text-gray-500">Received At</dt>
                <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ $restockOrder->received_at ? $restockOrder->received_at->format('Y-m-d H:i') : '—' }}
                </dd>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="text-sm text-gray-900 sm:col-span-2 whitespace-pre-line">
                    {{ $restockOrder->notes ?? 'None' }}
                </dd>
            </div>
        </dl>

        @if ($restockOrder->status === 'pending')
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row sm:justify-end gap-3">
                <a href="/restock-orders/{{ $restockOrder->id }}/edit"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-amber-500 text-white text-sm font-medium hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition">
                    Edit Order
                </a>

                <form action="/restock-orders/{{ $restockOrder->id }}/receive"
                      method="POST"
                      onsubmit="return confirm('Confirm delivery? This will update stock levels and record an expense.')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                        Mark as Received
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection