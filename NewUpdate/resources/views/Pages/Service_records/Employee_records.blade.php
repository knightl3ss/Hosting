@extends('layout.app')

<link rel="stylesheet" href="{{ asset('css/servicerecord/employee_records.css') }}">

@php $typeLabels = config('appointment_types'); @endphp

@section('content')
    <div class="container-fluid employee-record-container">
        <div class="header-container d-flex justify-content-between align-items-center">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h1 class="page-title mb-2 mb-md-0">
                    <i class="fas fa-user-tie me-2"></i>Employee Record
                </h1>
            </div>
            <div class="action-buttons">
                <!-- Return button at the bottom for better mobile experience -->
                <div class="d-flex justify-content-start">
                    <a href="{{ route('service_records') }}" class="btn btn-primary return-btn">
                        <i class="fas fa-arrow-left me-2"></i>Return to List
                    </a>
                </div>
                
                <button class="btn btn-light text-primary" onclick="window.location.href='{{ route('print_employee_records', ['id' => $employee->id]) }}'">
                    <i class="fas fa-print me-2"></i>Print Employee Records
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(!$isLatestAppointment)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Note:</strong> You are viewing an older version of this employee's appointment record. 
            The most recent appointment data may contain updated information.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card employee-details-card mb-4">
            <div class="employee-details-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h3 class="employee-name mb-2 mb-md-0">
                    Name: {{ $employee->getFullNameAttribute() }}
                </h3>
                <button class="btn btn-primary btn-responsive" data-bs-toggle="modal" data-bs-target="#addServiceRecordModal">
                    <i class="fas fa-plus-circle me-2"></i>Add Service Record
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table service-record-table">
                        <thead>
                            <tr>
                                <th colspan="2" style="border: 1px solid black;">Service</th>
                                <th colspan="3" style="border: 1px solid black;">Record of Appointment</th>
                                <th colspan="4" style="border: 1px solid black;">Office Entity/Leave Division Absence</th>
                                <th rowspan="2" style="border: 1px solid black;">Separation Date/Remarks</th>
                                <th rowspan="2" style="border: 1px solid black;">Service Status</th>
                                <th rowspan="2" style="border: 1px solid black;">Action</th>    
                            </tr>
                            <tr>
                                <th colspan="2" style="border: 1px solid black;">Inclusive Dates</th>
                                <th style="border: 1px solid black;">Designation</th>
                                <th style="border: 1px solid black;">Status Salary</th>
                                <th style="border: 1px solid black; position: relative;">
                                    <div class="d-flex align-items-center">
                                        <span id="salaryHeader" style="white-space: nowrap; fw-bold">Salary Per</span>
                                        @php
                                            $singleType = null;
                                            if ($serviceRecords->count() > 0) {
                                                $types = $serviceRecords->pluck('status')->map(function($status) {
                                                    return strtolower($status ?? '');
                                                })->unique();
                                                if ($types->count() === 1) {
                                                    $singleType = $types->first();
                                                }
                                            }
                                        @endphp
                                        @if($singleType === 'job_order' || $singleType === 'joborder' || $singleType === 'job order')
                                            <select class="form-select form-select-sm ms-2 fw-bold" id="salaryFormat" onchange="updateSalaryFormat(this.value)" style="min-width: 100px;">
                                                <option value="daily" selected>Daily</option>
                                            </select>
                                        @elseif($singleType)
                                            <select class="form-select form-select-sm ms-2 fw-bold" id="salaryFormat" onchange="updateSalaryFormat(this.value)" style="min-width: 100px;">
                                                <option value="annum" {{ (old('salary_format', 'annum') == 'annum') ? 'selected' : ''}}>Annum</option>
                                                <option value="monthly" {{ (old('salary_format', 'annum') == 'monthly') ? 'selected' : '' }}>Monthly</option>
                                                
                                            </select>
                                        @else
                                            <select class="form-select form-select-sm ms-2 fw-bold" id="salaryFormat" onchange="updateSalaryFormat(this.value)" style="min-width: 100px;">
                                                <option value="annum" {{ (old('salary_format', 'annum') == 'annum') ? 'selected' : ''}}>Annum</option>
                                                <option value="monthly" {{ (old('salary_format', 'annum') == 'monthly') ? 'selected' : '' }}>Monthly</option>
                                                <option value="daily" {{ (old('salary_format', 'annum') == 'daily') ? 'selected' : '' }}>Daily</option>
                                            </select>
                                        @endif
                                    </div>
                                </th>
                                
                                <th style="border: 1px solid black;">Station Place</th>
                                <th colspan="2" style="border: 1px solid black;">Branch</th>
                                <th rowspan="2" style="border: 1px solid black;">Without Pay</th>
                                
                                
                               
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($serviceRecords) && count($serviceRecords) > 0)
                                @foreach($serviceRecords as $record)
    <tr>
        <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($record->date_from)->format('F j, Y') }}</td>
        <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($record->date_to)->format('F j, Y') }}</td>
        <td style="border: 1px solid black;">{{ $record->designation }}</td>
        <td style="border: 1px solid black;">
            {{-- Status Salary (Appointment Type Label) --}}
            {{ $typeLabels[$record->status] ?? ucwords(str_replace('_', ' ', $record->status)) }}{{ isset($record->payment_frequency) && !empty($record->payment_frequency) ? '/'.$record->payment_frequency : '' }}
        </td>
        <td style="border: 1px solid black;" data-salary="{{ $record->salary }}">{{ number_format($record->salary, 2) }}</td>
        <td colspan="4" style="border: 1px solid black;">{{ $record->station }}</td>
        <td style="border: 1px solid black;">
            {{ $record->separation_date ? $record->separation_date : '-' }}
        </td>
        @if($loop->last)
            <td style="border: 1px solid black;">
                @php
                    // Define status color mapping
                    $statusClass = '';
                    $badgeClass = '';
                    if($record->service_status == 'In Service') {
                        $statusClass = 'status-in-service';
                        $badgeClass = 'bg-success';
                    } elseif($record->service_status == 'Suspension') {
                        $statusClass = 'status-suspension';
                        $badgeClass = 'bg-warning text-dark';
                    } elseif($record->service_status == 'Not in Service') {
                        $statusClass = 'status-not-in-service';
                        $badgeClass = 'bg-danger';
                    }
                @endphp
                <span class="status-cell {{ $statusClass }}">
                    <span class="badge {{ $badgeClass }}">
                        {{ $record->service_status }}
                    </span>
                </span>
            </td>
            <td style="border: 1px solid black;">
                <button type="button" class="btn btn-sm btn-primary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editServiceRecordModal{{ $record->id }}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteServiceRecordModal{{ $record->id }}">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        @else
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        @endif
    </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="12" class="text-center">No service records found. Add a new service record using the button above.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Delete Service Record Modals -->
    @foreach($serviceRecords as $record)
    <div class="modal fade" id="deleteServiceRecordModal{{ $record->id }}" tabindex="-1" aria-labelledby="deleteServiceRecordModalLabel{{ $record->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteServiceRecordModalLabel{{ $record->id }}">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this service record? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('delete_service_record', ['id' => $record->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Edit Service Record Modals - moved to ServiceRecordModal.blade.php -->
    @include('Pages.Service_records.ServiceRecordModal')
@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Date Range Validation for Service Records ---
    function validateDateOrder() {
        // For all visible modals (Add/Edit)
        document.querySelectorAll('.modal.show').forEach(function(modal) {
            const dateFrom = modal.querySelector('input[name="date_from"]');
            const dateTo = modal.querySelector('input[name="date_to"]');
            if (dateFrom && dateTo) {
                // --- Set min for Add Service Record modal ---
                const serviceRecords = @json($serviceRecords ?? []);
                if (modal.id === 'addServiceRecordModal') {
                    let latestDateTo = null;
                    serviceRecords.forEach(function(record) {
                        if (record.date_to && (!latestDateTo || record.date_to > latestDateTo)) {
                            latestDateTo = record.date_to;
                        }
                    });
                    if (latestDateTo) {
                        // Add 1 day to latestDateTo
                        const minFrom = new Date(latestDateTo);
                        minFrom.setDate(minFrom.getDate() + 1);
                        const minFromStr = minFrom.toISOString().slice(0, 10);
                        dateFrom.min = minFromStr;
                        dateTo.min = minFromStr;
                    }
                }
                // --- Set min/max for Edit Service Record modal ---
                if (modal.id.startsWith('editServiceRecordModal')) {
                    // Extract record id from modal id
                    const recordId = modal.id.replace('editServiceRecordModal', '');
                    // Find index of this record in serviceRecords
                    const idx = serviceRecords.findIndex(r => String(r.id) === String(recordId));
                    if (idx !== -1) {
                        // Previous record's date_to
                        if (idx > 0 && serviceRecords[idx-1].date_to) {
                            const prevTo = new Date(serviceRecords[idx-1].date_to);
                            prevTo.setDate(prevTo.getDate() + 1);
                            dateFrom.min = prevTo.toISOString().slice(0, 10);
                        } else {
                            dateFrom.removeAttribute('min');
                        }
                        // Next record's date_from
                        if (idx < serviceRecords.length - 1 && serviceRecords[idx+1].date_from) {
                            const nextFrom = new Date(serviceRecords[idx+1].date_from);
                            nextFrom.setDate(nextFrom.getDate() - 1);
                            dateTo.max = nextFrom.toISOString().slice(0, 10);
                        } else {
                            dateTo.removeAttribute('max');
                        }
                    }
                }
                // --- Set date_to min according to date_from ---
                dateFrom.addEventListener('change', function() {
                    if (dateFrom.value) {
                        dateTo.min = dateFrom.value;
                        if (dateTo.value && dateTo.value < dateFrom.value) {
                            dateTo.value = '';
                        }
                    }
                });
                dateTo.addEventListener('change', function() {
                    if (dateFrom.value && dateTo.value < dateFrom.value) {
                        alert('The TO date cannot be earlier than the FROM date.');
                        dateTo.value = '';
                        dateTo.focus();
                    }
                });
            }
        });
    }
    // Listen for modal show events (Bootstrap 5)
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('shown.bs.modal', validateDateOrder);
    });
    // For already visible modals on page load (rare)
    validateDateOrder();

    // Initialize any form validation or date pickers here if needed
    
    // Close alert messages after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        });
    }, 5000);

    // If any service record is job_order, set salaryFormat to daily
    const serviceRecords = @json($serviceRecords ?? []);
    let hasJobOrder = false;
    serviceRecords.forEach(record => {
        if (record.status === 'job_order' || record.status === 'joborder' || record.status === 'Job Order') {
            hasJobOrder = true;
        }
    });
    if (hasJobOrder) {
        const salaryFormatSelect = document.getElementById('salaryFormat');
        if (salaryFormatSelect) {
            salaryFormatSelect.value = 'daily';
            updateSalaryFormat('daily');
        }
    }
});

function updateSalaryFormat(format) {
    const header = document.getElementById('salaryHeader');
    header.textContent = 'Salary Per';
    
    // Update the salary values in the table
    const salaryCells = document.querySelectorAll('td[data-salary]');
    salaryCells.forEach(cell => {
        const originalSalary = parseFloat(cell.dataset.salary);
        if (format === 'monthly') {
            cell.textContent = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(originalSalary / 12);
        } else if (format === 'daily') {
            cell.textContent = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(originalSalary / 365);
        } else {
            cell.textContent = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(originalSalary);
        }
    });
}
</script>


@push('styles')
<style>
    /* Service Status Styling */
    .status-in-service {
        font-weight: bold;
    }
    
    .status-suspension {
        font-style: italic;
    }
    
    .status-not-in-service {
        text-decoration: line-through;
        opacity: 0.8;
    }
    
    /* Make sure badges are properly visible */
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
    }
</style>
@endpush
