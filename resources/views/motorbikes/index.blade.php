@extends('layouts.app')
@section('title', 'Fleet - Motorbikes — Aquavend')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight m-0">Motorbikes</h1>
            <p class="text-xs text-slate-500 mt-1 m-0">Manage delivery fleet, station assignments, and maintenance status</p>
        </div>

        <a 
            href="{{ route('motorbikes.create') }}" 
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/20 active:scale-[0.98] transition-all no-underline shrink-0"
        >
            <i class="bi bi-plus-lg text-sm"></i>
            <span>Add Motorbike</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Registration</th>
                        <th class="py-3.5 px-6">Model</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Assigned Staff</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse ($motorbikes as $bike)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center text-xs shrink-0">
                                        <i class="bi bi-bicycle"></i>
                                    </div>
                                    <span class="font-mono font-bold text-slate-900 tracking-wide uppercase">{{ $bike->registration_number }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-6 font-medium text-slate-600">
                                {{ $bike->model ?? 'N/A' }}
                            </td>

                            <td class="py-3.5 px-6">
                                @php
                                    $statusClasses = match($bike->status) {
                                        'available' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        'assigned' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                                        'maintenance' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200/80',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border {{ $statusClasses }}">
                                    {{ ucfirst($bike->status) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-6 text-slate-600 font-medium">
                                @if($bike->assignedStaff)
                                    <div class="flex items-center gap-1.5">
                                        <i class="bi bi-person text-slate-400 text-xs"></i>
                                        <span>{{ $bike->assignedStaff->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Unassigned</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-6 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a 
                                        href="{{ route('motorbikes.edit', $bike->id) }}" 
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-sky-600 hover:bg-sky-50 transition-all no-underline"
                                        title="Edit Motorbike"
                                    >
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>

                                    <form method="POST" action="{{ route('motorbikes.destroy', $bike->id) }}" onsubmit="return confirm('Delete this motorbike?')" class="m-0 inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all border-0 bg-transparent cursor-pointer"
                                            title="Delete Motorbike"
                                        >
                                            <i class="bi bi-trash3-fill text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                        <i class="bi bi-bicycle"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 m-0">No motorbikes registered</p>
                                    <p class="text-xs text-slate-400 mt-1 m-0">Get started by adding your first delivery motorbike.</p>
                                    <a href="{{ route('motorbikes.create') }}" class="mt-4 text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors no-underline">
                                        + Add Motorbike
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($motorbikes->hasPages())
        <div class="pt-2">
            {{ $motorbikes->links('partials.pagination') }}
        </div>
    @endif

</div>
@endsection