@extends('layouts.app')
@section('title', 'Stock — ' . $station->name)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Stock Levels — {{ $station->name }}</h1>
    </div>

    {{-- Stock Table Card --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Product</th>
                        <th scope="col" class="px-6 py-3.5">Available</th>
                        <th scope="col" class="px-6 py-3.5">Threshold</th>
                        <th scope="col" class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($stock as $s)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">
                                {{ $s->product->display_name ?? $s->product->name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-800">
                                {{ $s->quantity_available }} {{$s->product->stockUnitLabel() }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-600">
                                {{ $s->low_stock_threshold }} {{$s->product->stockUnitLabel() }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($s->isLowStock())
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Low
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-400">
                                No stock records yet for this station.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Update Stock Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="mb-5 text-base font-bold text-slate-900">Set / Update Stock</h2>

        <form method="POST" action="{{ route('stations.stock.update', $station) }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Product</label>
                <select name="product_id" id="product_id" required onchange="updateUnitLabel()" 
                        class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" data-unit="{{ $p->stockUnitLabel() }}">
                            {{ $p->display_name ?? $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label id="qty-label" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Quantity Available</label>
                <input type="number" step="0.01" name="quantity_available" required 
                       class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <p class="mt-2 text-xs text-slate-500">
                    For water/oil/milk/yoghurt, enter total <strong>liters</strong> in stock (e.g. 10 &times; 20L bottles = 200). For eggs/bottles, enter the count directly.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Low Stock Threshold</label>
                <input type="number" step="0.01" name="low_stock_threshold" value="5" required 
                       class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Save Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateUnitLabel() {
    const select = document.getElementById('product_id');
    const unit = select.options[select.selectedIndex].dataset.unit;
    document.getElementById('qty-label').textContent = `Quantity Available (${unit})`;
}
updateUnitLabel();
</script>
@endsection