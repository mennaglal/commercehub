@extends('admin.layouts.app')

@php
    // The icon of each kind of notification (Feather icons, the same set as your sidebar).
    $icons = ['order' => 'fe-shopping-cart', 'return' => 'fe-rotate-ccw', 'store' => 'fe-shopping-bag', 'contact' => 'fe-mail', 'broadcast' => 'fe-send'];
@endphp

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ __('notifications.title') }}</h4>
        <div>
            @can('notifications.send')
                <a href="{{ route('admin.notifications.compose') }}" class="btn btn-outline-primary">{{ __('notifications.send_title') }}</a>
            @endcan
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('admin.notifications.read-all') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-primary">{{ __('notifications.mark_all_read') }}</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $unreadOnly ? '' : 'active' }}" href="{{ route('admin.notifications.index') }}">{{ __('notifications.all') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $unreadOnly ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}">
                {{ __('notifications.unread') }} ({{ $unreadCount }})
            </a>
        </li>
    </ul>

    <div class="card">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                @php $data = $notification->data; @endphp
                <div class="list-group-item d-flex align-items-start {{ $notification->read_at ? '' : 'bg-light' }}">
                    <div class="me-3 mr-3 mt-1">
                        <i class="fe {{ $icons[$data['type'] ?? ''] ?? 'fe-bell' }} fe-16 text-primary"></i>
                    </div>

                    <div class="flex-fill">
                        <div class="{{ $notification->read_at ? '' : 'fw-bold font-weight-bold' }}">
                            {{ \App\Notifications\AppNotification::text($data, 'title') }}
                        </div>
                        <div class="text-muted">{{ \App\Notifications\AppNotification::text($data, 'body') }}</div>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="text-nowrap">
                        <a href="{{ route('admin.notifications.open', $notification->id) }}" class="btn btn-sm btn-outline-primary">{{ __('notifications.open') }}</a>
                        <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">{{ __('notifications.delete') }}</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">{{ __('notifications.empty') }}</div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection
