@extends('Layout.app')

@section('title', 'Appointment Page')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-primary">Employee Management</h1>
        </div>
    </div>
    
    <!-- Menu Cards Section -->
    <div class="row g-4">
        <!-- Appointment Schedule Card -->
        <div class="col-md-6">
            <a href="{{ route('appointment.schedule') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="flex-shrink-0 me-3 bg-light rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-calendar-alt fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h3 class="card-title fs-5 fw-bold text-dark mb-1">Employee Schedule</h3>
                            <p class="card-text text-muted mb-0">Manage your appointment calendar and scheduling</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transform: translateY(-3px);
    }
    
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection