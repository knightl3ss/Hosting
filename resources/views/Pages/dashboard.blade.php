@extends('Layout.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center">
                        <label for="appointment_type" class="form-label fw-medium text-dark me-3 mb-0">Filter by Appointment Type:</label>
                        <select name="appointment_type" id="appointment_type" class="form-select w-auto me-3" onchange="this.form.submit()">
                            <option value="">All Appointment Types</option>
                            @foreach($appointmentTypes as $key => $label)
                                <option value="{{ $key }}" {{ $selectedAppointmentType == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @if($selectedAppointmentType)
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filter
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Admin Card -->
        <div class="col-md-3 col-lg-2">
            <div class="card h-100 bg-light stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle  p-3 me-3">
                            <i class="fas fa-user-shield text-primary"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $adminCount ?? 0 }}</h3>
                            <p class="text-muted mb-0">Admin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Male Card -->
        <div class="col-md-3 col-lg-2">
            <div class="card h-100 bg-light stat-card" data-modal="maleEmployeesModal">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 me-3">
                            <i class="fas fa-male text-warning"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $maleCount ?? 0 }}</h3>
                            <p class="text-muted mb-0">Male</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Female Card -->
        <div class="col-md-3 col-lg-2">
            <div class="card h-100 bg-light stat-card" data-modal="femaleEmployeesModal">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle  p-3 me-3">
                            <i class="fas fa-female text-danger"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $femaleCount ?? 0 }}</h3>
                            <p class="text-muted mb-0">Female</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Service Card -->
        <div class="col-md-3 col-lg-2">
            <div class="card h-100 bg-light stat-card" data-modal="inServiceEmployeesModal">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 me-3">
                            <i class="fas fa-user-check text-success"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $inServiceCount ?? 0 }}</h3>
                            <p class="text-muted mb-0">In Service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspension Card -->
        <div class="col-md-3 col-lg-2">
            <div class="card h-100 bg-light stat-card" data-modal="suspensionEmployeesModal">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 me-3">
                            <i class="fas fa-user-clock text-warning"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $suspensionCount ?? 0 }}</h3>
                            <p class="text-muted mb-0">Suspension</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Not in Service Card -->
        <div class="col-md-3 col-lg-2">
            <div class="card h-100 bg-light stat-card" data-modal="notInServiceEmployeesModal">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle  p-3 me-3">
                            <i class="fas fa-user-times text-danger"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $notInServiceCount ?? 0 }}</h3>
                            <p class="text-muted mb-0">Not in Service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Employees by Years of Service (Summary Table) --}}
    <div class="card mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-chart-bar me-2"></i>Employees by Years of Service
            </h5>
        </div>
        <div class="card-body">
            <x-years-of-service-chart :serviceGroups="$serviceGroups" />
        </div>
    </div>

    {{-- Employees Who Have Made Requests --}}
    <div class="card mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-clipboard-list me-2"></i>Employees Who Have Made Requests
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Name</th>
                            <th class="fw-semibold">Position</th>
                            <th class="fw-semibold">Appointment Type</th>
                            <th class="fw-semibold">Date Hired</th>
                            <th class="fw-semibold text-center">Number of Requests</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeesWithRequests as $employee)
                            <tr class="text-center">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle    bg-opacity-10 me-2">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <span>{{ $employee->full_name ?? $employee->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $employee->position ?? 'Not specified' }}</td>
                                <td>
                                    <span class="badge  text-info px-3 py-2">
                                        {{ $typeLabels[$employee->appointment_type] ?? ucfirst($employee->appointment_type) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($employee->employment_start)->format('F d, Y h:i A') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        {{ \App\Models\ServiceRecordModel\RecordPurpose::where('employee_id', $employee->id)->count() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No employees with requests found
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Completed Record Purposes --}}
    <div class="card mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-check-circle me-2"></i>Recent Completed Record Purposes
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Employee</th>
                            <th class="fw-semibold">Purpose Type</th>
                            <th class="fw-semibold">Details</th>
                            <th class="fw-semibold">Date Requested</th>
                            <th class="fw-semibold">Date Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCompletedPurposes as $purpose)
                            <tr class="text-center">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            <i class="fas fa-user-check text-success"></i>
                                        </div>
                                        @if($purpose->employee)
                                            <span>{{ $purpose->employee->first_name }} {{ $purpose->employee->last_name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge text-primary px-3 py-2">
                                        {{ ucfirst($purpose->purpose_type) }}
                                    </span>
                                </td>
                                <td>{{ $purpose->purpose_details ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($purpose->requested_date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge text-success px-3 py-2">
                                        {{ \Carbon\Carbon::parse($purpose->updated_at)->format('M d, Y h:i A') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No completed record purposes found
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('Pages.dashboard_modals')

@push('scripts')
<script>
    // Make the entire card clickable
    document.querySelectorAll('.stat-card').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal');
            if (modalId) {
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            }
        });
    });
</script>
@endpush
@endsection

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 10px;
    }
    
    .stat-card {
        transition: transform 0.2s ease-in-out;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        font-weight: 600;
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,0.01);
    }
    
    .table-light th {
        background-color: #f8f9fa;
    }
    
    .text-primary {
        color: #4e73df !important;
    }
    
    .bg-primary {
        background-color: #4e73df !important;
    }
    
    .bg-success {
        background-color: #1cc88a !important;
    }
    
    .bg-info {
        background-color: #36b9cc !important;
    }
    
    .bg-warning {
        background-color: #f6c23e !important;
    }
    
    .bg-danger {
        background-color: #e74a3b !important;
    }
    
    .bg-secondary {
        background-color: #858796 !important;
    }
</style>
@endpush
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modalElement => {
            new bootstrap.Modal(modalElement);
        });

        // Make the entire card clickable
        document.querySelectorAll('.stat-card').forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal');
                if (modalId) {
                    const modalElement = document.getElementById(modalId);
                    if (modalElement) {
                        try {
                            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                            modal.show();
                        } catch (error) {
                            console.error('Error showing modal:', error);
                        }
                    }
                }
            });
        });

        // Add click event to the numbers and icons as well
        document.querySelectorAll('.stat-card .card-body').forEach(cardBody => {
            cardBody.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent double triggering
                const card = this.closest('.stat-card');
                const modalId = card.getAttribute('data-modal');
                if (modalId) {
                    const modalElement = document.getElementById(modalId);
                    if (modalElement) {
                        try {
                            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                            modal.show();
                        } catch (error) {
                            console.error('Error showing modal:', error);
                        }
                    }
                }
            });
        });
    });
</script>
