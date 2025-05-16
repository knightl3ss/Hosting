// DEBUG: Confirm JS is loaded
console.log('edit.js loaded');

document.addEventListener('DOMContentLoaded', function () {
    // Initialize validation for account forms
    const accountValidation = new AccountValidation();
});

// Account Validation class for admin account forms
class AccountValidation {
    constructor() {
        this.initializeValidation();
    }

    initializeValidation() {
        // Initialize validation for both registration and edit forms
        this.initializeFormValidation('adminRegistrationForm');
        this.initializeFormValidation('editAccountForm');
    }

    initializeFormValidation(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        // Get all input elements
        const inputs = form.querySelectorAll('input, select');

        // Track original values for unique fields
        const originalValues = {};
        if (formId === 'editAccountForm') {
            const uniqueFields = ['username', 'email', 'employee_id'];
            uniqueFields.forEach(field => {
                const input = form.querySelector(`#edit_${field}`);
                if (input) originalValues[field] = input.value;
            });
        }

        // Add event listeners for real-time validation
        inputs.forEach(input => {
            input.addEventListener('input', () => this.validateField(input, originalValues));
            input.addEventListener('change', () => this.validateField(input, originalValues));
            input.addEventListener('blur', () => this.validateField(input, originalValues));
        });

        // Auto-calculate age when birthday changes
        const dobField = formId === 'editAccountForm'
            ? form.querySelector('#edit_birthday')
            : form.querySelector('#birthday');

        const ageField = formId === 'editAccountForm'
            ? form.querySelector('#edit_age')
            : form.querySelector('#age');

        if (dobField && ageField) {
            ageField.readOnly = true;
            dobField.addEventListener('change', () => {
                const dob = new Date(dobField.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                ageField.value = age > 0 ? age : '';
                this.validateField(dobField, originalValues);
            });
        }

        // Password toggle functionality
        const toggleButtons = form.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                // Toggle password visibility
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Add form submit validation
        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form, originalValues)) {
                e.preventDefault();
                return;
            }

            // Handle readonly fields for submission
            if (formId === 'editAccountForm') {
                const uniqueFields = ['username', 'email', 'employee_id'];
                uniqueFields.forEach(field => {
                    const input = form.querySelector(`#edit_${field}`);
                    const readonlyField = document.getElementById(`${field}_readonly`);

                    if (input && readonlyField && readonlyField.value === 'true') {
                        // If field is still readonly, ensure we submit the original value
                        input.value = input.getAttribute('data-original-value') || input.value;
                    }
                });
            }
        });
    }

    validateField(input, originalValues = {}) {
        const value = input.value.trim();
        let isValid = true;

        // Clear previous error
        this.clearError(input);

        // Skip validation if disabled or readonly
        if (input.disabled || input.readOnly) {
            return true;
        }

        // Skip validation for unique fields if the value hasn't changed from original (for edit form)
        const fieldId = input.id;
        if (fieldId === 'edit_username' || fieldId === 'edit_email' || fieldId === 'edit_employee_id') {
            // If the field hasn't been edited (no data-edited attribute) or 
            // value matches original value, skip validation
            if (!input.hasAttribute('data-edited') ||
                (input.hasAttribute('data-original-value') &&
                    value === input.getAttribute('data-original-value'))) {
                return true;
            }
        }

        // Required field validation
        if (input.required && !value) {
            this.showError(input, 'This field is required');
            return false;
        }

        // Field-specific validation
        if (value) {
            const fieldName = input.name;
            const fieldId = input.id;

            // Name validations
            if (fieldName === 'first_name' || fieldName === 'middle_name' ||
                fieldName === 'last_name' || fieldName === 'extension_name') {
                isValid = this.validateName(input);
            }
            // Age and birthday validations
            else if (fieldName === 'birthday' || fieldId === 'edit_birthday') {
                isValid = this.validateAge(input);
            }
            else if (fieldName === 'age' || fieldId === 'edit_age') {
                // Age is calculated automatically, so just check range
                const age = parseInt(value);
                if (isNaN(age) || age < 18 || age > 100) {
                    this.showError(input, 'Age must be between 18 and 100');
                    isValid = false;
                }
            }
            // Employee ID validation
            else if (fieldName === 'employee_id' || fieldId === 'edit_employee_id') {
                isValid = this.validateEmployeeId(input);
            }
            // Username validation
            else if (fieldName === 'username' || fieldId === 'edit_username') {
                isValid = this.validateUsername(input);
            }
            // Email validation
            else if (fieldName === 'email' || fieldId === 'edit_email') {
                isValid = this.validateEmail(input);
            }
            // Phone number validation
            else if (fieldName === 'phone_number' || fieldId === 'edit_phone_number') {
                isValid = this.validatePhoneNumber(input);
            }
            // Password validation
            else if (fieldName === 'password' || fieldId === 'edit_password') {
                if (value.length > 0) {
                    isValid = this.validatePassword(input);
                }
            }
            // Password confirmation validation
            else if (fieldName === 'password_confirmation' || fieldId === 'edit_password_confirmation') {
                if (value.length > 0) {
                    isValid = this.validatePasswordConfirmation(input);
                }
            }
            // Address validation
            else if (fieldName.includes('address_')) {
                isValid = this.validateAddress(input);
            }
        }

        return isValid;
    }

    validateForm(form, originalValues = {}) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, select');

        inputs.forEach(input => {
            if (!this.validateField(input, originalValues)) {
                isValid = false;
            }
        });

        // Cross-field validations
        if (form.id === 'editAccountForm' || form.id === 'adminRegistrationForm') {
            const prefix = form.id === 'editAccountForm' ? 'edit_' : '';

            // Password and confirmation match
            const passwordField = document.getElementById(`${prefix}password`);
            const confirmField = document.getElementById(`${prefix}password_confirmation`);

            if (passwordField && confirmField &&
                passwordField.value &&
                passwordField.value !== confirmField.value) {
                this.showError(confirmField, 'Passwords do not match');
                isValid = false;
            }
        }

        return isValid;
    }

    validateName(input) {
        let value = input.value.trim();
        // Remove double spaces
        value = value.replace(/\s{2,}/g, ' ');
        // Uppercase the first letter of each word
        value = value.replace(/\b\w/g, c => c.toUpperCase());
        input.value = value;

        // Name validation
        const namePattern = /^[a-zA-Z\s\-'\.]+$/;
        if (input.name !== 'middle_name' && input.name !== 'extension_name' && value.length < 2) {
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

        // Find the age input field
        const ageInput = input.id === 'birthday' ?
            document.getElementById('age') :
            document.getElementById('edit_age');

        if (ageInput) {
            ageInput.value = age > 0 ? age : '';
        }

        if (age < 18 || age > 100) {
            this.showError(input, 'Age must be between 18 and 100 years');
            return false;
        }
        return true;
    }

    validateEmployeeId(input) {
        const value = input.value.trim();
        const pattern = /^[a-zA-Z0-9\-]+$/;

        if (!pattern.test(value)) {
            this.showError(input, 'Employee ID can only contain letters, numbers, and hyphens');
            return false;
        }

        return true;
    }

    validateUsername(input) {
        const value = input.value.trim();
        const pattern = /^[A-Za-z0-9_]+$/;

        if (!pattern.test(value)) {
            this.showError(input, 'Username can only contain letters, numbers, and underscores');
            return false;
        }

        if (value.length < 3) {
            this.showError(input, 'Username must be at least 3 characters long');
            return false;
        }

        return true;
    }

    validateEmail(input) {
        const value = input.value.trim();
        const pattern = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

        if (!pattern.test(value)) {
            this.showError(input, 'Please enter a valid email address');
            return false;
        }

        return true;
    }

    validatePhoneNumber(input) {
        const value = input.value.trim();
        // Format for Philippine mobile numbers: 09XXXXXXXXX (11 digits)
        const pattern = /^09\d{9}$/;

        // Clean the input - remove any non-digit characters
        const cleanedValue = value.replace(/\D/g, '');

        if (!pattern.test(cleanedValue)) {
            this.showError(input, 'Please enter a valid Philippine mobile number (09XXXXXXXXX)');
            return false;
        }

        // Update the field with the cleaned value
        if (cleanedValue !== value) {
            input.value = cleanedValue;
        }

        return true;
    }

    validatePassword(input) {
        const value = input.value;
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special char
        const pattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;

        if (!pattern.test(value)) {
            this.showError(input, 'Password must contain at least 8 characters, including uppercase, lowercase, number, and special character');
            return false;
        }

        return true;
    }

    validatePasswordConfirmation(input) {
        const passwordInput = input.id === 'password_confirmation' ?
            document.getElementById('password') :
            document.getElementById('edit_password');

        if (input.value !== passwordInput.value) {
            this.showError(input, 'Passwords do not match');
            return false;
        }

        return true;
    }

    validateAddress(input) {
        const value = input.value.trim();

        if (input.name === 'address_postal_code') {
            // Postal code should be numeric
            const pattern = /^\d+$/;
            if (!pattern.test(value)) {
                this.showError(input, 'Postal code must contain only numbers');
                return false;
            }
        }

        return true;
    }

    showError(input, message) {
        input.classList.add('is-invalid');
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    clearError(input) {
        input.classList.remove('is-invalid');
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }
}