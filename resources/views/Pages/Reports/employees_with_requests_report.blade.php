@extends('Layout.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-clipboard-list text-primary me-2"></i>Employees Who Have Made Requests Report
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

    <div class="card">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Request Details</h5>
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
                            <th>Date Hired</th>
                            <th class="text-center">Number of Requests</th>
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
                            <td>{{ \Carbon\Carbon::parse($employee->employment_start ?? $employee->date_hired)->format('M d, Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">
                                    {{ \App\Models\ServiceRecordModel\RecordPurpose::where('employee_id', $employee->id)->count() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
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
