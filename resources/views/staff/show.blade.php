@extends('layouts.tailwind')
@section('title', $staff->name . ' - Staff Details')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-sky-900">{{ $staff->name }}</h1>
        <p class="text-sm text-slate-500">Staff ID: #{{ $staff->id }} &bull; Role: <span class="capitalize font-medium text-slate-700">{{ $staff->role }}</span></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('staff.edit', $staff->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
            Edit Details
        </a>
        <a href="{{ route('staff.index') }}" class="text-slate-500 hover:text-slate-700 text-sm px-3 py-2">
            Back to List
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Details & Status -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Overview Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Profile Overview</h2>
            
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-xs font-medium text-slate-400 uppercase">Employment Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                        @if($staff->employment_status === 'active') bg-green-100 text-green-800
                        @elseif($staff->employment_status === 'pending_approval') bg-yellow-100 text-yellow-800
                        @elseif($staff->employment_status === 'on_leave') bg-blue-100 text-blue-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ str_replace('_', ' ', ucfirst($staff->employment_status)) }}
                    </span>
                    @if($staff->status_reason)
                        <p class="text-xs text-slate-500 italic mt-1">Reason: {{ $staff->status_reason }}</p>
                    @endif
                </div>

                <div>
                    <span class="block text-xs font-medium text-slate-400 uppercase">Email</span>
                    <span class="text-slate-700 font-medium">{{ $staff->email }}</span>
                </div>

                <div>
                    <span class="block text-xs font-medium text-slate-400 uppercase">Phone</span>
                    <span class="text-slate-700 font-medium">{{ $staff->phone ?? 'Not provided' }}</span>
                </div>

                <div>
                    <span class="block text-xs font-medium text-slate-400 uppercase">Assigned Station</span>
                    <span class="text-slate-700 font-medium">{{ $staff->station->name ?? 'Unassigned' }}</span>
                </div>

                <div>
                    <span class="block text-xs font-medium text-slate-400 uppercase">Assigned Motorbike</span>
                    <span class="text-slate-700 font-medium">
                        @if($staff->motorbike)
                            {{ $staff->motorbike->registration_number }} ({{ $staff->motorbike->model ?? 'N/A' }})
                        @else
                            None
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Management Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 space-y-4">
            <h2 class="text-lg font-semibold text-slate-800 pb-2 border-b border-slate-100">Actions</h2>

            @if($staff->employment_status === 'pending_approval' && auth()->user()?->role === 'director')
                <form method="POST" action="{{ route('staff.approve', $staff->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm py-2 px-4 rounded-lg transition">
                        Approve Staff Account
                    </button>
                </form>
            @endif

            @if($staff->employment_status === 'suspended' || $staff->employment_status === 'dismissed')
                <form method="POST" action="{{ route('staff.reinstate', $staff->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm py-2 px-4 rounded-lg transition">
                        Reinstate Staff Member
                    </button>
                </form>
            @endif

            @if($staff->employment_status === 'on_leave')
                <form method="POST" action="{{ route('staff.endLeave', $staff->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm py-2 px-4 rounded-lg transition">
                        End Leave (Return to Active)
                    </button>
                </form>
            @endif

            @if($staff->employment_status === 'active')
                <!-- Suspend Modal / Form -->
                <form method="POST" action="{{ route('staff.suspend', $staff->id) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="status_reason" placeholder="Reason for suspension..." class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm py-2 px-4 rounded-lg transition">
                        Suspend Staff
                    </button>
                </form>

                <!-- Dismiss Form -->
                <form method="POST" action="{{ route('staff.dismiss', $staff->id) }}" class="space-y-2 pt-2 border-t border-slate-100" onsubmit="return confirm('Are you sure you want to dismiss this staff member?');">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="status_reason" placeholder="Reason for dismissal..." class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium text-sm py-2 px-4 rounded-lg transition">
                        Dismiss Staff
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Right Column: Assignments & Leave Scheduling -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Schedule Leave -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Place / Schedule Leave</h2>
            <form method="POST" action="{{ route('staff.placeOnLeave', $staff->id) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">End Date</label>
                    <input type="date" name="end_date" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-700 mb-1">Reason (Optional)</label>
                    <input type="text" name="reason" placeholder="Annual leave, sick leave, etc." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm px-4 py-2 rounded-lg transition">
                        Schedule Leave
                    </button>
                </div>
            </form>
        </div>

        <!-- Leave History -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Leave History</h2>
            @if($staff->leaves->isEmpty())
                <p class="text-sm text-slate-500">No leave history recorded for this staff member.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2">Start Date</th>
                                <th class="px-3 py-2">End Date</th>
                                <th class="px-3 py-2">Reason</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($staff->leaves as $leave)
                                <tr>
                                    <td class="px-3 py-2.5 font-medium text-slate-800">{{ $leave->start_date }}</td>
                                    <td class="px-3 py-2.5 font-medium text-slate-800">{{ $leave->end_date }}</td>
                                    <td class="px-3 py-2.5 text-slate-500">{{ $leave->reason ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection