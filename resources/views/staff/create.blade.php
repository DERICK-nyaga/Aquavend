@extends('layouts.tailwind')
@section('title', 'Add Staff')

@section('content')
<h1 class="text-2xl font-bold text-sky-900 mb-6">Add Staff</h1>

<div class="bg-white rounded-xl shadow-sm p-6 max-w-xl">
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('staff.store') }}" class="space-y-4">
        @csrf

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                required 
                value="{{ old('name') }}"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('name') border-red-500 @else border-slate-300 @enderror"
            >
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                required 
                value="{{ old('email') }}"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('email') border-red-500 @else border-slate-300 @enderror"
            >
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone Number (Optional)</label>
            <input 
                type="text" 
                name="phone" 
                id="phone" 
                value="{{ old('phone') }}"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('phone') border-red-500 @else border-slate-300 @enderror"
            >
            @error('phone')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
            <select 
                name="role" 
                id="role" 
                required 
                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('role') border-red-500 @else border-slate-300 @enderror"
            >
                <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent (Station Staff)</option>
                <option value="director" {{ old('role') == 'director' ? 'selected' : '' }}>Director</option>
            </select>
            @error('role')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Station Assignment -->
        <div>
            <label for="station_id" class="block text-sm font-medium text-slate-700 mb-1">Assign Station (Optional)</label>
            <select 
                name="station_id" 
                id="station_id" 
                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('station_id') border-red-500 @else border-slate-300 @enderror"
            >
                <option value="">-- Select Station --</option>
                @foreach($stations as $station)
                    <option value="{{ $station->id }}" {{ old('station_id') == $station->id ? 'selected' : '' }}>
                        {{ $station->name }}
                    </option>
                @endforeach
            </select>
            @error('station_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password (Leave blank for random temporary password)</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('password') border-red-500 @else border-slate-300 @enderror"
            >
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Motorbike Assignment -->
        <div>
            <label for="assigned_motorbike_id" class="block text-sm font-medium text-slate-700 mb-1">Assign Motorbike (Optional)</label>
            <select 
                name="assigned_motorbike_id" 
                id="assigned_motorbike_id" 
                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('assigned_motorbike_id') border-red-500 @else border-slate-300 @enderror"
            >
                <option value="">-- No Motorbike --</option>
                @foreach($motorbikes as $bike)
                    <option value="{{ $bike->id }}" data-station="{{ $bike->station_id }}" {{ old('assigned_motorbike_id') == $bike->id ? 'selected' : '' }}>
                        {{ $bike->registration_number }} ({{ $bike->model ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
            @error('assigned_motorbike_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <p class="text-xs text-slate-500 pt-1">New staff are created as <strong>pending approval</strong> — a director must approve them before they can log in and work.</p>

        <div class="pt-2 flex items-center">
            <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-700">Create Staff</button>
            <a href="{{ route('staff.index') }}" class="text-slate-500 hover:underline text-sm ml-4">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stationSelect = document.getElementById('station_id');
    const bikeSelect = document.getElementById('assigned_motorbike_id');
    const bikeOptions = Array.from(bikeSelect.querySelectorAll('option[data-station]'));

    function filterBikes() {
        const selectedStation = stationSelect.value;
        let selectedBikeStillValid = false;

        bikeOptions.forEach(option => {
            if (!selectedStation || option.dataset.station === selectedStation) {
                option.style.display = '';
                option.disabled = false;
                if (option.selected) selectedBikeStillValid = true;
            } else {
                option.style.display = 'none';
                option.disabled = true;
            }
        });

        // Reset motorbike selection if the selected bike belongs to another station
        if (!selectedBikeStillValid) {
            bikeSelect.value = '';
        }
    }

    filterBikes();
    stationSelect.addEventListener('change', filterBikes);
});
</script>
@endsection