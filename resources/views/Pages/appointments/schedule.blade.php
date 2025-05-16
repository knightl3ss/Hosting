@extends('Layout.app')

@section('title', 'Appointment Schedule')

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

    .btn-edit {
        background-color: #EBF5FF;
        color: #1E40AF;
        border: 1px solid #BFDBFE;
        transition: all 0.2s;
    }

    .btn-edit:hover {
        background-color: #DBEAFE;
    }

    .btn-remove {
        background-color: #FEE2E2;
        color: #B91C1C;
        border: 1px solid #FECACA;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        background-color: #FEE2E2;
    }

    .progress-bar {
        height: 4px;
        background-color: #046C4E;
    }

    .progress-container {
        width: 100%;
        height: 4px;
        background-color: #E5E7EB;
        border-radius: 2px;
        overflow: hidden;
    }

    .error-list {
        max-height: 150px;
        overflow-y: auto;
        margin-top: 1rem;
        padding: 1rem;
        background-color: #FEE2E2;
        border: 1px solid #FECACA;
        border-radius: 8px;
    }

    .sample-csv-link {
        color: #1E40AF;
        text-decoration: none;
        font-size: 0.875rem;
    }

    .sample-csv-link:hover {
        text-decoration: underline;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-primary">Employee Schedule</h1>
        </div>
        <!-- Search Bar -->
        <form action="{{ route('appointment.schedule') }}" method="GET" class="d-flex" style="max-width:300px;">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by name or office..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>

    <!-- Page Content with Action Buttons -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <h5 class="card-title mb-3 mb-md-0 fw-bold text-dark">Employee Schedule</h5>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSchedModal">
                        <i class="fas fa-plus-circle me-2"></i> Add Personnel
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Form Validation Errors -->
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Filter Controls -->
        <div class="card-header bg-light py-3 border-top">
            <div class="d-flex align-items-center">
                <label for="appointmentTypeFilter" class="form-label fw-medium text-dark me-3 mb-0">Filter by Employeement Status:</label>
                <select class="form-select" id="appointmentTypeFilter" style="max-width: 200px;">
                    <option value="" {{ is_null($appointmentType) ? 'selected' : '' }}>All Status</option>
                    <optgroup label="Plantilla Records">
                        <option value="casual" {{ $appointmentType == 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="contractual" {{ $appointmentType == 'contractual' ? 'selected' : '' }}>Contractual</option>
                        <option value="coterminous" {{ $appointmentType == 'coterminous' ? 'selected' : '' }}>Coterminous</option>
                        <option value="coterminousTemporary" {{ $appointmentType == 'coterminousTemporary' ? 'selected' : '' }}>Coterminous - Temporary</option>
                        <option value="elected" {{ $appointmentType == 'elected' ? 'selected' : '' }}>Elected</option>
                        <option value="permanent" {{ $appointmentType == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="provisional" {{ $appointmentType == 'provisional' ? 'selected' : '' }}>Provisional</option>
                        <option value="regularPermanent" {{ $appointmentType == 'regularPermanent' ? 'selected' : '' }}>Regular Permanent</option>
                        <option value="substitute" {{ $appointmentType == 'substitute' ? 'selected' : '' }}>Substitute</option>
                        <option value="temporary" {{ $appointmentType == 'temporary' ? 'selected' : '' }}>Temporary</option>
                    </optgroup>
                    <optgroup label="Service Records">
                        <option value="job_order" {{ $appointmentType == 'job_order' ? 'selected' : '' }}>Job Order</option>
                    </optgroup>
                </select>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="px-3 py-3">#</th>
                            <th scope="col" class="px-3 py-3">Name</th>
                            <th scope="col" class="px-3 py-3">Position</th>
                            <th scope="col" class="px-3 py-3">Rate/Day</th>
                            <th scope="col" class="px-3 py-3">Period of Employment</th>
                            <th scope="col" class="px-3 py-3">Source of Fund</th>
                            <th scope="col" class="px-3 py-3">Office Assignment</th>
                            <th scope="col" class="px-3 py-3">Appointment Type</th>
                            <th scope="col" class="px-3 py-3">Status</th>
                            <th scope="col" class="px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pagedAppointments as $index => $appointment)
                        <tr>
                            <td class="px-3 py-3">{{ ($pagedAppointments->currentPage() - 1) * $pagedAppointments->perPage() + $index + 1 }}</td>
                            <td class="px-3 py-3 fw-medium">{{ $appointment->full_name }}</td>
                            <td class="px-3 py-3">{{ $appointment->position }}</td>
                            <td class="px-3 py-3">₱{{ number_format($appointment->rate_per_day, 2) }}/day</td>
                            <td class="px-3 py-3">
                                {{ \Carbon\Carbon::parse($appointment->employment_start)->format('M d, Y') }}
                                @if (!in_array($appointment->appointment_type ?? $appointment->type, ['casual', 'contractual', 'coterminous', 'coterminousTemporary', 'elected', 'permanent', 'provisional', 'regularPermanent', 'substitute', 'temporary']))
                                    to
                                    {{ \Carbon\Carbon::parse($appointment->employment_end)->format('M d, Y') }}
                                @endif
                            </td>
                            <td class="px-3 py-3">{{ $appointment->source_of_fund }}</td>
                            <td class="px-3 py-3">{{ $appointment->office_assignment }}</td>
                            <td class="px-3 py-3">
                                {{ $typeLabels[$appointment->appointment_type ?? $appointment->type] ?? ucfirst(str_replace('_', ' ', $appointment->appointment_type ?? $appointment->type ?? '-')) }}
                            </td>
                            <td class="px-3 py-3">
                                @php
                                $currentDate = \Carbon\Carbon::now();
                                $employmentEndDate = \Carbon\Carbon::parse($appointment->employment_end);
                                $status = $currentDate->greaterThan($employmentEndDate) ? 'Retired' : 'Ongoing';
                                // Job Order Completed logic
                                if (($appointment->appointment_type ?? $appointment->type ?? null) === 'job_order') {
                                    if (isset($appointment->status) && strtolower($appointment->status) === 'completed') {
                                        $status = 'Completed';
                                    } elseif ($currentDate->greaterThan($employmentEndDate)) {
                                        $status = 'Completed'; // or 'Expired' if you prefer
                                    } else {
                                        $status = 'Ongoing';
                                    }
                                }
                                @endphp
                                <span class="{{ $status == 'Ongoing' ? 'status-ongoing' : ($status == 'Completed' ? 'status-completed' : 'status-retired') }}">{{ $status }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-primary px-3 py-1 rounded text-sm fw-medium">
                                    <i class="fas fa-eye me-1"></i> View Record
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $pagedAppointments->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('Pages.appointments.modals.appointment-modals')

<!-- Include Validation Script for form validation -->
<script src="{{ asset('js/appointments/validation.js') }}"></script>

<!-- Include Schedule Script for modal and CSV handling -->
<script src="{{ asset('js/appointments/schedule.js') }}"></script>

@endsection