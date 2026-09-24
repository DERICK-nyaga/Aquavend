@extends('layouts.tailwind')
@section('title', 'Staff Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-sky-900">Staff Directory</h1>
    <a href="{{ route('staff.create') }}" class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-700">+ Add Staff</a>
</div>

@if ($errors->any())
    <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
        <div class="font-medium mb-1">Please fix the following issues:</div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 text-slate-600 text-left">
                <tr>
                    <th class="px-4 py-3">Name & Email</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Station</th>
                    <th class="px-4 py-3">Assigned Motorbike</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($staff as $s)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">
                        {{ $s->name }}<br>
                        <span class="text-slate-400 text-xs">{{ $s->email }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $s->phone ?? '—' }}</td>
                    <td class="px-4 py-3 capitalize font-medium text-slate-700">{{ $s->role }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $s->station->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">
                        @if ($s->motorbike)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                {{ $s->motorbike->registration_number }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $colors = [
                                'active' => 'bg-green-100 text-green-700',
                                'pending_approval' => 'bg-yellow-100 text-yellow-700',
                                'suspended' => 'bg-orange-100 text-orange-700',
                                'dismissed' => 'bg-red-100 text-red-700',
                                'on_leave' => 'bg-blue-100 text-blue-700',
                            ];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$s->employment_status] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ str_replace('_', ' ', ucfirst($s->employment_status ?? 'active')) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('staff.show', $s) }}" class="text-sky-600 font-medium hover:underline">Manage</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">No staff members found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if (method_exists($staff, 'links'))
    <div class="mt-4">
        {{ $staff->links() }}
    </div>
@endif
@endsection