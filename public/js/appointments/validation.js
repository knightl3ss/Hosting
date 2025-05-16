// Validation class for appointment forms
console.log('Appointment validation.js loaded');

class AppointmentValidation {
    constructor() {
        this.initializeValidation();
        this.debounceTimers = {};
    }

    // Debounce function to limit how often functions are called
    debounce(func, delay, id) {
        return (...args) => {
            // Clear existing timer for this id
            if (this.debounceTimers[id]) {
                clearTimeout(this.debounceTimers[id]);
            }
            // Set new timer
            this.debounceTimers[id] = setTimeout(() => {
                func.apply(this, args);
                delete this.debounceTimers[id];
            }, delay);
        };
    }

    initializeValidation() {
        // Initialize validation for both add and edit forms
        this.initializeFormValidation('addAppointmentForm');
        this.initializeFormValidation('editAppointmentForm');
    }

    initializeFormValidation(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        // Get all input elements
        const inputs = form.querySelectorAll('input, select');

        // Add event listeners for real-time validation
        inputs.forEach(input => {
            input.addEventListener('input', () => this.validateField(input));
            input.addEventListener('change', () => this.validateField(input));
            input.addEventListener('blur', () => this.validateField(input));
        });

        // Add event listeners for uniqueness validation on input and blur
        const prefixId = formId === 'editAppointmentForm' ? 'edit_' : '';
        const employeeIdInput = form.querySelector(`#${prefixId}employeeId`);
        const itemNoInput = form.querySelector(`#${prefixId}itemNo`);

        if (employeeIdInput) {
            // Real-time check with debounce
            employeeIdInput.addEventListener('input', () => {
                if (employeeIdInput.style.display !== 'none' && employeeIdInput.value.trim()) {
                    this.debounce(
                        () => this.checkUniqueField('employee_id', employeeIdInput.value.trim(), employeeIdInput),
                        500, // 500ms delay
                        'employee_id'
                    )();
                }
            });

            // Still keep blur event for when user tabs between fields
            employeeIdInput.addEventListener('blur', () => {
                if (employeeIdInput.style.display !== 'none' && employeeIdInput.value.trim()) {
                    this.checkUniqueField('employee_id', employeeIdInput.value.trim(), employeeIdInput);
                }
            });
        }

        if (itemNoInput) {
            // Real-time check with debounce
            itemNoInput.addEventListener('input', () => {
                if (itemNoInput.style.display !== 'none' && itemNoInput.value.trim()) {
                    this.debounce(
                        () => this.checkUniqueField('item_no', itemNoInput.value.trim(), itemNoInput),
                        500, // 500ms delay
                        'item_no'
                    )();
                }
            });

            // Still keep blur event for when user tabs between fields
            itemNoInput.addEventListener('blur', () => {
                if (itemNoInput.style.display !== 'none' && itemNoInput.value.trim()) {
                    this.checkUniqueField('item_no', itemNoInput.value.trim(), itemNoInput);
                }
            });
        }

        // Add formatting for middle, last, and extension name fields
        ['middleName', 'lastName', 'extensionName', 'edit_middleName', 'edit_lastName', 'edit_extensionName'].forEach(id => {
            const field = form.querySelector(`#${id}`);
            if (field) {
                field.addEventListener('input', () => {
                    let value = field.value.trim();
                    value = value.replace(/\s{2,}/g, ' ');
                    value = value.replace(/\b\w/g, c => c.toUpperCase());
                    field.value = value;
                });
            }
        });

        // Add numeric-only input validation for rate_per_day fields
        this.setupRatePerDayValidation(form);

        // Make age field readonly and update on birthdate change
        const dobField = form.querySelector('#dob') || form.querySelector('#edit_dob');
        const ageField = form.querySelector('#age') || form.querySelector('#edit_age');
        if (ageField) {
            ageField.readOnly = true;
        }
        if (dobField && ageField) {
            dobField.addEventListener('change', () => {
                const dob = new Date(dobField.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                ageField.value = age > 0 ? age : '';
            });
        }

        // Show/hide employment_end for addAppointmentForm based on appointment_type
        if (formId === 'addAppointmentForm') {
            const appointmentType = form.querySelector('#appointment_type');
            const employmentEndGroup = form.querySelector('#employment_end_group');
            const employmentEndInput = form.querySelector('#employment_end');
            const employmentStartInput = form.querySelector('#employment_start');

            function toggleEmploymentEnd() {
                if (appointmentType.value === 'job_order') {
                    employmentEndGroup.style.display = '';
                    employmentEndInput.required = true;
                } else {
                    employmentEndGroup.style.display = 'none';
                    employmentEndInput.required = false;
                    employmentEndInput.value = '';
                }
            }

            // Set min date for employment_end based on employment_start
            if (employmentStartInput && employmentEndInput) {
                employmentStartInput.addEventListener('change', () => {
                    if (employmentStartInput.value) {
                        employmentEndInput.min = employmentStartInput.value;

                        // If current end date is before new start date, clear it
                        if (employmentEndInput.value && employmentEndInput.value < employmentStartInput.value) {
                            employmentEndInput.value = '';
                        }
                    }
                });
            }

            // Initial state
            toggleEmploymentEnd();

            // Listen for changes
            appointmentType.addEventListener('change', toggleEmploymentEnd);
        }

        // Apply the same logic to edit form
        if (formId === 'editAppointmentForm') {
            const editAppointmentType = form.querySelector('#edit_appointment_type');
            const editEmploymentEndGroup = form.querySelector('#edit_employment_end_group');
            const editEmploymentEndInput = form.querySelector('#edit_employment_end');
            const editEmploymentStartInput = form.querySelector('#edit_employment_start');

            function toggleEditEmploymentEnd() {
                if (editAppointmentType.value === 'job_order') {
                    editEmploymentEndGroup.style.display = '';
                    editEmploymentEndInput.required = true;
                } else {
                    editEmploymentEndGroup.style.display = 'none';
                    editEmploymentEndInput.required = false;
                    editEmploymentEndInput.value = '';
                }
            }

            // Set min date for employment_end based on employment_start
            if (editEmploymentStartInput && editEmploymentEndInput) {
                editEmploymentStartInput.addEventListener('change', () => {
                    if (editEmploymentStartInput.value) {
                        editEmploymentEndInput.min = editEmploymentStartInput.value;

                        // If current end date is before new start date, clear it
                        if (editEmploymentEndInput.value && editEmploymentEndInput.value < editEmploymentStartInput.value) {
                            editEmploymentEndInput.value = '';
                        }
                    }
                });
            }

            // Initial state
            toggleEditEmploymentEnd();

            // Listen for changes
            if (editAppointmentType) {
                editAppointmentType.addEventListener('change', toggleEditEmploymentEnd);
            }
        }

        // Add form submit validation
        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form)) {
                e.preventDefault();
            }
        });
    }

    validateField(input) {
        const value = input.value.trim();
        let isValid = true;

        // Clear previous error
        this.clearError(input);

        // Required field validation
        if (input.required && !value && input.style.display !== 'none') {
            this.showError(input, 'This field is required');
            return false;
        }

        // Field-specific validation
        if (value && input.style.display !== 'none') {
            switch (input.id) {
                case 'employeeId':
                case 'edit_employeeId':
                    isValid = this.validateEmployeeId(input);
                    break;
                case 'itemNo':
                case 'edit_itemNo':
                    isValid = this.validateItemNo(input);
                    break;
                case 'firstName':
                case 'middleName':
                case 'lastName':
                case 'extensionName':
                case 'edit_firstName':
                case 'edit_middleName':
                case 'edit_lastName':
                case 'edit_extensionName':
                    isValid = this.validateName(input);
                    break;
                case 'dob':
                case 'edit_dob':
                    isValid = this.validateAge(input);
                    break;
                case 'employment_start':
                case 'employment_end':
                case 'edit_employment_start':
                case 'edit_employment_end':
                    isValid = this.validateEmploymentDates(input);
                    break;
                case 'rate_per_day':
                case 'edit_rate_per_day':
                    isValid = this.validateRatePerDay(input);
                    break;
                case 'position':
                case 'edit_position':
                    isValid = this.validatePosition(input);
                    break;
            }
        }

        return isValid;
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, select');
        let employeeIds = new Set();
        let itemNos = new Set();
        let duplicateFound = false;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
            // Cross-row uniqueness check for batch forms
            if (input.name === 'employee_id' && input.value.trim() && input.style.display !== 'none') {
                if (employeeIds.has(input.value.trim())) {
                    this.showError(input, 'Duplicate Employee ID in batch.');
                    isValid = false;
                    duplicateFound = true;
                } else {
                    employeeIds.add(input.value.trim());
                }
            }
            if (input.name === 'item_no' && input.value.trim() && input.style.display !== 'none') {
                if (itemNos.has(input.value.trim())) {
                    this.showError(input, 'Duplicate Item No in batch.');
                    isValid = false;
                    duplicateFound = true;
                } else {
                    itemNos.add(input.value.trim());
                }
            }
        });

        // Optionally, display a summary error if duplicate found
        if (duplicateFound) {
            alert('Duplicate Employee ID or Item No found in the batch. Please ensure all are unique.');
        }

        return isValid;
    }

    // Check if field value is unique in the database
    checkUniqueField(type, value, input) {
        if (!value) return;

        // Show checking message
        const checkingMsg = `Checking ${type.replace('_', ' ')}...`;
        this.showMessage(input, checkingMsg, 'text-info');

        console.log(`Checking uniqueness for ${type} with value: ${value}`);
        fetch('/check-unique', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ type, value })
        })
            .then(res => res.json())
            .then(data => {
                console.log(`Uniqueness check response for ${type}:`, data);
                if (data.exists) {
                    let msg = `${type.replace('_', ' ')} is already taken.`;
                    this.showError(input, msg.charAt(0).toUpperCase() + msg.slice(1));
                } else {
                    this.clearError(input);
                    // Show a success message briefly
                    this.showMessage(input, `${type.replace('_', ' ')} is available`, 'text-success');
                    setTimeout(() => {
                        if (input.value === value) { // Only clear if value hasn't changed
                            this.clearMessage(input);
                        }
                    }, 2000);
                }
            })
            .catch((error) => {
                console.error(`Error checking uniqueness for ${type}:`, error);
                this.showError(input, 'Could not validate uniqueness.');
            });
    }

    validateEmployeeId(input) {
        const value = input.value.trim();
        // Only validate if field is visible
        if (input.style.display === 'none') return true;

        // Basic validation: required and only alphanumeric with hyphen
        const basicPattern = /^[a-zA-Z0-9\-]+$/;
        if (!value) {
            this.showError(input, 'Employee ID is required');
            return false;
        }
        if (!basicPattern.test(value)) {
            this.showError(input, 'Employee ID can only contain letters, numbers, and hyphens');
            return false;
        }

        // Check if employee ID is unique
        this.checkUniqueField('employee_id', value, input);

        return true;
    }

    validateItemNo(input) {
        const value = input.value.trim();
        // Only validate if field is visible
        if (input.style.display === 'none') return true;

        // Basic validation: required and only alphanumeric with hyphen
        const basicPattern = /^[a-zA-Z0-9\-]+$/;
        if (!value) {
            this.showError(input, 'Item No is required');
            return false;
        }
        if (!basicPattern.test(value)) {
            this.showError(input, 'Item No can only contain letters, numbers, and hyphens');
            return false;
        }

        // Check if item no is unique
        this.checkUniqueField('item_no', value, input);

        return true;
    }

    validateName(input) {
        let value = input.value.trim();
        // Remove double spaces
        value = value.replace(/\s{2,}/g, ' ');
        // Uppercase the first letter of each word
        value = value.replace(/\b\w/g, c => c.toUpperCase());
        input.value = value;
        // Allow letters, spaces, and common name characters
        const namePattern = /^[a-zA-Z\s\-'\.]+$/;
        if (value.length < 2 && input.id !== 'extensionName' && input.id !== 'edit_extensionName') {
            this.showError(input, 'Name must be at least 2 characters long');
            return false;
        }
        if (!namePattern.test(value)) {
            this.showError(input, 'Name can only contain letters, spaces, hyphens, apostrophes, and periods');
            return false;
        }
        return true;
    }

    validateAge(input) {
        const dob = new Date(input.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        const ageInput = document.getElementById(input.id === 'dob' ? 'age' : 'edit_age');
        if (ageInput) {
            ageInput.value = age > 0 ? age : '';
            ageInput.readOnly = true;
        }
        if (age < 18 || age > 65) {
            this.showError(input, 'Age must be between 18 and 65 years');
            return false;
        }
        return true;
    }

    validateEmploymentDates(input) {
        // Determine if we're dealing with add or edit form fields
        const isEditForm = input.id.includes('edit_');
        const startDateId = isEditForm ? 'edit_employment_start' : 'employment_start';
        const endDateId = isEditForm ? 'edit_employment_end' : 'employment_end';
        const appointmentTypeId = isEditForm ? 'edit_appointment_type' : 'appointment_type';

        const startDateElement = document.getElementById(startDateId);
        const endDateElement = document.getElementById(endDateId);
        const appointmentTypeElement = document.getElementById(appointmentTypeId);

        if (!startDateElement || !appointmentTypeElement) return true; // Skip validation if elements don't exist

        const startDate = new Date(startDateElement.value);
        const appointmentType = appointmentTypeElement.value;
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time to start of day for fair comparison

        // Check if start date is in the future
        if (startDate > today) {
            this.showError(input.id === startDateId ? startDateElement : input, 'Employment start date cannot be in the future');
            return false;
        }

        // Only validate end date if it's visible and has a value (for job orders)
        if (appointmentType === 'job_order' && endDateElement && endDateElement.value && endDateElement.style.display !== 'none') {
            const endDate = new Date(endDateElement.value);

            // Check if end date is in the future
            if (endDate > today) {
                this.showError(input.id === endDateId ? endDateElement : input, 'Employment end date cannot be in the future');
                return false;
            }

            if (endDate < startDate) {
                this.showError(input, 'End date cannot be earlier than start date');
                return false;
            }

            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if (diffDays > 180) {
                this.showError(input, 'Job Order duration cannot exceed 180 days');
                return false;
            }
        }

        return true;
    }

    validateRatePerDay(input) {
        // First sanitize the input to ensure it's a valid number format
        let value = input.value.trim();
        value = value.replace(/[^\d.]/g, ''); // Remove any non-numeric characters except decimal point

        // Ensure there's only one decimal point
        const decimalPoints = value.match(/\./g);
        if (decimalPoints && decimalPoints.length > 1) {
            value = value.substring(0, value.lastIndexOf('.'));
        }

        // Limit to 2 decimal places
        if (value.includes('.')) {
            const parts = value.split('.');
            if (parts[1] && parts[1].length > 2) {
                parts[1] = parts[1].substring(0, 2);
                value = parts.join('.');
            }
        }

        // Update the value if it was changed
        if (value !== input.value) {
            input.value = value;
        }

        // Now validate the numeric range
        const numValue = parseFloat(value);
        if (isNaN(numValue) || numValue < 500 || numValue > 5000000) {
            this.showError(input, 'Salary Rate must be between ₱500 and ₱5,000,000');
            return false;
        }

        return true;
    }

    setupRatePerDayValidation(form) {
        const rateFields = [
            form.querySelector('#rate_per_day'),
            form.querySelector('#edit_rate_per_day')
        ].filter(field => field); // Filter out null values

        rateFields.forEach(field => {
            // Allow only numbers and decimal point on input
            field.addEventListener('input', function () {
                // Replace any non-numeric characters except decimal point
                let value = this.value.replace(/[^0-9.]/g, '');

                // Ensure only one decimal point
                const decimalPoints = value.match(/\./g);
                if (decimalPoints && decimalPoints.length > 1) {
                    value = value.substring(0, value.lastIndexOf('.'));
                }

                // Ensure no more than 2 decimal places
                if (value.includes('.')) {
                    const parts = value.split('.');
                    if (parts[1] && parts[1].length > 2) {
                        parts[1] = parts[1].substring(0, 2);
                        value = parts.join('.');
                    }
                }

                // Update the input value
                this.value = value;
            });

            // Prevent non-numeric key presses
            field.addEventListener('keypress', function (e) {
                const key = e.key;

                // Allow backspace, delete, and arrow keys
                if (e.key === 'Backspace' || e.key === 'Delete' ||
                    e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                    return;
                }

                // Allow decimal point only if not already present
                if (key === '.' && this.value.includes('.')) {
                    e.preventDefault();
                    return;
                }

                // Block any other non-numeric input
                if (isNaN(key) && key !== '.') {
                    e.preventDefault();
                }
            });

            // Format to 2 decimal places on blur for better readability
            field.addEventListener('blur', function () {
                if (this.value) {
                    const number = parseFloat(this.value);
                    if (!isNaN(number)) {
                        this.value = number.toFixed(2);
                    }
                }
            });
        });
    }

    validatePosition(input) {
        const value = input.value.trim();
        if (value.length < 3) {
            this.showError(input, 'Position must be at least 3 characters long');
            return false;
        }
        // Check for special characters
        const specialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
        if (specialChars.test(value)) {
            this.showError(input, 'Position cannot contain special characters');
            return false;
        }
        return true;
    }

    showError(input, message) {
        input.classList.add('is-invalid');
        this.showMessage(input, message, 'text-danger');
    }

    clearError(input) {
        input.classList.remove('is-invalid');
        this.clearMessage(input);
    }

    // Show a message (not necessarily an error)
    showMessage(input, message, className = 'text-info') {
        input.classList.remove('is-invalid');
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';

            // Remove any existing classes
            errorDiv.classList.remove('text-danger', 'text-info', 'text-success');
            // Add the new class
            errorDiv.classList.add(className);
        }
    }

    // Clear any message
    clearMessage(input) {
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }
}

// Initialize validation when the document is ready
document.addEventListener('DOMContentLoaded', () => {
    new AppointmentValidation();
}); 