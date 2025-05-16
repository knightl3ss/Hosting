<!-- Edit Personal Details Modal -->
<div class="modal fade" id="editPersonalDetailsModal" tabindex="-1" aria-labelledby="editPersonalDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPersonalDetailsModalLabel">Edit Personal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPersonalDetailsForm" action="{{ route('appointment.updatePersonalDetails', $appointment->id ?? '') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Debug Error:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <strong>Debug Error:</strong> {{ session('error') }}
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Personal Details</h6>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="firstName" class="form-label">First Name*</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="{{ $appointment->first_name ?? '' }}" required pattern="[A-Za-z\s]+" title="Please enter alphabets only">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" name="middle_name" value="{{ $appointment->middle_name ?? '' }}" pattern="[A-Za-z\s]*" title="Please enter alphabets only">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="lastName" class="form-label">Last Name*</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="{{ $appointment->last_name ?? '' }}" required pattern="[A-Za-z\s]+" title="Please enter alphabets only">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="extensionName" class="form-label">Name Extension</label>
                            <input type="text" class="form-control" id="extensionName" name="extension_name" placeholder="e.g. Jr., Sr., III" value="{{ $appointment->extension_name ?? '' }}" pattern="[A-Za-z\s\.]+" title="Please enter valid extension (e.g., Jr., Sr.)">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="gender" class="form-label">Gender*</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="" disabled>Select gender</option>
                                <option value="male" {{ (isset($appointment) && $appointment->gender == 'male') ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ (isset($appointment) && $appointment->gender == 'female') ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ (isset($appointment) && $appointment->gender == 'other') ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="dob" class="form-label">Date of Birth*</label>
                            <input type="date" class="form-control" id="dob" name="birthday" value="{{ isset($appointment->birthday) ? \Carbon\Carbon::parse($appointment->birthday)->format('Y-m-d') : '' }}" required max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                            <div class="invalid-feedback"></div>
                            <small id="birthdayFeedback" class="form-text"></small>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="age" class="form-label">Age*</label>
                            <input type="number" class="form-control" id="age" name="age" value="{{ isset($appointment->age) ? $appointment->age : '' }}" required readonly>
                            <div class="invalid-feedback"></div>
                            <small id="ageFeedback" class="form-text"></small>
                        </div>
                        <div class="col-md-6">
                            <label for="appointmentLocation" class="form-label">Street/Municipality/Province/Postal Code</label>
                            <input type="text" name="location" id="appointmentLocation" class="form-control" placeholder="Enter location" value="{{ $appointment->location ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Specific Appointment Modal -->
<div class="modal fade" id="deleteSpecificModal" tabindex="-1" aria-labelledby="deleteSpecificModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteSpecificModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                </div>
                <p class="mb-1">Are you sure you want to delete this appointment record for:</p>
                <h5 class="mb-3 text-center" id="specificEmployeeName"></h5>
                <p class="mb-1" id="specificEmployeePosition"></p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i> This action cannot be undone and may affect related records.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteSpecificForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="parent_appointment_id" value="{{ $appointment->id }}">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Employee Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                </div>
                <p class="mb-1">Are you sure you want to delete this employee record:</p>
                <h5 class="mb-3 text-center">{{ $appointment->name }}</h5>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> <strong>Warning:</strong> This will delete ALL appointment records for this employee, along with all related data in Plantilla/Service Records.
                </div>
                @php
                $appointmentCount = \App\Models\AppointmentModel\Appointment::where('employee_id', $appointment->employee_id)->count();
                @endphp
                <p class="mb-0"><strong>Employee ID/Item No:</strong> {{ $appointment->employee_id }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @php
                    $identifier = $appointment->appointment_type === 'job_order'
                        ? $appointment->employee_id
                        : $appointment->item_no;
                @endphp
                <form action="{{ route('appointments.destroyEmployee', $identifier) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Delete Employee Records
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include Validation Script -->
<script src="{{ asset('js/appointments/validation.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize validation for edit personal details form
    const editPersonalDetailsForm = document.getElementById('editPersonalDetailsForm');
    if (editPersonalDetailsForm) {
        // Add event listeners for real-time validation
        const inputs = editPersonalDetailsForm.querySelectorAll('input, select');
        
        // Auto-calculate age when birthday changes
        const dobField = editPersonalDetailsForm.querySelector('#dob');
        const ageField = editPersonalDetailsForm.querySelector('#age');
        
        if (dobField && ageField) {
            ageField.readOnly = true;
            dobField.addEventListener('change', function() {
                const dob = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                ageField.value = age > 0 ? age : '';
                
                // Validate age
                if (age < 18 || age > 100) {
                    showError(dobField, 'Age must be between 18 and 100 years');
                } else {
                    clearError(dobField);
                }
            });
        }
        
        // Name field validation
        const nameFields = ['firstName', 'middleName', 'lastName', 'extensionName'];
        nameFields.forEach(fieldId => {
            const field = editPersonalDetailsForm.querySelector(`#${fieldId}`);
            if (field) {
                field.addEventListener('input', function() {
                    let value = this.value.trim();
                    // Remove double spaces
                    value = value.replace(/\s{2,}/g, ' ');
                    // Uppercase the first letter of each word
                    value = value.replace(/\b\w/g, c => c.toUpperCase());
                    this.value = value;
                    
                    // Validate name
                    const namePattern = /^[a-zA-Z\s\-'\.]+$/;
                    if (fieldId !== 'middleName' && fieldId !== 'extensionName' && value.length < 2) {
                        showError(this, 'Name must be at least 2 characters long');
                    } else if (!namePattern.test(value) && value.length > 0) {
                        showError(this, 'Name can only contain letters, spaces, hyphens, apostrophes, and periods');
                    } else {
                        clearError(this);
                    }
                });
            }
        });
        
        // Form submission validation
        editPersonalDetailsForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all required fields
            inputs.forEach(input => {
                if (input.required && !input.value.trim()) {
                    showError(input, 'This field is required');
                    isValid = false;
                }
            });
            
            // Validate age range
            if (ageField && (parseInt(ageField.value) < 18 || parseInt(ageField.value) > 100)) {
                showError(ageField, 'Age must be between 18 and 100 years');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Helper functions for validation
    function showError(input, message) {
        input.classList.add('is-invalid');
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }
    
    function clearError(input) {
        input.classList.remove('is-invalid');
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }
});
</script>