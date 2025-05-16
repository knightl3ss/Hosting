// Service Record Modal Validation (Add/Edit)
// Attach this script to pages with service record modals

document.addEventListener('DOMContentLoaded', function () {
    // Helper functions for error display
    function showError(input, message) {
        input.classList.add('is-invalid');
        let feedback = input.parentElement.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentElement.appendChild(feedback);
        }
        feedback.textContent = message;
    }
    function clearError(input) {
        input.classList.remove('is-invalid');
        let feedback = input.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = '';
    }

    // Attach validation to all service record forms (add/edit)
    document.querySelectorAll('.service-record-form').forEach(function (form) {
        const dateFrom = form.querySelector('[name="date_from"]');
        const dateTo = form.querySelector('[name="date_to"]');
        const salary = form.querySelector('[name="salary"]');
        const designation = form.querySelector('[name="designation"]');
        const station = form.querySelector('[name="station"]');
        const serviceStatus = form.querySelector('[name="service_status"]');
        const separationDate = form.querySelector('[name="separation_date"]');
        const statusSalary = form.querySelector('[name="status"]');
        const paymentFrequency = form.querySelector('[name="payment_frequency"]');

        // Date validation
        if (dateFrom && dateTo) {
            dateFrom.addEventListener('input', function () {
                validateDateFrom(dateFrom);
                validateDateTo(dateTo, dateFrom);
            });
            dateTo.addEventListener('input', function () {
                validateDateTo(dateTo, dateFrom);
            });
        }
        // Salary validation
        if (salary) {
            salary.addEventListener('input', function () {
                validateSalary(salary, statusSalary, paymentFrequency);
            });
        }
        // Designation validation
        if (designation) {
            designation.addEventListener('input', function () {
                validateDesignation(designation);
            });
        }
        // Station validation
        if (station) {
            station.addEventListener('input', function () {
                validateStation(station);
            });
        }
        // Service status and separation date validation
        if (serviceStatus && separationDate) {
            serviceStatus.addEventListener('change', function () {
                validateServiceStatus(serviceStatus, separationDate);
            });
            separationDate.addEventListener('input', function () {
                validateSeparationDate(separationDate, serviceStatus);
            });
        }

        // On submit, validate all
        form.addEventListener('submit', function (event) {
            let isValid = true;
            if (dateFrom) isValid = validateDateFrom(dateFrom) && isValid;
            if (dateTo && dateFrom) isValid = validateDateTo(dateTo, dateFrom) && isValid;
            if (salary && statusSalary && paymentFrequency) isValid = validateSalary(salary, statusSalary, paymentFrequency) && isValid;
            if (designation) isValid = validateDesignation(designation) && isValid;
            if (station) isValid = validateStation(station) && isValid;
            if (serviceStatus && separationDate) isValid = validateServiceStatus(serviceStatus, separationDate) && isValid;
            if (separationDate && serviceStatus) isValid = validateSeparationDate(separationDate, serviceStatus) && isValid;
            if (!isValid) event.preventDefault();
        });
    });

    // Validation functions
    function validateDateFrom(input) {
        clearError(input);
        const date = new Date(input.value);
        const currentDate = new Date();
        if (input.value && date > currentDate) {
            showError(input, 'From date cannot be in the future');
            return false;
        }
        return true;
    }
    function validateDateTo(input, fromInput) {
        clearError(input);
        const fromDate = new Date(fromInput.value);
        const toDate = new Date(input.value);
        const currentDate = new Date();
        if (!input.value || !fromInput.value) return true;
        if (toDate > currentDate) {
            showError(input, 'To date cannot be in the future');
            return false;
        }
        if (toDate.getTime() < fromDate.getTime()) {
            showError(input, 'To date cannot be earlier than From date');
            return false;
        }
        const diffTime = Math.abs(toDate.getTime() - fromDate.getTime());
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (diffDays < 1) {
            showError(input, 'Service period must be at least 1 day');
            return false;
        }
        // Max period by status
        const form = input.closest('form');
        const statusInput = form.querySelector('[name="status"]');
        const serviceStatusInput = form.querySelector('[name="service_status"]');
        const status = statusInput ? statusInput.value.toLowerCase() : '';
        const serviceStatus = serviceStatusInput ? serviceStatusInput.value : '';
        let maxPeriod = 3650; // 10 years default
        if (status === 'job_order' || status === 'probationary') maxPeriod = 180;
        if (status === 'part_time') maxPeriod = 365;
        if (serviceStatus === 'Suspension' && diffDays > 90) {
            showError(input, 'Suspension period cannot exceed 90 days.');
            return false;
        }
        if (serviceStatus === 'On Leave' && diffDays > 30) {
            showError(input, 'Leave period cannot exceed 30 days.');
            return false;
        }
        if (diffDays > maxPeriod) {
            showError(input, `Service period cannot exceed ${maxPeriod} days for this status.`);
            return false;
        }
        const diffYears = diffDays / 365;
        if (diffYears > 10) {
            showError(input, 'Service period cannot exceed 10 years');
            return false;
        }
        return true;
    }
    function validateSalary(input, statusInput, paymentInput) {
        clearError(input);
        const salary = parseFloat(input.value);
        // Normalize status and payment values
        const status = statusInput ? statusInput.value.trim().toLowerCase().replace(/\s+/g, '_') : '';
        const payment = paymentInput ? paymentInput.value.trim().toLowerCase() : '';
        if (isNaN(salary) || salary <= 0) {
            showError(input, 'Salary must be greater than zero');
            return false;
        }
        // Salary min/max per status (approximate, client-side only)
        let minSalary = 0, maxSalary = 1000000;
        if (status === 'job_order') {
            minSalary = 500; maxSalary = 5000;
            if (payment !== 'daily') {
                showError(input, 'Job Order positions must be paid on a daily basis');
                return false;
            }
        } else if (status === 'regularpermanent') {
            minSalary = 120000; maxSalary = 1000000;
        } else if (status === 'contractual') {
            minSalary = 96000; maxSalary = 800000;
        } else if (status === 'temporary') {
            minSalary = 84000; maxSalary = 600000;
        } else if (status === 'casual') {
            minSalary = 96000; maxSalary = 800000;
            if (payment === 'daily') { minSalary = 400; maxSalary = 800; }
        } else if (status === 'elected') {
            minSalary = 180000; maxSalary = 1500000;
        } else if (status === 'provisional') {
            minSalary = 90000; maxSalary = 700000;
        } else if (status === 'coterminous') {
            minSalary = 150000; maxSalary = 1200000;
        } else if (status === 'coterminoustemporary') {
            minSalary = 120000; maxSalary = 900000;
        } else if (status === 'substitute') {
            minSalary = 120000; maxSalary = 900000;
            if (payment === 'daily') { minSalary = 600; maxSalary = 1000; }
        } else if (status === 'part_time') {
            minSalary = 0; maxSalary = 25000;
        }
        // Convert to annual if needed
        let annualSalary = salary;
        if (payment === 'monthly') annualSalary = salary * 12;
        if (payment === 'daily') annualSalary = salary * 365;
        if (payment === 'daily' && (status === 'job_order' || status === 'casual' || status === 'substitute')) {
            if (salary < minSalary || salary > maxSalary) {
                showError(input, `Daily rate for this position must be between ₱${minSalary} and ₱${maxSalary}`);
                return false;
            }
        } else if (status && payment !== 'daily') {
            if (annualSalary < minSalary) {
                showError(input, `Annual salary for this position must be at least ₱${minSalary}`);
                return false;
            }
            if (annualSalary > maxSalary) {
                showError(input, `Annual salary for this position cannot exceed ₱${maxSalary}`);
                return false;
            }
        }
        if (status === 'job_order' && salary < 500) {
            showError(input, 'Daily rate for Job Order position must be at least ₱500/day.');
            return false;
        }
        return true;
    }
    function validateDesignation(input) {
        clearError(input);
        if (!/^[A-Za-z\s\-\.]+$/.test(input.value)) {
            showError(input, 'Designation should only contain letters, spaces, hyphens, and periods');
            return false;
        }
        if (input.value.length < 3) {
            showError(input, 'Designation must be at least 3 characters long');
            return false;
        }
        if (input.value.length > 255) {
            showError(input, 'Designation cannot exceed 255 characters');
            return false;
        }
        return true;
    }
    function validateStation(input) {
        clearError(input);
        // Allow letters, numbers, spaces, and common address characters (.,-,#)
        if (!/^[A-Za-z0-9\s\-\.,#]+$/.test(input.value)) {
            showError(input, 'Station can only contain letters, numbers, spaces, and common address characters (.,-,#)');
            return false;
        }
        if (input.value.trim().length < 3) {
            showError(input, 'Station place must be at least 3 characters long');
            return false;
        }
        if (input.value.length > 255) {
            showError(input, 'Station cannot exceed 255 characters');
            return false;
        }
        return true;
    }
    function validateServiceStatus(select, separationInput) {
        clearError(select);
        if (select.value === 'Not in Service' && !separationInput.value.trim()) {
            showError(select, 'Separation date is required when service status is "Not in Service"');
            return false;
        }
        return true;
    }
    function validateSeparationDate(input, statusSelect) {
        clearError(input);
        if (statusSelect.value === 'In Service' && input.value.trim()) {
            showError(input, 'Separation date should be empty for "In Service" status');
            return false;
        }
        return true;
    }

    // --- Date min/max logic for add/edit modals ---
    // Get service records from a global JS variable if available
    let serviceRecords = window.serviceRecords || [];

    // Add min/max to add modal
    const addForm = document.getElementById('addServiceRecordForm');
    if (addForm) {
        const dateFrom = addForm.querySelector('[name="date_from"]');
        const dateTo = addForm.querySelector('[name="date_to"]');
        if (serviceRecords.length > 0) {
            // Get latest to date
            const lastRecord = serviceRecords[serviceRecords.length - 1];
            if (lastRecord && lastRecord.date_to) {
                const minFrom = new Date(lastRecord.date_to);
                minFrom.setDate(minFrom.getDate() + 1);
                const minFromStr = minFrom.toISOString().slice(0, 10);
                dateFrom.min = minFromStr;
                dateTo.min = minFromStr;
            }
        }
        // Prevent manual input of old dates
        dateFrom.addEventListener('input', function () {
            if (dateFrom.min && dateFrom.value < dateFrom.min) {
                showError(dateFrom, `From date cannot be before ${dateFrom.min}`);
            } else {
                clearError(dateFrom);
            }
        });
    }

    // Add min/max to edit modals
    document.querySelectorAll('.modal').forEach(function (modal) {
        if (!modal.id.startsWith('editServiceRecordModal')) return;
        const form = modal.querySelector('form');
        if (!form) return;
        const recordId = form.querySelector('[name="record_id"]').value;
        const dateFrom = form.querySelector('[name="date_from"]');
        const dateTo = form.querySelector('[name="date_to"]');
        const idx = serviceRecords.findIndex(r => String(r.id) === String(recordId));
        if (idx !== -1) {
            // Previous record's date_to
            if (idx > 0 && serviceRecords[idx - 1].date_to) {
                const prevTo = new Date(serviceRecords[idx - 1].date_to);
                prevTo.setDate(prevTo.getDate() + 1);
                dateFrom.min = prevTo.toISOString().slice(0, 10);
            } else {
                dateFrom.removeAttribute('min');
            }
            // Next record's date_from
            if (idx < serviceRecords.length - 1 && serviceRecords[idx + 1].date_from) {
                const nextFrom = new Date(serviceRecords[idx + 1].date_from);
                nextFrom.setDate(nextFrom.getDate() - 1);
                dateTo.max = nextFrom.toISOString().slice(0, 10);
            } else {
                dateTo.removeAttribute('max');
            }
        }
        // Prevent manual input of old dates
        dateFrom.addEventListener('input', function () {
            if (dateFrom.min && dateFrom.value < dateFrom.min) {
                showError(dateFrom, `From date cannot be before ${dateFrom.min}`);
            } else {
                clearError(dateFrom);
            }
        });
    });
}); 