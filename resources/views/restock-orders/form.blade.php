{{-- 
    Form Partial for Restock Orders (Create & Edit)
    Requires: $suppliers, $stations, and optional $restockOrder model
--}}

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

    {{-- Supplier Field --}}
    <div class="flex flex-col gap-1.5">
        <label for="supplier_id" class="text-xs font-semibold uppercase tracking-wider text-slate-600">
            Supplier <span class="text-red-500">*</span>
        </label>
        <select name="supplier_id" id="supplier_id" required
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <option value="">-- Select Supplier --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" 
                    {{ old('supplier_id', $restockOrder->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Destination Station Field --}}
    <div class="flex flex-col gap-1.5">
        <label for="station_id" class="text-xs font-semibold uppercase tracking-wider text-slate-600">
            Destination Station <span class="text-red-500">*</span>
        </label>
        <select name="station_id" id="station_id" required
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <option value="">-- Select Station --</option>
            @foreach($stations as $station)
                <option value="{{ $station->id }}" 
                    {{ old('station_id', $restockOrder->station_id ?? '') == $station->id ? 'selected' : '' }}>
                    {{ $station->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Volume (Liters) Field --}}
    <div class="flex flex-col gap-1.5">
        <label for="liters_added" class="text-xs font-semibold uppercase tracking-wider text-slate-600">
            Volume (Liters) <span class="text-red-500">*</span>
        </label>
        <input type="number" step="0.01" min="0.01" name="liters_added" id="liters_added" required
               value="{{ old('liters_added', $restockOrder->liters_added ?? '') }}" 
               placeholder="e.g. 5000"
               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
    </div>

    {{-- Total Cost Field --}}
    <div class="flex flex-col gap-1.5">
        <label for="total_cost" class="text-xs font-semibold uppercase tracking-wider text-slate-600">
            Total Cost ($) <span class="text-red-500">*</span>
        </label>
        <input type="number" step="0.01" min="0" name="total_cost" id="total_cost" required
               value="{{ old('total_cost', $restockOrder->total_cost ?? '') }}" 
               placeholder="e.g. 1500.00"
               class="w-full text-right rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
    </div>

    {{-- Status Field (Only selectable on creation or pending status) --}}
    @if(!isset($restockOrder) || $restockOrder->status === 'pending')
        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="status" class="text-xs font-semibold uppercase tracking-wider text-slate-600">
                Status <span class="text-red-500">*</span>
            </label>
            <select name="status" id="status" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="pending" {{ old('status', $restockOrder->status ?? 'pending') === 'pending' ? 'selected' : '' }}>
                    Pending Delivery
                </option>
                <option value="received" {{ old('status', $restockOrder->status ?? '') === 'received' ? 'selected' : '' }}>
                    Received Immediately (Update Stock & Log Expense)
                </option>
            </select>
        </div>
    @endif

    {{-- Notes Field --}}
    <div class="flex flex-col gap-1.5 sm:col-span-2">
        <label for="notes" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Notes</label>
        <textarea name="notes" id="notes" rows="3" 
                  placeholder="Optional delivery notes, invoice IDs, or vehicle details..."
                  class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('notes', $restockOrder->notes ?? '') }}</textarea>
    </div>

</div>

{{-- Form Footer Actions --}}
<div class="mt-8 flex flex-col-reverse justify-end gap-3 border-t border-slate-200 pt-6 sm:flex-row">
    <a href="{{ route('restock-orders.index') }}" 
       class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
        Cancel
    </a>
    <button type="submit" 
            class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
        {{ isset($restockOrder) ? 'Update Restock Order' : 'Create Restock Order' }}
    </button>
</div>