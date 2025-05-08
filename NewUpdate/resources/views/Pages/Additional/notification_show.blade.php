@extends('Layout.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Notification Details</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <i class="fas fa-bell me-2"></i>
                <strong>{{ $notification->data['message'] ?? 'Notification' }}</strong>
            </div>
            <div class="text-muted small mb-3">
                Received: {{ $notification->created_at->format('F j, Y h:i A') }}
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
