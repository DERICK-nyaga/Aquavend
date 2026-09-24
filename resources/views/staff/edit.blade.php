@extends('layouts.app')
@section('title', 'Edit Staff Member')

@section('content')
<div class="card" style="max-width:500px;margin:2rem auto;">

    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <h1>Edit Delivery Staff</h1>

    <form method="POST" action="{{ route('staff.update', $staff->id) }}">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                required 
                value="{{ old('name', $staff->name) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('name') border-red-500 @enderror"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                required 
                value="{{ old('email', $staff->email) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('email') border-red-500 @enderror"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input 
                type="text" 
                name="phone" 
                id="phone" 
                required 
                value="{{ old('phone', $staff->phone) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('phone') border-red-500 @enderror"
            >
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Station Assignment -->
        <div class="mb-3">
            <label for="station_id" class="block text-sm font-medium text-gray-700">Assign Station</label>
            <select 
                name="station_id" 
                id="station_id"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('station_id') border-red-500 @enderror"
            >
                <option value="">-- Select Station --</option>
                @foreach($stations as $station)
                    <option value="{{ $station->id }}" {{ old('station_id', $staff->station_id) == $station->id ? 'selected' : '' }}>
                        {{ $station->name }}
                    </option>
                @endforeach
            </select>
            @error('station_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password (Optional on update) -->
        <div class="mb-3">
            <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-xs text-gray-500">(Leave blank to keep current password)</span></label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('password') border-red-500 @enderror"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Motorbike Assignment -->
        <div class="mb-3">
            <label for="assigned_motorbike_id" class="block text-sm font-medium text-gray-700">Assign Motorbike (Optional)</label>
            <select 
                name="assigned_motorbike_id" 
                id="assigned_motorbike_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('assigned_motorbike_id') border-red-500 @enderror"
            >
                <option value="">-- No Motorbike --</option>
                @foreach($motorbikes as $bike)
                    <option value="{{ $bike->id }}" data-station="{{ $bike->station_id }}" {{ old('assigned_motorbike_id', $staff->assigned_motorbike_id) == $bike->id ? 'selected' : '' }}>
                        {{ $bike->registration_number }} ({{ $bike->model ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
            @error('assigned_motorbike_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn btn-primary" type="submit" style="margin-top:1rem;">Save Changes</button>
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

    // Initial run on page load (preserves existing assignment or old input)
    filterBikes();

    // Run when station selection changes
    stationSelect.addEventListener('change', filterBikes);
});
</script>
@endsection