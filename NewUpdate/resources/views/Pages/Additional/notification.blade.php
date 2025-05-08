@extends('Layout.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Notifications</h2>
    <div class="card">
        <div class="card-body">
            @if(auth()->user()->notifications->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach(auth()->user()->notifications as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-bell me-2"></i>
                                {{ $notification->data['message'] ?? 'Notification' }}
                                <span class="text-muted small ms-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            @if($notification->unread())
                                <span class="badge bg-warning">Unread</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <form method="POST" action="{{ route('notifications.markAllRead') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-primary">Mark all as read</button>
                </form>
            @else
                <p class="text-center">No notifications found.</p>
            @endif
        </div>
    </div>
</div>
@endsection