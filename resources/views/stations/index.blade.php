@extends('layouts.app')
@section('title', 'Stations')

@section('content')
<div class="top-actions">
    <h1>Stations</h1>
    <a href="/stations/create" class="btn btn-primary">+ New Station</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Name</th><th>Location</th><th>Status</th><th>Level (L)</th><th>Capacity (L)</th><th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stations as $station)
            <tr>
                <td>{{ $station->name }}</td>
                <td>{{ $station->location ?? '—' }}</td>
                <td><span class="badge badge-{{ $station->status }}">{{ ucfirst($station->status) }}</span></td>
                <td>{{ $station->current_level_liters ?? '—' }}</td>
                <td>{{ $station->tank_capacity_liters ?? '—' }}</td>
                <td>
                    <a href="/stations/{{ $station->id }}/edit" class="btn btn-edit">Edit</a>
                    <form class="inline" method="POST" action="/stations/{{ $station->id }}" onsubmit="return confirm('Delete this station?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No stations yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:1rem">{{ $stations->links('partials.pagination') }}</div>
@endsection