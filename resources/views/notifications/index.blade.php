@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<h1>Notification Log</h1>

<div class="card">
    <table>
        <thead><tr><th>Customer</th><th>Channel</th><th>Event</th><th>Message</th><th>Status</th><th>Sent</th></tr></thead>
        <tbody>
            @forelse ($notifications as $n)
            <tr>
                <td>{{ $n->customer->name ?? '—' }}</td>
                <td>{{ strtoupper($n->channel) }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($n->event)) }}</td>
                <td style="max-width:300px; font-size:0.85rem;">{{ $n->message }}</td>
                <td>
                    <span class="badge badge-{{ $n->status === 'sent' ? 'active' : ($n->status === 'failed' ? 'inactive' : 'maintenance') }}">
                        {{ ucfirst($n->status) }}
                    </span>
                </td>
                <td>{{ $n->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="6">No notifications sent yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">{{ $notifications->links('partials.pagination') }}</div>
@endsection