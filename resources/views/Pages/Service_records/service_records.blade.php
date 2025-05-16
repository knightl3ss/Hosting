@extends('Layout.app')

@php $typeLabels = config('appointment_types'); @endphp

@push('styles')
<link href="{{ asset('css/servicerecord/service_records.css') }}" rel="stylesheet">
<style>
    /* Admin column styling */
    .admin-info {
        display: flex;
        flex-direction: column;
        text-align: left;
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .admin-name {
        margin-bottom: 0.25rem;
    }
    
    .admin-time {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    /* Purpose icon styling */
    .purpose-icon {
        color: #4e73df;
        transition: all 0.2s ease;
    }
    
    .purpose-icon:hover {
        color: #2e59d9;
        transform: scale(1.1);
    }
    
    .purpose-btn {
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }
    
    .purpose-btn:hover {
        background-color: #eaecf4;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush

@section('content')
    <!-- Page Content -->
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-primary">Service Records</h1>
            </div>
            <!-- Search Bar -->
            <form action="{{ route('service_records.filter') }}" method="GET" class="d-flex" style="max-width:300px;">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by name or office..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <div class="card shadow-lg border-0">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 text-primary">
                        <i class="fas fa-user-friends me-2"></i>Employee Service Records
                    </h4>
                </div>
            </div>
            <div class="card-header bg-light py-3 border-top mb-3">
                <div class="d-flex align-items-center">
                    <label for="employmentStatusFilter" class="form-label fw-medium text-dark me-3 mb-0">Filter by Employment Status:</label>
                    <form method="GET" action="{{ route('service_records.filter') }}" class="d-inline-block w-auto">
                        <select class="form-select" id="employmentStatusFilter" name="appointment_type" style="max-width: 200px;" onchange="this.form.submit()">
                            <option value="" {{ request('appointment_type') == null ? 'selected' : '' }}>All Status</option>
                            <optgroup label="Plantilla Records">
                                <option value="casual" {{ request('appointment_type') == 'casual' ? 'selected' : '' }}>Casual</option>
                                <option value="contractual" {{ request('appointment_type') == 'contractual' ? 'selected' : '' }}>Contractual</option>
                                <option value="coterminous" {{ request('appointment_type') == 'coterminous' ? 'selected' : '' }}>Coterminous</option>
                                <option value="coterminousTemporary" {{ request('appointment_type') == 'coterminousTemporary' ? 'selected' : '' }}>Coterminous - Temporary</option>
                                <option value="elected" {{ request('appointment_type') == 'elected' ? 'selected' : '' }}>Elected</option>
                                <option value="permanent" {{ request('appointment_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="provisional" {{ request('appointment_type') == 'provisional' ? 'selected' : '' }}>Provisional</option>
                                <option value="regularPermanent" {{ request('appointment_type') == 'regularPermanent' ? 'selected' : '' }}>Regular Permanent</option>
                                <option value="substitute" {{ request('appointment_type') == 'substitute' ? 'selected' : '' }}>Substitute</option>
                                <option value="temporary" {{ request('appointment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                            </optgroup>
                            <optgroup label="Service Records">
                                <option value="job_order" {{ request('appointment_type') == 'job_order' ? 'selected' : '' }}>Job Order</option>
                            </optgroup>
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 service-record-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center action-column" style="width: 120px;">Item No.</th>
                                <th>Employee Name</th>
                                <th>Gender</th>
                                <th>Employment Status</th>
                                <th>Office Assignment</th>
                                <th>Remarks</th>
                                <th>Admin</th>
                                <th class="text-center">Record Details</th>
                                <th class="text-center">Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hasEmployees = ($employees && (is_array($employees) || $employees instanceof Countable)) ? count($employees) > 0 : false;
                            @endphp
                            
                            <!-- Display Job Order and Temporary employees -->
                            @if($hasEmployees)
                                @foreach ($employees as $employee)
                                    @php
                                        $appointmentType = $employee->appointment_type;
                                        // For job_order, use employee_id; for others, use item_no
                                        $uniqueKey = $appointmentType === 'job_order' ? $employee->employee_id : $employee->item_no;
                                        $activeAppointment = null;
                                        if ($appointmentType === 'job_order') {
                                            $activeAppointment = App\Models\AppointmentModel\Appointment::where('employee_id', $employee->employee_id)
                                                ->where('is_active', true)
                                                ->first();
                                            if (!$activeAppointment) {
                                                $activeAppointment = App\Models\AppointmentModel\Appointment::where('employee_id', $employee->employee_id)
                                                    ->latest('updated_at')
                                                    ->first();
                                            }
                                        } else {
                                            $activeAppointment = App\Models\AppointmentModel\Appointment::where('item_no', $employee->item_no)
                                                ->where('is_active', true)
                                                ->first();
                                            if (!$activeAppointment) {
                                                $activeAppointment = App\Models\AppointmentModel\Appointment::where('item_no', $employee->item_no)
                                                    ->latest('updated_at')
                                                    ->first();
                                            }
                                        }
                                        $appointmentText = $typeLabels[$appointmentType] ?? ucwords(str_replace('_', ' ', $appointmentType));
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            @if ($appointmentType !== 'job_order')
                                                {{ $employee->item_no }}
                                            @else
                                                <!-- {{ $employee->employee_id }} -->
                                            @endif
                                        </td>
                                        <td>{{ $employee->full_name }}</td>
                                        <td>{{ ucfirst($employee->gender) }}</td>
                                        <td>{{ $appointmentText }}</td>
                                        <td>{{ $employee->office_assignment ?? 'Not Assigned' }}</td>
                                        <td>
                                            @php
                                                // Get the latest service record for this employee
                                                $latestServiceRecord = DB::table('service_records')
                                                    ->where('employee_id', $employee->id)
                                                    ->orderBy('date_to', 'desc')
                                                    ->first();
                                                
                                                // Determine service status and appropriate styling
                                                $serviceStatus = '';
                                                $statusClass = '';
                                                $displayStatus = '';
                                                
                                                if($latestServiceRecord && isset($latestServiceRecord->service_status)) {
                                                    $serviceStatus = $latestServiceRecord->service_status;
                                                    $displayStatus = $latestServiceRecord->service_status; // Keep original text
                                                } elseif(isset($employee->service_status)) {
                                                    $serviceStatus = $employee->service_status;
                                                    $displayStatus = $employee->service_status; // Keep original text
                                                } else {
                                                    $serviceStatus = 'not_specified';
                                                    $displayStatus = 'Not specified';
                                                }
                                                
                                                // Map service status to appropriate Bootstrap class
                                                switch(strtolower($serviceStatus)) {
                                                    case 'active':
                                                    case 'in service':
                                                        $statusClass = 'bg-success';
                                                        break;
                                                    case 'inactive':
                                                    case 'not in service':
                                                        $statusClass = 'bg-danger';
                                                        break;
                                                    case 'suspension':
                                                        $statusClass = 'bg-warning text-dark';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-secondary';
                                                }
                                            @endphp
                                            
                                            <span class="badge {{ $statusClass }} status-badge">
                                                {{ $displayStatus }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                // Get the latest service record for this employee
                                                $latestServiceRecord = DB::table('service_records')
                                                    ->where('employee_id', $employee->id)
                                                    ->orderBy('date_to', 'desc')
                                                    ->first();
                                                
                                                // Get admin info from the service record if available
                                                $adminName = 'Not available';
                                                $updateTime = null;
                                                
                                                if($latestServiceRecord) {
                                                    if(property_exists($latestServiceRecord, 'updated_by') && $latestServiceRecord->updated_by) {
                                                        $admin = DB::table('users')->find($latestServiceRecord->updated_by);
                                                        $adminName = $admin ? $admin->first_name . ' ' . $admin->last_name : 'Unknown';
                                                        $updateTime = $latestServiceRecord->updated_at;
                                                    } elseif(property_exists($latestServiceRecord, 'created_by') && $latestServiceRecord->created_by) {
                                                        $admin = DB::table('users')->find($latestServiceRecord->created_by);
                                                        $adminName = $admin ? $admin->first_name . ' ' . $admin->last_name : 'Unknown';
                                                        $updateTime = $latestServiceRecord->created_at;
                                                    }
                                                } elseif(property_exists($employee, 'updated_by') && $employee->updated_by) {
                                                    $admin = DB::table('users')->find($employee->updated_by);
                                                    $adminName = $admin ? $admin->first_name . ' ' . $admin->last_name : 'Unknown';
                                                    $updateTime = $employee->updated_at;
                                                }
                                            @endphp
                                            
                                            @if($adminName != 'Not available')
                                                <div class="admin-info">
                                                    <div class="admin-name">
                                                        <i class="fas fa-user-edit me-1"></i>
                                                        {{ $adminName }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">Not updated</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('Employee_records', ['id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                                View Full Record
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('record_purpose', ['id' => $employee->id]) }}" class="purpose-btn" data-bs-toggle="tooltip" title="Set Record Purpose">
                                                <i class="fas fa-clipboard-list purpose-icon"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            
                            @if(!$hasEmployees)
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No employees found.
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Add pagination links -->
                @if($employees->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $employees->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchEmployee');
    const tableBody = document.querySelector('.table tbody');

    searchInput.addEventListener('input', debounce(function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            window.location.href = `{{ route('service_records.filter') }}?search=${searchTerm}&page=1`;
        }
    }, 500));

    // Add debounce function to prevent excessive requests
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
