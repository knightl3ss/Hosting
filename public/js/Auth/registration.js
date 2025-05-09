// DEBUG: Confirm JS is loaded
console.log('registration.js loaded');

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('adminRegistrationForm');
    if (!form) {
        console.warn('adminRegistrationForm not found');
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
        input.classList.add('is-invalid');
    }

    // Helper: Clear error message
    function clearError(input) {
        let errorElem = input.parentElement.querySelector('.invalid-feedback');
        if (errorElem) errorElem.textContent = '';
        input.classList.remove('is-invalid');
    }

    // --- Philippine Mobile Number Validation ---
    function isValidPhilippineMobile(number) {
        // Accepts +63 XXX-XXX-XXXX, +63XXXXXXXXXX, 09XXXXXXXXX, 09XX XXX XXXX
        const pattern = /^(\+63|0)9\d{2}[- ]?\d{3}[- ]?\d{4}$/;
        return pattern.test(number.trim());
    }

    // --- Password Validation ---
    function isValidPassword(password) {
        // Min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/.test(password);
    }

    // --- Confirm Password Validation ---
    const passwordInput = form.querySelector('#password');
    const confirmPasswordInput = form.querySelector('#confirm_password');
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
    const restrictedDomains = ['@test.com', '@blocked.com', '@example.com', '@root.com', '@superuser.com','@user.com'];
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
        passwordInput.addEventListener('input', function() {
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
    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', () => {
            validateField(input);
        });
        input.addEventListener('blur', () => {
            validateField(input);
        });
    });

    // --- Restrict Leading Spaces for All Text Inputs ---
    form.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('input', function() {
            // Remove leading spaces
            let val = input.value;
            val = val.replace(/^\s+/, '');
            if (val !== input.value) {
                input.value = val;
            }
        });
    });

    // --- Name Fields: Restrict Numbers, Prevent Leading Spaces ---
    const nameFields = ['first_name', 'middle_name', 'last_name', 'extension_name'];
    nameFields.forEach(fieldId => {
        const field = form.querySelector(`#${fieldId}`);
        if (field) {
            field.addEventListener('input', function(e) {
                // Remove any digit
                val = val.replace(/[0-9]/g, '');
            });
        }
    });

    // --- Uniqueness Check for Username, Email, Employee ID ---
    function checkUniqueField(type, value, input) {
        if (!value) return;
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
            if (data.exists) {
                let msg = `${type.replace('_', ' ')} is already taken.`;
                showError(input, msg.charAt(0).toUpperCase() + msg.slice(1));
            } else {
                clearError(input);
            }
        })
        .catch(() => {
            showError(input, 'Could not validate uniqueness.');
        });
    }

    // Attach uniqueness validation on blur
    const usernameInput = form.querySelector('[name="username"]');
    const emailInput = form.querySelector('[name="email"]');
    const employeeIdInput = form.querySelector('[name="employee_id"]');
    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            checkUniqueField('username', usernameInput.value, usernameInput);
        });
    }
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            checkUniqueField('email', emailInput.value, emailInput);
        });
    }
    if (employeeIdInput) {
        employeeIdInput.addEventListener('blur', function() {
            checkUniqueField('employee_id', employeeIdInput.value, employeeIdInput);
        });
    }

    // Validation logic (whole form)
    function validateForm() {
        let valid = true;
        requiredFieldNames.forEach(fieldName => {
            const input = form.elements.namedItem(fieldName);
            if (input && !validateField(input)) valid = false;
        });
        // Also check for any .is-invalid fields
        if (form.querySelector('.is-invalid')) valid = false;
        return valid;
    }

    // Prevent duplicate submissions
    let submitting = false;
    form.addEventListener('submit', function (e) {
        if (submitting) {
            e.preventDefault();
            return;
        }
        if (!validateForm()) {
            e.preventDefault();
            return;
        }
        // Extra safety: prevent if any field is marked invalid
        if (form.querySelector('.is-invalid')) {
            e.preventDefault();
            alert('Please fix all errors in the form before submitting.');
            return;
        }
        submitting = true;
        // Optionally: Add loading spinner or disable button
    });

    // --- Birthday to Age auto-calculation ---
    const birthdayInput = form.querySelector('#birthday');
    const ageInput = form.querySelector('#age');
    if (birthdayInput && ageInput) {
        // Prevent manual edit of age
        ageInput.readOnly = true;
        birthdayInput.addEventListener('change', function () {
            const birthDate = new Date(this.value);
            if (isNaN(birthDate)) {
                ageInput.value = '';
                return;
            }
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            ageInput.value = age > 0 ? age : '';
        });
    }
    // --- End Birthday to Age auto-calculation ---

    // --- Persist and Restore Form Data ---
    const FORM_STORAGE_KEY = 'adminRegistrationFormData';
    // Restore data if present
    const savedData = localStorage.getItem(FORM_STORAGE_KEY);
    if (savedData) {
        try {
            const values = JSON.parse(savedData);
            Object.keys(values).forEach(function(key) {
                const field = form.elements.namedItem(key);
                if (field) field.value = values[key];
            });
        } catch (e) { console.warn('Could not parse saved form data'); }
    }
    // On input change, save to localStorage
    form.querySelectorAll('input, select, textarea').forEach(function(input) {
        input.addEventListener('input', function() {
            const data = {};
            form.querySelectorAll('input, select, textarea').forEach(function(f) {
                if (f.name) data[f.name] = f.value;
            });
            localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify(data));
        });
    });
    // On successful submit, clear localStorage
    form.addEventListener('submit', function(e) {
        // You may want to check for errors before clearing, but for now we'll clear on submit
        localStorage.removeItem(FORM_STORAGE_KEY);
    });
});