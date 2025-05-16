// DEBUG: Confirm JS is loaded
console.log('registration.js loaded');

// Add CSS for validation states
function addValidationStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .is-validating {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath fill='%23007bff' d='M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='1s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: calc(1.5em + 0.75rem) !important;
        }
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #dc3545;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .pending-feedback, .success-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
        }
        .form-control.is-valid {
            border-color: #28a745;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
    `;
    document.head.appendChild(style);
}

document.addEventListener('DOMContentLoaded', function () {
    // Add validation styles first
    addValidationStyles();

    const form = document.getElementById('adminRegistrationForm');
    const editForm = document.getElementById('editAccountForm'); // Add edit form reference

    console.log('Registration form found:', form !== null);
    console.log('Edit form found:', editForm !== null);

    if (!form && !editForm) {
        console.warn('No forms found on page');
        return;
    }

    // Helper: Show error message next to input
    function showError(input, message) {
        let errorElem = input.parentElement.querySelector('.invalid-feedback');
        if (!errorElem) {
            errorElem = document.createElement('div');
            errorElem.className = 'invalid-feedback';
            input.parentElement.appendChild(errorElem);
        }
        errorElem.textContent = message;
        errorElem.style.display = 'block'; // Ensure error is visible
        input.classList.add('is-invalid');

        // Add subtle animation to draw attention
        input.style.transition = 'border-color 0.3s';
        input.style.borderColor = '#dc3545';

        // Scroll into view if not visible (optional)
        if (!isElementInViewport(input)) {
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        return false; // For chaining
    }

    // Helper: Clear error message
    function clearError(input) {
        let errorElem = input.parentElement.querySelector('.invalid-feedback');
        if (errorElem) {
            errorElem.textContent = '';
            errorElem.style.display = 'none';
        }
        input.classList.remove('is-invalid');
        input.style.borderColor = '';
        return true; // For chaining
    }

    // Check if element is in viewport
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // --- Philippine Mobile Number Validation ---
    function isValidPhilippineMobile(number) {
        // Clean the number first - remove all non-digit characters
        const cleanedNumber = number.trim().replace(/[^0-9]/g, '');

        // Only accept 09XXXXXXXXX format (11 digits starting with 09)
        // Other formats like +639XXXXXXXXX or 639XXXXXXXXX are no longer accepted

        // Check if it's exactly 11 digits starting with 09
        if (/^09\d{9}$/.test(cleanedNumber)) {
            return true;
        }

        return false;
    }

    // --- Password Validation ---
    function isValidPassword(password) {
        // Min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/.test(password);
    }

    // --- Confirm Password Validation ---
    const passwordInput = form ? form.querySelector('#password') : null;
    const confirmPasswordInput = form ? form.querySelector('#confirm_password') : null;
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function () {
            if (confirmPasswordInput.value !== passwordInput.value) {
                showError(confirmPasswordInput, 'Passwords do not match.');
            } else {
                clearError(confirmPasswordInput);
            }
        });
    }

    // --- Email Validation for Allowed Accounts ---
    const restrictedDomains = ['@test.com', '@blocked.com', '@example.com', '@root.com', '@superuser.com', '@user.com'];
    const restrictedUsernames = ['user', 'example', 'root', 'superuser'];
    function isValidEmail(input) {
        const emailPattern = /^[^\s@]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(input.value)) return false;
        const email = input.value.toLowerCase();
        // Block restricted domains
        if (restrictedDomains.some(domain => email.endsWith(domain))) return false;
        // Block if username contains any restricted username
        const username = email.split('@')[0];
        if (restrictedUsernames.some(word => username.includes(word))) return false;
        return true;
    }

    // --- Custom Required Fields List (JS only) ---
    const requiredFieldNames = [
        'first_name', 'last_name', 'age', 'birthday', 'gender',
        'address_street', 'address_city', 'address_state', 'address_postal_code',
        'employee_id', 'username', 'email', 'phone_number', 'role', 'password', 'password_confirmation'
    ]; // Add/remove as needed

    // --- Field dependencies for cross-validation ---
    const fieldDependencies = {
        'first_name': ['last_name'], // First and last name should be consistent in capitalization
        'last_name': ['first_name'],
        'birthday': ['age'], // Birthday and age should be consistent
        'age': ['birthday'],
        'address_city': ['address_state'], // Address components should be consistent
        'address_state': ['address_city'],
        'password': ['password_confirmation'], // Password fields should match
        'password_confirmation': ['password']
    };

    // Single field validation
    let prevValidateField = null;
    function validateField(input) {
        clearError(input);
        // JS-only required check
        if (requiredFieldNames.includes(input.name) && !input.value.trim()) {
            showError(input, 'This field is required.');
            return false;
        }
        if (input.name === 'email' && input.value) {
            if (!isValidEmail(input)) {
                showError(input, "Email must not include restricted domains or usernames.");
                return false;
            }
        }

        // Cross-field validation
        if (fieldDependencies[input.name]) {
            const dependentFields = fieldDependencies[input.name];
            for (const depFieldName of dependentFields) {
                const depField = form.elements.namedItem(depFieldName);
                if (!depField || !depField.value.trim()) continue;

                // Specific cross-field validations
                if (input.name === 'password' && depFieldName === 'password_confirmation') {
                    if (input.value !== depField.value) {
                        showError(input, 'Passwords do not match.');
                        return false;
                    }
                } else if (input.name === 'password_confirmation' && depFieldName === 'password') {
                    if (input.value !== depField.value) {
                        showError(input, 'Passwords do not match.');
                        return false;
                    }
                } else if (input.name === 'birthday' && depFieldName === 'age') {
                    const calculatedAge = calculateAge(input.value);
                    if (calculatedAge !== parseInt(depField.value)) {
                        showError(input, 'Birthday does not match the provided age.');
                        return false;
                    }
                } else if (input.name === 'age' && depFieldName === 'birthday') {
                    const calculatedAge = calculateAge(depField.value);
                    if (calculatedAge !== parseInt(input.value)) {
                        showError(input, 'Age does not match the provided birthday.');
                        return false;
                    }
                }
            }
        }
        // Capitalize first letter (for text inputs)
        if (input.type === 'text' && input.value.length > 0) {
            let val = input.value;
            // Only capitalize if NOT
            if (input.name !== 'password' && input.name !== 'password_confirmation' && input.name !== 'extension_name' && input.name !== 'employee_id' && input.name !== 'username') {
                val = val.charAt(0).toUpperCase() + val.slice(1);
            }
            // Remove double spaces
            val = val.replace(/\s{2,}/g, ' ');
            if (val !== input.value) {
                input.value = val;
            }
            // Check for double spaces (shouldn't be possible after replace, but just in case)
            if (/\s{2,}/.test(input.value)) {
                showError(input, 'Double spaces are not allowed.');
                return false;
            }
        }
        if (input.type === 'email' && input.value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(input.value)) {
                showError(input, 'Please enter a valid email address.');
                return false;
            }
        }
        if (input.name === 'phone_number' && input.value) {
            if (!isValidPhilippineMobile(input.value)) {
                showError(input, 'Please enter a valid Philippine mobile number.');
                return false;
            }
        }
        if (input.name === 'confirm_password' && input.value) {
            if (input.value !== passwordInput.value) {
                showError(input, 'Passwords do not match.');
                return false;
            }
        }
        // Prevent numbers in name fields
        if ((input.name === 'first_name' || input.name === 'last_name' || input.name === 'middle_name' || input.name === 'extension_name') && input.value) {
            if (/\d/.test(input.value)) {
                showError(input, 'Name cannot contain numbers.');
                return false;
            }
        }
        // Prevent letters in phone_number field
        if (input.name === 'phone_number' && input.value) {
            if (/[^0-9+\-\s]/.test(input.value)) {
                showError(input, 'Mobile number cannot contain letters.');
                return false;
            }
        }
        return true;
    }

    // --- Real-time Password Strength Feedback ---
    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            const pwd = passwordInput.value;
            let msg = '';
            if (pwd.length < 8) {
                msg = 'Password must be at least 8 characters.';
            } else if (!/[A-Z]/.test(pwd)) {
                msg = 'Include at least one uppercase letter.';
            } else if (!/[a-z]/.test(pwd)) {
                msg = 'Include at least one lowercase letter.';
            } else if (!/[0-9]/.test(pwd)) {
                msg = 'Include at least one number.';
            } else if (!/[^A-Za-z0-9]/.test(pwd)) {
                msg = 'Include at least one special character.';
            }
            let feedbackElem = passwordInput.parentElement.querySelector('.password-feedback');
            if (!feedbackElem) {
                feedbackElem = document.createElement('small');
                feedbackElem.className = 'password-feedback text-danger';
                passwordInput.parentElement.appendChild(feedbackElem);
            }
            feedbackElem.textContent = msg;
            if (msg) {
                showError(passwordInput, msg);
            } else {
                clearError(passwordInput);
            }
        });
    }

    // --- Real-time Error Message for All Fields ---
    function addFieldValidation(formElement) {
        if (!formElement) return;

        formElement.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', () => {
                validateField(input);
            });
            input.addEventListener('blur', () => {
                validateField(input);
            });
        });
    }

    // Add validation to both forms
    addFieldValidation(form);
    addFieldValidation(editForm);

    // --- Restrict Leading Spaces for All Text Inputs ---
    function addSpaceRestriction(formElement) {
        if (!formElement) return;

        formElement.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function () {
                // Remove leading spaces
                let val = input.value;
                val = val.replace(/^\s+/, '');
                if (val !== input.value) {
                    input.value = val;
                }
            });
        });
    }

    addSpaceRestriction(form);
    addSpaceRestriction(editForm);

    // --- Name Fields: Restrict Numbers, Prevent Leading Spaces ---
    const nameFields = ['first_name', 'middle_name', 'last_name', 'extension_name'];
    nameFields.forEach(fieldId => {
        const field = form ? form.querySelector(`#${fieldId}`) : null;
        if (field) {
            field.addEventListener('input', function (e) {
                // Remove any digit
                let val = this.value;
                val = val.replace(/[0-9]/g, '');
                if (val !== this.value) {
                    this.value = val;
                }
            });
        }
    });

    // --- Uniqueness Check for Username, Email, Employee ID ---
    function checkUniqueField(type, value, input) {
        if (!value) return;

        console.log(`Checking uniqueness for ${type}: ${value}`);

        // Show pending state before result comes back
        input.classList.add('is-validating');
        let pendingMsg = input.parentElement.querySelector('.pending-feedback');
        if (!pendingMsg) {
            pendingMsg = document.createElement('div');
            pendingMsg.className = 'pending-feedback text-info small';
            input.parentElement.appendChild(pendingMsg);
        }

        // Custom messages based on field type
        const checkingMessages = {
            'username': 'Checking if username is available...',
            'email': 'Checking if email is available...',
            'employee_id': 'Checking if employee ID is available...'
        };

        const successMessages = {
            'username': 'Username is available!',
            'email': 'Email is available!',
            'employee_id': 'Employee ID is available!'
        };

        const errorMessages = {
            'username': 'This username is already taken.',
            'email': 'This email is already registered.',
            'employee_id': 'This employee ID is already in use.'
        };

        pendingMsg.textContent = checkingMessages[type] || `Checking if ${type} is available...`;
        pendingMsg.style.display = 'block';

        // Check for CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error("CSRF token meta tag not found!");
            showError(input, "CSRF validation failed. Please refresh the page.");
            if (pendingMsg) pendingMsg.style.display = 'none';
            input.classList.remove('is-validating');
            return;
        }

        fetch('/check-unique', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({ type, value })
        })
            .then(res => {
                console.log(`Response status for ${type}: ${res.status}`);
                return res.json();
            })
            .then(data => {
                console.log(`Check result for ${type}: exists=${data.exists}`);
                if (pendingMsg) pendingMsg.style.display = 'none';
                input.classList.remove('is-validating');

                if (data.exists) {
                    let msg = errorMessages[type] || `This ${type.replace('_', ' ')} is already taken.`;
                    showError(input, msg);

                    // Add visual feedback
                    input.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
                    setTimeout(() => {
                        input.style.backgroundColor = '';
                    }, 2000);
                } else {
                    clearError(input);
                    // Show success message
                    let successMsg = input.parentElement.querySelector('.success-feedback');
                    if (!successMsg) {
                        successMsg = document.createElement('div');
                        successMsg.className = 'success-feedback small text-success';
                        input.parentElement.appendChild(successMsg);
                    }
                    successMsg.textContent = successMessages[type] || `${type.replace('_', ' ')} is available!`;
                    successMsg.style.display = 'block';
                    input.classList.add('is-valid');

                    // Hide success message after 3 seconds
                    setTimeout(() => {
                        successMsg.style.display = 'none';
                        if (!input.classList.contains('is-invalid')) {
                            input.classList.remove('is-valid');
                        }
                    }, 3000);
                }
            })
            .catch((error) => {
                console.error(`Error checking uniqueness for ${type}:`, error);
                if (pendingMsg) pendingMsg.style.display = 'none';
                input.classList.remove('is-validating');
                showError(input, 'Could not validate uniqueness. Please try again.');
            });
    }

    // Function to attach uniqueness validation to both forms
    function attachUniquenessValidation(formElement, idPrefix = '') {
        if (!formElement) return;

        // More specific selectors to ensure we find the right fields
        const usernameInput = formElement.querySelector(`#${idPrefix}username`) ||
            formElement.querySelector(`[name="username"][id="${idPrefix}username"]`) ||
            formElement.querySelector('[name="username"]');

        const emailInput = formElement.querySelector(`#${idPrefix}email`) ||
            formElement.querySelector(`[name="email"][id="${idPrefix}email"]`) ||
            formElement.querySelector('[name="email"]');

        const employeeIdInput = formElement.querySelector(`#${idPrefix}employee_id`) ||
            formElement.querySelector(`[name="employee_id"][id="${idPrefix}employee_id"]`) ||
            formElement.querySelector('[name="employee_id"]');

        console.log(`Form ${idPrefix || 'registration'} - Username field found:`, usernameInput !== null);
        console.log(`Form ${idPrefix || 'registration'} - Email field found:`, emailInput !== null);
        console.log(`Form ${idPrefix || 'registration'} - Employee ID field found:`, employeeIdInput !== null);

        // Log selected fields' IDs to help with debugging
        if (usernameInput) console.log(`${idPrefix || 'registration'} username field ID:`, usernameInput.id);
        if (emailInput) console.log(`${idPrefix || 'registration'} email field ID:`, emailInput.id);
        if (employeeIdInput) console.log(`${idPrefix || 'registration'} employee_id field ID:`, employeeIdInput.id);

        // Real-time validation for unique fields
        const setupRealTimeValidation = (input, type) => {
            if (!input) return;

            // Create debounce function to avoid too many requests
            let debounceTimer;

            // Remove any existing listeners to prevent duplicates
            const newInput = input.cloneNode(true);
            if (input.parentNode) {
                input.parentNode.replaceChild(newInput, input);
                input = newInput;
            }

            // Add placeholder text with hint about uniqueness requirement
            const placeholderSuffixes = {
                'username': '(must be unique)',
                'email': '(must be unique)',
                'employee_id': '(must be unique)'
            };

            if (input.placeholder && !input.placeholder.includes('unique')) {
                input.placeholder += ' ' + (placeholderSuffixes[type] || '');
            } else if (!input.placeholder) {
                input.placeholder = `Enter ${type.replace('_', ' ')} ${placeholderSuffixes[type] || ''}`;
            }

            input.addEventListener('input', function () {
                // Don't check if field is readonly
                if (this.readOnly) {
                    console.log(`Field ${type} is readonly, skipping validation`);
                    return;
                }

                // Clear any previous timers
                clearTimeout(debounceTimer);

                // Clear existing validation messages when typing
                let pendingMsg = input.parentElement.querySelector('.pending-feedback');
                let successMsg = input.parentElement.querySelector('.success-feedback');
                if (pendingMsg) pendingMsg.style.display = 'none';
                if (successMsg) successMsg.style.display = 'none';

                // If empty, just clear errors
                if (!this.value.trim()) {
                    clearError(this);
                    return;
                }

                // Set a timer to check after user stops typing
                debounceTimer = setTimeout(() => {
                    console.log(`Checking ${type} after typing stopped:`, this.value);
                    checkUniqueField(type, this.value, this);
                }, 500); // Wait 500ms after typing stops
            });

            // Also keep blur handler for immediate validation when leaving field
            input.addEventListener('blur', function () {
                if (!this.readOnly && this.value.trim()) {
                    // Clear debounce timer to avoid duplicate checks
                    clearTimeout(debounceTimer);
                    console.log(`Checking ${type} on blur:`, this.value);
                    checkUniqueField(type, this.value, this);
                }
            });

            // If the field has Edit button next to it (in edit form), handle the button click
            const editBtn = input.parentElement.querySelector('button[onclick*="readOnly=false"]');
            if (editBtn) {
                console.log(`Found edit button for ${type} field`);
                editBtn.addEventListener('click', function () {
                    // Clear validation state when edit button is clicked
                    clearError(input);
                    input.classList.remove('is-valid');

                    // Remove any existing feedback
                    const feedbacks = input.parentElement.querySelectorAll('.pending-feedback, .success-feedback');
                    feedbacks.forEach(feedback => {
                        feedback.style.display = 'none';
                    });

                    // Focus the input field
                    setTimeout(() => {
                        input.focus();
                    }, 100);
                });
            }
        };

        if (usernameInput) {
            setupRealTimeValidation(usernameInput, 'username');
        }

        if (emailInput) {
            setupRealTimeValidation(emailInput, 'email');
        }

        if (employeeIdInput) {
            setupRealTimeValidation(employeeIdInput, 'employee_id');
        }
    }

    // Attach uniqueness validation to registration form
    attachUniquenessValidation(form, '');

    // Attach uniqueness validation to edit form with prefix
    attachUniquenessValidation(editForm, 'edit_');

    // Validation logic (whole form)
    function validateForm() {
        let valid = true;

        // First pass: validate individual fields
        requiredFieldNames.forEach(fieldName => {
            const input = form.elements.namedItem(fieldName);
            if (input && !validateField(input)) valid = false;
        });

        // Second pass: cross-validate related fields
        Object.keys(fieldDependencies).forEach(fieldName => {
            const input = form.elements.namedItem(fieldName);
            if (!input || !input.value.trim()) return;

            const dependentFields = fieldDependencies[fieldName];
            for (const depFieldName of dependentFields) {
                const depField = form.elements.namedItem(depFieldName);
                if (!depField || !depField.value.trim()) continue;

                // Additional cross-field validations
                if (fieldName === 'address_city' && depFieldName === 'address_state') {
                    // Could implement region-specific validation here
                    // For example, check if city belongs to the selected state/province
                }
            }
        });

        // Also check for any .is-invalid fields
        if (form.querySelector('.is-invalid')) valid = false;
        return valid;
    }

    // Prevent duplicate submissions
    let submitting = false;
    if (form) {
        form.addEventListener('submit', function (e) {
            console.log('Registration form submit event fired');
            if (submitting) {
                e.preventDefault();
                return;
            }
            if (!validateForm()) {
                e.preventDefault();
                console.log('Form validation failed');
                return;
            }
            // Extra safety: prevent if any field is marked invalid
            if (form.querySelector('.is-invalid')) {
                e.preventDefault();
                alert('Please fix all errors in the form before submitting.');
                return;
            }
            submitting = true;
            console.log('Form validation passed, submitting');
            // Optionally: Add loading spinner or disable button
        });
    }

    // --- Helper function for age calculation ---
    function calculateAge(birthdayStr) {
        const birthDate = new Date(birthdayStr);
        if (isNaN(birthDate)) return 0;

        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age > 0 ? age : 0;
    }

    // --- Birthday to Age auto-calculation ---
    const birthdayInput = form ? form.querySelector('#birthday') : null;
    const ageInput = form ? form.querySelector('#age') : null;
    if (birthdayInput && ageInput) {
        // Prevent manual edit of age
        ageInput.readOnly = true;
        birthdayInput.addEventListener('change', function () {
            const age = calculateAge(this.value);
            ageInput.value = age > 0 ? age : '';

            // Validate age is at least 18
            if (age < 18) {
                showError(birthdayInput, 'You must be at least 18 years old.');
            } else {
                clearError(birthdayInput);
            }
        });
    }
    // --- End Birthday to Age auto-calculation ---

    // --- Persist and Restore Form Data ---
    const FORM_STORAGE_KEY = 'adminRegistrationFormData';
    // Restore data if present
    if (form) {
        const savedData = localStorage.getItem(FORM_STORAGE_KEY);
        if (savedData) {
            try {
                const values = JSON.parse(savedData);
                Object.keys(values).forEach(function (key) {
                    const field = form.elements.namedItem(key);
                    if (field) field.value = values[key];
                });
            } catch (e) { console.warn('Could not parse saved form data'); }
        }
        // On input change, save to localStorage
        form.querySelectorAll('input, select, textarea').forEach(function (input) {
            input.addEventListener('input', function () {
                const data = {};
                form.querySelectorAll('input, select, textarea').forEach(function (f) {
                    if (f.name) data[f.name] = f.value;
                });
                localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify(data));
            });
        });
        // On successful submit, clear localStorage
        form.addEventListener('submit', function (e) {
            // You may want to check for errors before clearing, but for now we'll clear on submit
            localStorage.removeItem(FORM_STORAGE_KEY);
        });
    }

    // Additional debug info
    console.log('Registration script initialization complete');
});