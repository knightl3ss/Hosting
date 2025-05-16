@extends('Layout.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas {{ $icon ?? 'fa-file-alt' }} {{ $iconColor ?? 'text-primary' }} me-2"></i>{{ $title ?? 'Employee Report' }}
        </h2>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print Report
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <!-- Filter Controls -->
    <div class="card mb-4 filter-section">
        <div class="card-body">
            <form id="filterForm" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="appointment_type" class="form-label">Appointment Type</label>
                    <select name="appointment_type" id="appointment_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($appointmentTypes as $value => $label)
                            <option value="{{ $value }}" {{ request('appointment_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="office_assignment" class="form-label">Office Assignment</label>
                    <input type="text" name="office_assignment" id="office_assignment" class="form-control" value="{{ request('office_assignment') }}">
                </div>
                <div class="col-md-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" name="position" id="position" class="form-control" value="{{ request('position') }}">
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Employee Details</h5>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>Generated on: {{ date('F d, Y') }}
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Appointment Type</th>
                            <th>Office Assignment</th>
                            <th>Gender</th>
                            <th>Years of Service</th>
                            <th>Service Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-{{ $employee->gender == 'male' ? 'primary' : 'danger' }} bg-opacity-10 me-2">
                                        <i class="fas fa-{{ $employee->gender == 'male' ? 'male' : 'female' }} text-{{ $employee->gender == 'male' ? 'primary' : 'danger' }}"></i>
                                    </div>
                                    <span>{{ $employee->first_name }} {{ $employee->last_name }}</span>
                                </div>
                            </td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                <span class="badge bg-info text-white">
                                    {{ $appointmentTypes[$employee->appointment_type] ?? ucfirst($employee->appointment_type) }}
                                </span>
                            </td>
                            <td>{{ $employee->office_assignment }}</td>
                            <td>
                                <span class="badge {{ $employee->gender == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                    {{ ucfirst($employee->gender) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $dateHired = \Carbon\Carbon::parse($employee->employment_start ?? $employee->date_hired);
                                    $yearsOfService = (int)$dateHired->diffInYears(now());
                                @endphp
                                <span class="badge bg-info">{{ $yearsOfService }} {{ Str::plural('year', $yearsOfService) }}</span>
                            </td>
                            <td>
                                @php
                                    $latestServiceRecord = DB::table('service_records')
                                        ->where('employee_id', $employee->id)
                                        ->orderBy('updated_at', 'desc')
                                        ->first();
                                    $status = $latestServiceRecord ? $latestServiceRecord->service_status : 'Not specified';
                                    $statusClass = '';
                                    if($status == 'In Service') $statusClass = 'bg-success';
                                    elseif($status == 'Suspension') $statusClass = 'bg-warning text-dark';
                                    elseif($status == 'Not in Service') $statusClass = 'bg-danger';
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-2"></i>No employees found
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
@endsection

@push('styles')
<style>
    /* Hide filter section when printing */
    @media print {
        /* Hide everything except what we want to print */
        body * {
            visibility: hidden;
        }
        
        /* Only show the title and table */
        .container-fluid h2,
        .container-fluid .card-header h5,
        .container-fluid .card-header .text-muted,
        .container-fluid .table-responsive,
        .container-fluid .table-responsive * {
            visibility: visible;
            color: black !important;
        }
        
        /* Remove all scrollbars */
        ::-webkit-scrollbar {
            display: none !important;
        }
        
        * {
            overflow: visible !important;
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
        }
        
        /* Hide all navigation elements */
        .navbar, .btn, .sidebar, footer, .filter-section {
            display: none !important;
        }
        
        /* Reset container padding */
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        
        /* Remove card styling but keep structure */
        .card {
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
        }
        
        .card-header {
            background-color: white !important;
            border-bottom: 1px solid #ddd !important;
            padding-bottom: 10px !important;
        }
        
        /* Make table take full width */
        .table-responsive {
            overflow: visible !important;
            width: 100% !important;
        }
        
        /* Make all badges print with black text on white background with a border */
        .badge {
            background-color: white !important;
            color: black !important;
            border: 1px solid #000 !important;
        }
        
        /* Ensure all text in table cells is black */
        table th, table td {
            color: black !important;
        }
        
        /* Make sure all icons are black */
        .fas, .far, .fab, .fa {
            color: black !important;
        }
        
        /* Ensure headings are black */
        h1, h2, h3, h4, h5, h6 {
            color: black !important;
        }
    }
    
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush
