@extends('Layout.app')

@section('title', 'Appointment Details')

@section('content')
<style>
    .status-ongoing {
        background-color: #DEF7EC;
        color: #046C4E;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-retired {
        background-color: #FEF3C7;
        color: #92400E;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-completed {
        background-color: #C6F4D6;
        color: #2E865F;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .detail-card {
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .detail-label {
        color: #6B7280;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .detail-value {
        color: #111827;
        font-weight: 500;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-primary">Employee Details</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('appointment.schedule') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Back to Schedule
            </a>
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#editPersonalDetailsModal">
                <i class="fas fa-edit me-2"></i> Edit Personal Details
            </button>
            <button type="button" class="btn btn-danger d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                <i class="fas fa-trash-alt me-2"></i> Delete Employee
            </button>
        </div>
    </div>

    <!-- Employee Details Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark">Employee Information</h5>
                <div class="d-flex align-items-center">
                    @php
                    $currentDate = \Carbon\Carbon::now();
                    $employmentEndDate = \Carbon\Carbon::parse($appointment->employment_end);
                    $status = $currentDate->greaterThan($employmentEndDate) ? 'Retired' : 'Ongoing';
                    @endphp
                    
                    <span class="{{ $status == 'Ongoing' ? 'status-ongoing' : 'status-retired' }}">{{ $status }}</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $appointment->full_name }}" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="name" class="form-label">Date of Birth</label>
                        <input type="text" class="form-control" id="dob" name="birthday" value="{{ \Carbon\Carbon::parse($appointment->birthday)->format('M d, Y') }}" readonly>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label for="name" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age" value="{{ $appointment->age }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="{{ $appointment->location }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position" value="{{ $appointment->position }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        @if ($appointment->appointment_type === 'job_order')
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ $appointment->employee_id }}" readonly>
                        @else
                            <label for="item_no" class="form-label">Item No</label>
                            <input type="text" class="form-control" id="item_no" name="item_no" value="{{ $appointment->item_no }}" readonly>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="rate_per_day" class="form-label">Rate/Day</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" class="form-control" id="rate_per_day" name="rate_per_day" value="₱{{ number_format($appointment->rate_per_day, 2) }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="employment_start" class="form-label">Period of Employment</label>
                        <input type="text" class="form-control" id="employment_start" name="employment_start" value="{{ \Carbon\Carbon::parse($appointment->employment_start)->format('M d, Y') }}@if (!in_array($appointment->appointment_type ?? $appointment->type, ['casual', 'contractual', 'coterminous', 'coterminousTemporary', 'elected', 'permanent', 'provisional', 'regularPermanent', 'substitute', 'temporary'])) to {{ \Carbon\Carbon::parse($appointment->employment_end)->format('M d, Y') }}@endif" readonly>
                        </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="office_assignment" class="form-label">Office Assignment</label>
                        <input type="text" class="form-control" id="office_assignment" name="office_assignment" value="{{ $appointment->office_assignment }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="source_of_fund" class="form-label">Source of Fund</label>
                        <input type="text" class="form-control" id="source_of_fund" name="source_of_fund" value="{{ $appointment->source_of_fund }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="created_at" class="form-label">Date Created</label>
                        <input type="text" class="form-control" id="created_at" name="created_at" value="{{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, Y h:i A') }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="updated_at" class="form-label">Date Updated</label>
                        <input type="text" class="form-control" id="updated_at" name="updated_at" value="{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y h:i A') }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('Pages.appointments.AppointmentModal')
</div>

@endsection
