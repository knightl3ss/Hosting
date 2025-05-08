    @php $typeLabels = config('appointment_types'); @endphp

<!-- Edit Service Record Modals - Moved outside the table -->
@if(isset($serviceRecords) && count($serviceRecords) > 0)
    @foreach($serviceRecords as $record)
    <div class="modal fade" id="editServiceRecordModal{{ $record->id }}" tabindex="-1" aria-labelledby="editServiceRecordLabel{{ $record->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceRecordLabel{{ $record->id }}">Edit Service Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('update.service_record', ['id' => $employee->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Add hidden input for record_id -->
                        <input type="hidden" name="record_id" value="{{ $record->id }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_date_from{{ $record->id }}" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="edit_date_from{{ $record->id }}" name="date_from" value="{{ $record->date_from ? \Carbon\Carbon::parse($record->date_from)->format('Y-m-d') : '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_date_to{{ $record->id }}" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="edit_date_to{{ $record->id }}" name="date_to" value="{{ $record->date_to ? \Carbon\Carbon::parse($record->date_to)->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_designation{{ $record->id }}" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="edit_designation{{ $record->id }}" name="designation" value="{{ $record->designation }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_status{{ $record->id }}" class="form-label">Status Salary</label>
                                <input type="text" class="form-control" id="edit_status{{ $record->id }}" name="status" value="{{ $typeLabels[$record->status] ?? ucwords(str_replace('_', ' ', $record->status)) }}" required readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit_payment_frequency{{ $record->id }}" class="form-label">Payment Frequency</label>
                                <select class="form-select" id="edit_payment_frequency{{ $record->id }}" name="payment_frequency" required>
                                    <option value="">Select Frequency</option>
                                    <option value="Daily" {{ isset($record->payment_frequency) && $record->payment_frequency == 'Daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="Monthly" {{ isset($record->payment_frequency) && $record->payment_frequency == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="Annum" {{ isset($record->payment_frequency) && $record->payment_frequency == 'Annum' ? 'selected' : '' }}>Annum</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_salary{{ $record->id }}" class="form-label">Salary</label>
                                <input type="number" step="0.01" class="form-control" id="edit_salary{{ $record->id }}" name="salary" value="{{ $record->salary }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_station{{ $record->id }}" class="form-label">Station Place</label>
                                <input type="text" class="form-control" id="edit_station{{ $record->id }}" name="station" value="{{ $record->station }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_separation_date{{ $record->id }}" class="form-label">Separation Date (if applicable)</label>
                                <input type="text" class="form-control" id="edit_separation_date{{ $record->id }}" name="separation_date" value="{{ $record->separation_date }}">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_service_status{{ $record->id }}" class="form-label">Service Status</label>
                                <select class="form-select" id="edit_service_status{{ $record->id }}" name="service_status" required>
                                    <option value="">Select Status</option>
                                    <option value="In Service" {{ $record->service_status == 'In Service' ? 'selected' : '' }}>In Service</option>
                                    <option value="Suspension" {{ $record->service_status == 'Suspension' ? 'selected' : '' }}>Suspension</option>
                                    <option value="Not in Service" {{ $record->service_status == 'Not in Service' ? 'selected' : '' }}>Not in Service</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Service Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

<!-- Add Service Record Modal -->
<div class="modal fade" id="addServiceRecordModal" tabindex="-1" role="dialog" aria-labelledby="addServiceRecordLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceRecordLabel">Add Service Record for {{ $employee->getFullNameAttribute() }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('store.service_record', ['id' => $employee->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" required>
                        </div>
                        <div class="col-md-6">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation', $employee->position ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status_salary" class="form-label">Status Salary</label>
                            <input type="text" class="form-control" id="status_salary" name="status" value="{{ $typeLabels[$employee->appointment_type] ?? ucwords(str_replace('_', ' ', $employee->appointment_type)) }}" required readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="payment_frequency" class="form-label">Payment Frequency</label>
                            <select class="form-select" id="payment_frequency" name="payment_frequency" required>
                                <option value="">Select Frequency</option>
                                <option value="Daily">Daily</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Annum">Annum</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" class="form-control" id="salary" name="salary" required>
                        </div>
                        <div class="col-md-4">
                            <label for="station" class="form-label">Station Place</label>
                            <input type="text" class="form-control" id="station" name="station" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="separation_date" class="form-label">Separation Date (if applicable)</label>
                            <input type="text" class="form-control" id="separation_date" name="separation_date">
                        </div>
                        <div class="col-md-6">
                            <label for="service_status" class="form-label">Service Status</label>
                            <select class="form-select" id="service_status" name="service_status" required>
                                <option value="">Select Status</option>
                                <option value="In Service">In Service</option>
                                <option value="Suspension">Suspension</option>
                                <option value="Not in Service">Not in Service</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Service Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Service Record Data Validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add validation for the add service record form
    const addForm = document.querySelector('#addServiceRecordModal form');
    if (addForm) {
        addForm.addEventListener('submit', function(event) {
            if (!validateServiceRecordForm(this)) {
                event.preventDefault();
            }
        });

        // Set up real-time validation for date fields
        const dateFrom = document.getElementById('date_from');
        const dateTo = document.getElementById('date_to');
        
        dateFrom.addEventListener('change', function() {
            validateDateRange(dateFrom, dateTo);
        });
        
        dateTo.addEventListener('change', function() {
            validateDateRange(dateFrom, dateTo);
        });
    }

    // Add validation for all edit service record forms
    const editForms = document.querySelectorAll('[id^="editServiceRecordModal"] form');
    editForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!validateServiceRecordForm(this)) {
                event.preventDefault();
            }
        });

        // Get the record ID from the modal ID
        const modalId = form.closest('.modal').id;
        const recordId = modalId.replace('editServiceRecordModal', '');
        
        // Set up real-time validation for date fields in edit forms
        const editDateFrom = document.getElementById('edit_date_from' + recordId);
        const editDateTo = document.getElementById('edit_date_to' + recordId);
        
        if (editDateFrom && editDateTo) {
            editDateFrom.addEventListener('change', function() {
                validateDateRange(editDateFrom, editDateTo);
            });
            
            editDateTo.addEventListener('change', function() {
                validateDateRange(editDateFrom, editDateTo);
            });
        }
    });

    // Function to validate the entire service record form
    function validateServiceRecordForm(form) {
        let isValid = true;
        
        // Get form elements
        const dateFrom = form.querySelector('[name="date_from"]');
        const dateTo = form.querySelector('[name="date_to"]');
        const salary = form.querySelector('[name="salary"]');
        const paymentFrequency = form.querySelector('[name="payment_frequency"]');
        const separationDate = form.querySelector('[name="separation_date"]');
        const serviceStatus = form.querySelector('[name="service_status"]');
        
        // Validate date range
        if (!validateDateRange(dateFrom, dateTo)) {
            isValid = false;
        }
        
        // Validate salary is positive
        if (parseFloat(salary.value) <= 0) {
            alert('Salary must be greater than zero.');
            salary.focus();
            isValid = false;
        }
        
        // Validate service status and separation date consistency
        if (serviceStatus.value === 'Not in Service' && !separationDate.value.trim()) {
            alert('Separation date is required when service status is "Not in Service".');
            separationDate.focus();
            isValid = false;
        }
        
        // Validate that "In Service" status doesn't have a separation date
        if (serviceStatus.value === 'In Service' && separationDate.value.trim()) {
            alert('Separation date should be empty for "In Service" status.');
            separationDate.focus();
            isValid = false;
        }
        
        return isValid;
    }
    
    // Function to validate date range
    function validateDateRange(fromDate, toDate) {
        if (!fromDate.value || !toDate.value) {
            return true; // Skip validation if either date is not set
        }
        
        const from = new Date(fromDate.value);
        const to = new Date(toDate.value);
        
        if (from > to) {
            alert('From date cannot be later than To date.');
            fromDate.focus();
            return false;
        }
        
        // Check if the date range is reasonable (e.g., not more than 10 years)
        const diffYears = (to - from) / (1000 * 60 * 60 * 24 * 365);
        if (diffYears > 10) {
            if (!confirm('The date range spans more than 10 years. Is this correct?')) {
                fromDate.focus();
                return false;
            }
        }
        
        return true;
    }
});
</script>