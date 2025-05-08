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
            <h1 class="text-primary">Appointment Schedule</h1>
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
                <h5 class="card-title mb-3 mb-md-0 fw-bold text-dark">Appointment Schedule</h5>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSchedModal">
                        <i class="fas fa-plus-circle me-2"></i> Add Personnel
                    </button>
                    <!-- <button type="button" class="btn btn-outline-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importCsvModal" 
                            data-bs-toggle="tooltip" data-bs-placement="bottom" 
                            title="Import multiple appointments from a CSV file. Required columns: first_name, last_name, position, rate_per_day, employment_start, appointment_type, employee_id">
                        <i class="fas fa-file-csv me-2"></i> Import CSV
                    </button> -->
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

<!-- Modal for Adding Schedule -->
<div id="addSchedModal" class="modal fade" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i> Add New Appointment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <form action="{{ route('appointment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($errors->has('employee_id'))
                        <div class="alert alert-danger py-2">{{ $errors->first('employee_id') }}</div>
                    @endif
                    @if ($errors->has('item_no'))
                        <div class="alert alert-danger py-2">{{ $errors->first('item_no') }}</div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="appointment_type" class="form-label fw-semibold">Employment Status</label>
                            <select name="appointment_type" id="appointment_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <optgroup label="Plantilla Records">
                                    <option value="casual">Casual</option>
                                    <option value="contractual">Contractual</option>
                                    <option value="coterminous">Coterminous</option>
                                    <option value="coterminousTemporary">Coterminous - Temporary</option>
                                    <option value="elected">Elected</option>
                                    <option value="permanent">Permanent</option>
                                    <option value="provisional">Provisional</option>
                                    <option value="regularPermanent">Regular Permanent</option>
                                    <option value="substitute">Substitute</option>
                                    <option value="temporary">Temporary</option>
                                </optgroup>
                                <optgroup label="Service Records">
                                    <option value="job_order">Job Order</option>
                                </optgroup>
                            </select>
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i> Job Order will be directed to Service Records. All other types will go to Plantilla.
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="employeeId" class="form-label fw-semibold">
                                <span id="id_label">Employee ID/Item No</span><span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="employeeId" name="employee_id" required>
                            <small class="form-text text-muted" id="id_help">
                                For Job Order: Use Employee ID. For Plantilla Records: Use Item No.
                            </small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 text-secondary"><i class="fas fa-user me-2"></i>Personal Details</h6>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="firstName" class="form-label fw-semibold">First Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="middleName" class="form-label fw-semibold">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" name="middle_name">
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="lastName" class="form-label fw-semibold">Last Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="extensionName" class="form-label fw-semibold">Name Extension</label>
                            <input type="text" class="form-control" id="extensionName" name="extension_name" placeholder="e.g. Jr., Sr., III">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="gender" class="form-label fw-semibold">Gender<span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="dob" class="form-label fw-semibold">Date of Birth<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dob" name="birthday" required>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="age" class="form-label fw-semibold">Age<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="col-md-6">
                            <label for="appointmentLocation" class="form-label">Street/Municipality/Province/Postal Code</label>
                            <input type="text" name="location" id="appointmentLocation" class="form-control" placeholder="Enter location">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="position" class="form-label fw-semibold">Position<span class="text-danger">*</span></label>
                            <input type="text" name="position" id="position" class="form-control" placeholder="Enter position" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rate_per_day" class="form-label fw-semibold">Rate/Day</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="rate_per_day" id="rate_per_day" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3 text-secondary mt-2"><i class="fas fa-calendar-alt me-2"></i>Period of Employment</h6>
                            <div class="row">
                                <div class="col-6">
                                    <label for="employment_start" class="form-label form-label-sm text-muted">From</label>
                                    <input type="date" name="employment_start" id="employment_start" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label for="employment_end" class="form-label form-label-sm text-muted">To</label>
                                    <input type="date" name="employment_end" id="employment_end" class="form-control" value="{{ old('employment_end') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="source_of_fund" class="form-label fw-semibold">Source of Fund</label>
                            <input type="text" name="source_of_fund" id="source_of_fund" class="form-control" placeholder="Enter source of fund" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="office_assignment" class="form-label fw-semibold">Office Assignment</label>
                            <select name="office_assignment" id="office_assignment" class="form-select" required>
                                <option value="">Select an office</option>
                                <option value="Office of the Mayor (MO)">Office of the Mayor (MO)</option>
                                <option value="Office of the Sanguniang Bayan (SBO)">Office of the Sanguniang Bayan (SBO)</option>
                                <option value="Municipal Planning & Development Coordinator (MPDO)">Municipal Planning & Development Coordinator (MPDO)</option>
                                <option value="Office of the Local Civil Registrar (LCR)">Office of the Local Civil Registrar (LCR)</option>
                                <option value="Office of the Municipal Budget Officer (MBO)">Office of the Municipal Budget Officer (MBO)</option>
                                <option value="Office of the Municipal Accountant (MACCO)">Office of the Municipal Accountant (MACCO)</option>
                                <option value="Office of the Municipal Treasurer (MTO)">Office of the Municipal Treasurer (MTO)</option>
                                <option value="Office of the Municipal Assessor (MASSO)">Office of the Municipal Assessor (MASSO)</option>
                                <option value="Office of the Municipal Health Officer (MHO/RHU)">Office of the Municipal Health Officer (MHO/RHU)</option>
                                <option value="Social Welfare & Development Officer (MSWDO)">Social Welfare & Development Officer (MSWDO)</option>
                                <option value="Office of the Municipal Agriculturist (MAO)">Office of the Municipal Agriculturist (MAO)</option>
                                <option value="Office of the Municipal Engineer (MEO)">Office of the Municipal Engineer (MEO)</option>
                                <option value="Ergonomic Enterprise Development Management (MEE)">Ergonomic Enterprise Development Management (MEE)</option>
                                <option value="Local Disaster Risk Reduction & Management (MDRRMO)">Local Disaster Risk Reduction & Management (MDRRMO)</option>
                            </select>
                        </div>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const appointmentType = document.getElementById('appointment_type');
                        const idLabel = document.getElementById('id_label');
                        const idInput = document.getElementById('employeeId');
                        const idHelp = document.getElementById('id_help');

                        function updateIdField() {
                            if (appointmentType.value === 'job_order') {
                                idLabel.textContent = 'Employee ID';
                                idInput.name = 'employee_id';
                                idInput.placeholder = 'Enter Employee ID';
                                idInput.value = '';
                                idInput.required = true;
                                idHelp.textContent = 'For Job Order: Use Employee ID.';
                            } else if (appointmentType.value) {
                                idLabel.textContent = 'Item No';
                                idInput.name = 'item_no';
                                idInput.placeholder = 'Enter Item No';
                                idInput.value = '';
                                idInput.required = true;
                                idHelp.textContent = 'For Plantilla Records: Use Item No.';
                            } else {
                                idLabel.textContent = 'Employee ID/Item No';
                                idInput.name = 'employee_id';
                                idInput.placeholder = '';
                                idInput.value = '';
                                idInput.required = true;
                                idHelp.textContent = 'For Job Order: Use Employee ID. For Plantilla Records: Use Item No.';
                            }
                        }
                        appointmentType.addEventListener('change', updateIdField);
                        updateIdField();
                    });
                    </script>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const plantillaTypes = [
                            'casual', 'contractual', 'coterminous', 'coterminousTemporary',
                            'elected', 'permanent', 'provisional', 'regularPermanent', 'substitute', 'temporary'
                        ];
                        const appointmentTypeSelect = document.getElementById('appointment_type');
                        const employmentEndInput = document.getElementById('employment_end');
                        function toggleEmploymentEnd() {
                            const selectedType = appointmentTypeSelect.value;
                            if (plantillaTypes.includes(selectedType)) {
                                employmentEndInput.style.display = 'none';
                                employmentEndInput.value = '';
                                employmentEndInput.removeAttribute('required');
                            } else {
                                employmentEndInput.style.display = '';
                                employmentEndInput.setAttribute('required', 'required');
                            }
                        }
                        if (appointmentTypeSelect && employmentEndInput) {
                            appointmentTypeSelect.addEventListener('change', toggleEmploymentEnd);
                            toggleEmploymentEnd(); // Initial call on page load
                        }
                    });
                    </script>
                    <div class="modal-footer bg-light border-0 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import CSV Modal -->
<div class="modal fade" id="importCsvModal" tabindex="-1" aria-labelledby="importCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="importCsvModalLabel">
                    <i class="fas fa-file-csv me-2"></i> Import CSV - Appointment Records
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('appointment.import') }}" method="POST" enctype="multipart/form-data" id="csvImportForm">
                @csrf
                <div class="modal-body bg-light">
                    <div class="mb-3">
                        <label for="csvFile" class="form-label fw-semibold">CSV File</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                            <a href="{{ route('appointments.downloadSampleCsv') }}" class="sample-csv-link" target="_blank">
                                <i class="fas fa-download me-1"></i> Download Sample CSV
                            </a>
                        </div>
                        <small class="text-muted">Max file size: 2MB. Required columns: name, position, rate_per_day, employment_start, employment_end, source_of_fund, location, office_assignment, appointment_type, item_no, employee_id, first_name, middle_name, last_name, extension_name, gender, birthday, age</small>
                    </div>
                    <!-- Progress bar -->
                    <div class="progress-container" style="display: none;">
                        <div class="progress-bar" style="width: 0%;"></div>
                    </div>
                    <!-- Error display -->
                    @if($errors->any())
                        <div class="error-list">
                            <strong>Import Errors:</strong>
                            <ul class="list-unstyled">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- Success message -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Close</button>
                    <button type="submit" class="btn btn-success" id="importSubmitBtn"><i class="fas fa-upload me-1"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Editing Schedule -->
<div id="editSchedModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="editAppointmentForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="scheduleId">
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="edit_appointment_type" class="form-label">Appointment Type</label>
                            <select name="appointment_type" id="edit_appointment_type" class="form-select" required>
                                <optgroup label="Plantilla Records">
                                    <option value="casual">Casual</option>
                                    <option value="contractual">Contractual</option>
                                    <option value="coterminous">Coterminous</option>
                                    <option value="coterminousTemporary">Coterminous - Temporary</option>
                                    <option value="elected">Elected</option>
                                    <option value="permanent">Permanent</option>
                                    <option value="provisional">Provisional</option>
                                    <option value="regularPermanent">Regular Permanent</option>
                                    <option value="substitute">Substitute</option>
                                    <option value="temporary">Temporary</option>
                                </optgroup>
                                <optgroup label="Service Records">
                                    <option value="job_order">Job Order</option>
                                </optgroup>
                            </select>
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i> Job Order will be directed to Service Records. All other types will go to Plantilla.
                            </small>
                        </div>
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="editEmployeeId" class="form-label">
                                <span id="edit_id_label">Employee ID/Item No</span>*
                            </label>
                            <input type="text" class="form-control" id="editEmployeeId" name="employee_id" required>
                            <small class="form-text text-muted">
                                For Job Order: Use Employee ID. For Plantilla Records: Use Item No.
                            </small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Personal Details</h6>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="editFirstName" class="form-label">First Name*</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="editMiddleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="editMiddleName" name="middle_name">
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="editLastName" class="form-label">Last Name*</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="editExtensionName" class="form-label">Name Extension</label>
                            <input type="text" class="form-control" id="editExtensionName" name="extension_name" placeholder="e.g. Jr., Sr., III">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="editGender" class="form-label">Gender*</label>
                            <select class="form-select" id="editGender" name="gender" required>
                                <option value="" disabled>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="editDob" class="form-label">Date of Birth*</label>
                            <input type="date" class="form-control" id="editDob" name="birthday" required>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="editAge" class="form-label">Age*</label>
                            <input type="number" class="form-control" id="editAge" name="age" required>
                        </div>
                        <div class="col-md-6">
                            <label for="appointmentLocation" class="form-label">Location</label>
                            <input type="text" name="location" id="appointmentLocation" class="form-control" placeholder="Enter location">
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="appointmentPosition" class="form-label">Position*</label>
                            <input type="text" name="position" id="appointmentPosition" class="form-control" placeholder="Enter position" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="appointmentRatePerDay" class="form-label">Rate/Day</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="rate_per_day" id="appointmentRatePerDay" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Period of Employment</label>
                            <div class="row">
                                <div class="col-6">
                                    <label for="appointmentEmploymentStart" class="form-label form-label-sm text-muted">From</label>
                                    <input type="date" name="employment_start" id="appointmentEmploymentStart" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label for="appointmentEmploymentEnd" class="form-label form-label-sm text-muted">To</label>
                                    <input type="date" name="employment_end" id="appointmentEmploymentEnd" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="appointmentSourceOfFund" class="form-label">Source of Fund</label>
                            <input type="text" name="source_of_fund" id="appointmentSourceOfFund" class="form-control" placeholder="Enter source of fund" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="appointmentOfficeAssignment" class="form-label">Office Assignment</label>
                            <select name="office_assignment" id="appointmentOfficeAssignment" class="form-select" required>
                                <option value="">Select an office</option>
                                <option value="Office of the Mayor (MO)">Office of the Mayor (MO)</option>
                                <option value="Office of the Sanguniang Bayan (SBO)">Office of the Sanguniang Bayan (SBO)</option>
                                <option value="Municipal Planning & Development Coordinator (MPDO)">Municipal Planning & Development Coordinator (MPDO)</option>
                                <option value="Office of the Local Civil Registrar (LCR)">Office of the Local Civil Registrar (LCR)</option>
                                <option value="Office of the Municipal Budget Officer (MBO)">Office of the Municipal Budget Officer (MBO)</option>
                                <option value="Office of the Municipal Accountant (MACCO)">Office of the Municipal Accountant (MACCO)</option>
                                <option value="Office of the Municipal Treasurer (MTO)">Office of the Municipal Treasurer (MTO)</option>
                                <option value="Office of the Municipal Assessor (MASSO)">Office of the Municipal Assessor (MASSO)</option>
                                <option value="Office of the Municipal Health Officer (MHO/RHU)">Office of the Municipal Health Officer (MHO/RHU)</option>
                                <option value="Social Welfare & Development Officer (MSWDO)">Social Welfare & Development Officer (MSWDO)</option>
                                <option value="Office of the Municipal Agriculturist (MAO)">Office of the Municipal Agriculturist (MAO)</option>
                                <option value="Office of the Municipal Engineer (MEO)">Office of the Municipal Engineer (MEO)</option>
                                <option value="Ergonomic Enterprise Development Management (MEE)">Ergonomic Enterprise Development Management (MEE)</option>
                                <option value="Local Disaster Risk Reduction & Management (MDRRMO)">Local Disaster Risk Reduction & Management (MDRRMO)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/appointments/schedule.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const appointmentType = document.getElementById('appointment_type');
        const employeeIdInput = document.getElementById('employeeId');
        let debounceTimeout = null;

        // Create or get the error message element
        let errorMsg = document.createElement('div');
        errorMsg.className = 'alert alert-danger py-2';
        errorMsg.style.display = 'none';
        employeeIdInput.parentNode.insertBefore(errorMsg, employeeIdInput.nextSibling);

        function checkUnique(type, value) {
            if (!value) {
                errorMsg.style.display = 'none';
                return;
            }
            fetch("{{ route('appointment.checkUnique') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ type: type, value: value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    errorMsg.textContent = (type === 'employee_id')
                        ? 'This Employee ID is already in use.'
                        : 'This Item No is already in use.';
                    errorMsg.style.display = 'block';
                } else {
                    errorMsg.style.display = 'none';
                }
            });
        }

        function getFieldType() {
            return appointmentType.value === 'job_order' ? 'employee_id' : 'item_no';
        }

        employeeIdInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                checkUnique(getFieldType(), employeeIdInput.value.trim());
            }, 400);
        });
        appointmentType.addEventListener('change', function () {
            errorMsg.style.display = 'none';
            employeeIdInput.value = '';
        });
    });
</script>

@endsection