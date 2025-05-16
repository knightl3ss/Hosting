<!-- Modal for Adding Schedule -->
<div id="addSchedModal" class="modal fade" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i> Add New Appointment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-4">
                <form id="addAppointmentForm" action="{{ route('appointment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-4">
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
                            <div class="invalid-feedback d-block"></div>
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i> Job Order will be directed to Service Records. All other types will go to Plantilla.
                            </small>
                        </div>
                        <div class="col-md-6">
    <label class="form-label fw-semibold">
        <span id="id_label">Employee ID / Item No</span><span class="text-danger">*</span>
    </label>
    <!-- Employee ID for Job Order -->
    <input type="text" class="form-control mb-2" id="employeeId" name="employee_id" placeholder="Enter Employee ID" style="display:none;">
    <div class="invalid-feedback d-block" id="employeeId_feedback"></div>
    <!-- Item No for Plantilla -->
    <input type="text" class="form-control mb-2" id="itemNo" name="item_no" placeholder="Enter Item No" style="display:none;">
    <div class="invalid-feedback d-block" id="itemNo_feedback"></div>
    <small class="form-text text-muted" id="id_help">
        Only letters, numbers, and hyphens (-) are allowed.
    </small>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function toggleIdFields() {
            var type = document.getElementById('appointment_type').value;
            var empId = document.getElementById('employeeId');
            var itemNo = document.getElementById('itemNo');
            var empIdFeedback = document.getElementById('employeeId_feedback');
            var itemNoFeedback = document.getElementById('itemNo_feedback');
            if (type === 'job_order') {
                empId.style.display = '';
                empId.required = true;
                itemNo.style.display = 'none';
                itemNo.required = false;
                itemNo.value = '';
                empIdFeedback.style.display = '';
                itemNoFeedback.style.display = 'none';
            } else if (type) {
                itemNo.style.display = '';
                itemNo.required = true;
                empId.style.display = 'none';
                empId.required = false;
                empId.value = '';
                empIdFeedback.style.display = 'none';
                itemNoFeedback.style.display = '';
                
                // Add event listener to copy value from itemNo to employeeId for non-job orders
                itemNo.addEventListener('input', function() {
                    empId.value = this.value;
                });
            } else {
                empId.style.display = 'none';
                empId.required = false;
                itemNo.style.display = 'none';
                itemNo.required = false;
                empIdFeedback.style.display = 'none';
                itemNoFeedback.style.display = 'none';
            }
        }
        var typeSelect = document.getElementById('appointment_type');
        if (typeSelect) {
            typeSelect.addEventListener('change', toggleIdFields);
            toggleIdFields();
        }
    });
</script>
                    </div>

                    <hr class="my-4">
                    <!-- Personal Details Section -->
                    <div class="row mb-4">
                        <div class="col-12 mb-2">
                            <h6 class="border-bottom pb-2 mb-3 text-secondary"><i class="fas fa-user me-2"></i>Personal Details</h6>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="firstName" class="form-label fw-semibold">First Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                            <div class="invalid-feedback d-block"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="middleName" class="form-label fw-semibold">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" name="middle_name">
                            <div class="invalid-feedback d-block"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="lastName" class="form-label fw-semibold">Last Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                            <div class="invalid-feedback d-block"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="extensionName" class="form-label fw-semibold">Name Extension</label>
                            <input type="text" class="form-control" id="extensionName" name="extension_name" placeholder="e.g. Jr., Sr., III">
                            <div class="invalid-feedback d-block"></div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- Additional fields with error containers -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="gender" class="form-label fw-semibold">Gender<span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <div class="invalid-feedback d-block"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="dob" class="form-label fw-semibold">Date of Birth<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dob" name="birthday" required>
                            <div class="invalid-feedback d-block"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="age" class="form-label fw-semibold">Age<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="age" name="age" required readonly>
                            <div class="invalid-feedback d-block"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="appointmentLocation" class="form-label">Location</label>
                            <input type="text" name="location" id="appointmentLocation" class="form-control" placeholder="Enter location">
                            <div class="invalid-feedback d-block"></div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- Position and Rate -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="position" class="form-label fw-semibold">Position<span class="text-danger">*</span></label>
                            <input type="text" name="position" id="position" class="form-control" placeholder="Enter position" required>
                            <div class="invalid-feedback d-block"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="rate_per_day" class="form-label fw-semibold">Salary Rate</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="text" name="rate_per_day" id="rate_per_day" class="form-control" placeholder="0.00" required>
                                <div class="invalid-feedback d-block"></div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- Employment Period -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3 text-secondary mt-2"><i class="fas fa-calendar-alt me-2"></i>Period of Employment</h6>
                            <div class="row">
                                <div class="col-6">
                                    <label for="employment_start" class="form-label form-label-sm text-muted">From</label>
                                    <input type="date" name="employment_start" id="employment_start" class="form-control" required max="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback d-block"></div>
                                    <small class="text-muted">Start date cannot be in the future</small>
                                </div>
                                <div class="col-6" id="employment_end_group">
                                    <label for="employment_end" class="form-label form-label-sm text-muted">To</label>
                                    <input type="date" name="employment_end" id="employment_end" class="form-control" required data-validation="end-date" max="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback d-block"></div>
                                    <small class="text-muted" id="employment_end_help">End date must be after start date and not in the future</small>
                                </div>

                                <div class="col-6">
                                    <label for="source_of_fund" class="form-label fw-semibold">Source of Fund</label>
                                    <input type="text" name="source_of_fund" id="source_of_fund" class="form-control" placeholder="Enter source of fund" required>
                                    <div class="invalid-feedback d-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- Office Assignment -->
                    <div class="row mb-4">
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
                            <div class="invalid-feedback d-block"></div>
                        </div>
                    </div>

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
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="editModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Appointment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <form id="editAppointmentForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="edit_appointment_type" class="form-label fw-semibold">Employment Status</label>
                            <select name="appointment_type" id="edit_appointment_type" class="form-select" required>
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
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_employeeId" class="form-label fw-semibold">
                                <span id="edit_id_label">Employee ID/Item No</span><span class="text-danger">*</span>
                            </label>
                            <!-- Employee ID for Job Order -->
                            <input type="text" class="form-control mb-2" id="edit_employeeId" name="employee_id" placeholder="Enter Employee ID" style="display:none;">
                            <div class="invalid-feedback d-block" id="edit_employeeId_feedback"></div>
                            <!-- Item No for Plantilla -->
                            <input type="text" class="form-control mb-2" id="edit_itemNo" name="item_no" placeholder="Enter Item No" style="display:none;">
                            <div class="invalid-feedback d-block" id="edit_itemNo_feedback"></div>
                            <small class="form-text text-muted" id="edit_id_help">
                                Only letters, numbers, and hyphens (-) are allowed.
                            </small>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            function toggleEditIdFields() {
                                var type = document.getElementById('edit_appointment_type').value;
                                var empId = document.getElementById('edit_employeeId');
                                var itemNo = document.getElementById('edit_itemNo');
                                var empIdFeedback = document.getElementById('edit_employeeId_feedback');
                                var itemNoFeedback = document.getElementById('edit_itemNo_feedback');
                                
                                if (type === 'job_order') {
                                    empId.style.display = '';
                                    empId.required = true;
                                    itemNo.style.display = 'none';
                                    itemNo.required = false;
                                    itemNo.value = '';
                                    empIdFeedback.style.display = '';
                                    itemNoFeedback.style.display = 'none';
                                } else if (type) {
                                    itemNo.style.display = '';
                                    itemNo.required = true;
                                    empId.style.display = 'none';
                                    empId.required = false;
                                    empId.value = ''; // We'll sync this when submitting
                                    empIdFeedback.style.display = 'none';
                                    itemNoFeedback.style.display = '';
                                    
                                    // Add event listener to copy value from itemNo to employeeId for non-job orders
                                    itemNo.addEventListener('input', function() {
                                        empId.value = this.value;
                                    });
                                } else {
                                    empId.style.display = 'none';
                                    empId.required = false;
                                    itemNo.style.display = 'none';
                                    itemNo.required = false;
                                    empIdFeedback.style.display = 'none';
                                    itemNoFeedback.style.display = 'none';
                                }
                            }
                            
                            var typeSelect = document.getElementById('edit_appointment_type');
                            if (typeSelect) {
                                typeSelect.addEventListener('change', toggleEditIdFields);
                                toggleEditIdFields();
                            }
                        });
                    </script>

                    <!-- Personal Details Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 text-secondary"><i class="fas fa-user me-2"></i>Personal Details</h6>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_firstName" class="form-label fw-semibold">First Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_firstName" name="first_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_middleName" class="form-label fw-semibold">Middle Name</label>
                            <input type="text" class="form-control" id="edit_middleName" name="middle_name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_lastName" class="form-label fw-semibold">Last Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_lastName" name="last_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_extensionName" class="form-label fw-semibold">Name Extension</label>
                            <input type="text" class="form-control" id="edit_extensionName" name="extension_name" placeholder="e.g. Jr., Sr., III">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Additional fields with error containers -->
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_gender" class="form-label fw-semibold">Gender<span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_gender" name="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_dob" class="form-label fw-semibold">Date of Birth<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_dob" name="birthday" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_age" class="form-label fw-semibold">Age<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_age" name="age" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_appointmentLocation" class="form-label">Location</label>
                            <input type="text" name="location" id="edit_appointmentLocation" class="form-control" placeholder="Enter location">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Position and Rate -->
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="edit_position" class="form-label fw-semibold">Position<span class="text-danger">*</span></label>
                            <input type="text" name="position" id="edit_position" class="form-control" placeholder="Enter position" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_rate_per_day" class="form-label fw-semibold">Rate/Day</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="text" name="rate_per_day" id="edit_rate_per_day" class="form-control" placeholder="0.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Period -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3 text-secondary mt-2"><i class="fas fa-calendar-alt me-2"></i>Period of Employment</h6>
                            <div class="row">
                                <div class="col-6">
                                    <label for="edit_employment_start" class="form-label form-label-sm text-muted">From</label>
                                    <input type="date" name="employment_start" id="edit_employment_start" class="form-control" required max="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback"></div>
                                    <small class="text-muted">Start date cannot be in the future</small>
                                </div>
                                <div class="col-6" id="edit_employment_end_group">
                                    <label for="edit_employment_end" class="form-label form-label-sm text-muted">To</label>
                                    <input type="date" name="employment_end" id="edit_employment_end" class="form-control" required data-validation="end-date" max="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback"></div>
                                    <small class="text-muted" id="edit_employment_end_help">End date must be after start date and not in the future</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_source_of_fund" class="form-label fw-semibold">Source of Fund</label>
                            <input type="text" name="source_of_fund" id="edit_source_of_fund" class="form-control" placeholder="Enter source of fund" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Office Assignment -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_office_assignment" class="form-label fw-semibold">Office Assignment</label>
                            <select name="office_assignment" id="edit_office_assignment" class="form-select" required>
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
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Form submit handler for the add appointment form
        const addForm = document.getElementById('addAppointmentForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                const type = document.getElementById('appointment_type').value;
                const itemNo = document.getElementById('itemNo');
                const empId = document.getElementById('employeeId');
                
                // For non-job orders, copy item_no to employee_id just before submitting
                if (type && type !== 'job_order' && itemNo.value) {
                    empId.value = itemNo.value;
                }
            });
        }
        
        // Form submit handler for the edit appointment form
        const editForm = document.getElementById('editAppointmentForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const type = document.getElementById('edit_appointment_type').value;
                const itemNo = document.getElementById('edit_itemNo');
                const empId = document.getElementById('edit_employeeId');
                
                // For non-job orders, copy item_no to employee_id just before submitting
                if (type && type !== 'job_order' && itemNo.value) {
                    empId.value = itemNo.value;
                }
            });
        }
    });
</script>

<!-- Load appointment validation script -->
<script src="{{ asset('js/appointments/validation.js') }}"></script> 