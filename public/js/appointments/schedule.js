// Handle edit modal
function toggleEditModal(button) {
    if (!button) return;
    const form = document.getElementById('editAppointmentForm');
    const modal = document.getElementById('editSchedModal');
    const id = button.getAttribute('data-id');
    form.action = "{{ route('appointments.update', '') }}/" + id;
    document.getElementById('scheduleId').value = id;
    const appointmentType = button.getAttribute('data-appointment_type');
    const appointmentTypeSelect = document.getElementById('edit_appointment_type');
    for (let i = 0; i < appointmentTypeSelect.options.length; i++) {
        if (appointmentTypeSelect.options[i].value === appointmentType) {
            appointmentTypeSelect.options[i].selected = true;
            break;
        }
    }
    const idLabel = document.getElementById('edit_id_label');
    if (appointmentType === 'job_order') {
        idLabel.textContent = 'Employee ID';
    } else {
        idLabel.textContent = 'Item No';
    }
    const employeeName = button.getAttribute('data-name');
    if (employeeName) {
        const nameParts = employeeName.split(' ');
        if (nameParts.length >= 2) {
            document.getElementById('editFirstName').value = nameParts[0] || '';
            document.getElementById('editLastName').value = nameParts[nameParts.length - 1] || '';
            if (nameParts.length > 2) {
                const middleParts = nameParts.slice(1, nameParts.length - 1);
                const lastPart = nameParts[nameParts.length - 1];
                const extensions = ['Jr.', 'Sr.', 'II', 'III', 'IV', 'V'];
                if (extensions.includes(lastPart)) {
                    document.getElementById('editExtensionName').value = lastPart;
                    document.getElementById('editLastName').value = nameParts[nameParts.length - 2] || '';
                    document.getElementById('editMiddleName').value = middleParts.slice(0, -1).join(' ');
                } else {
                    document.getElementById('editMiddleName').value = middleParts.join(' ');
                    document.getElementById('editExtensionName').value = '';
                }
            } else {
                document.getElementById('editMiddleName').value = '';
                document.getElementById('editExtensionName').value = '';
            }
        }
    }
    document.getElementById('editEmployeeId').value = '';
    document.getElementById('editGender').value = 'male';
    document.getElementById('editDob').value = '';
    document.getElementById('editAge').value = '0';
    document.getElementById('appointmentPosition').value = button.getAttribute('data-position');
    document.getElementById('appointmentRatePerDay').value = button.getAttribute('data-rate_per_day');
    document.getElementById('appointmentEmploymentStart').value = button.getAttribute('data-employment_start');
    document.getElementById('appointmentEmploymentEnd').value = button.getAttribute('data-employment_end');
    document.getElementById('appointmentSourceOfFund').value = button.getAttribute('data-source_of_fund');
    document.getElementById('appointmentLocation').value = button.getAttribute('data-location') || '';
    const officeAssignment = button.getAttribute('data-office_assignment');
    const officeSelect = document.getElementById('appointmentOfficeAssignment');
    let officeFound = false;
    for (let i = 0; i < officeSelect.options.length; i++) {
        if (officeSelect.options[i].value === officeAssignment) {
            officeSelect.options[i].selected = true;
            officeFound = true;
            break;
        }
    }
    if (!officeFound && officeAssignment) {
        const newOption = new Option(officeAssignment, officeAssignment);
        officeSelect.add(newOption);
        newOption.selected = true;
    }
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap is loaded correctly');
        const addModal = document.getElementById('addSchedModal');
        const editModal = document.getElementById('editSchedModal');
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.modal');
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            });
        });
    } else {
        console.error('Bootstrap is not defined. Modal functionality may not work properly.');
    }
    document.getElementById('appointmentTypeFilter').addEventListener('change', function() {
        const selectedType = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('appointment_type', selectedType);
        window.location.href = url.toString();
    });
    const addAppointmentType = document.getElementById('appointment_type');
    const addIdLabel = document.getElementById('id_label');
    if (addAppointmentType.value === 'job_order') {
        addIdLabel.textContent = 'Employee ID';
    } else if (addAppointmentType.value !== '') {
        addIdLabel.textContent = 'Item No';
    }
    function calculateAge(birthdate) {
        const dob = new Date(birthdate);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        return age;
    }
    document.getElementById('dob').addEventListener('change', function() {
        const age = calculateAge(this.value);
        document.getElementById('age').value = age;
    });
    document.getElementById('editDob').addEventListener('change', function() {
        const age = calculateAge(this.value);
        document.getElementById('editAge').value = age;
    });
    document.getElementById('appointment_type').addEventListener('change', function() {
        const selectedType = this.value;
        const idLabel = document.getElementById('id_label');
        if (selectedType === 'job_order') {
            idLabel.textContent = 'Employee ID';
        } else {
            idLabel.textContent = 'Item No';
        }
    });
    document.getElementById('edit_appointment_type').addEventListener('change', function() {
        const selectedType = this.value;
        const idLabel = document.getElementById('edit_id_label');
        if (selectedType === 'job_order') {
            idLabel.textContent = 'Employee ID';
        } else {
            idLabel.textContent = 'Item No';
        }
    });
});

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

document.getElementById('csvFile').addEventListener('change', function() {
    const fileName = this.files[0].name;
    const fileSize = this.files[0].size / 1024 / 1024;
    const fileSizeElement = document.querySelector('.file-size');
    if (fileSizeElement) {
        fileSizeElement.textContent = fileSize.toFixed(2) + ' MB';
    }
});

document.getElementById('csvImportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('importSubmitBtn');
    const progressBar = document.querySelector('.progress-bar');
    const progressContainer = document.querySelector('.progress-container');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Importing...';
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    let progress = 0;

    const fileInput = document.getElementById('csvFile');
    if (!fileInput.files.length) {
        alert('Please select a CSV file.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Import';
        return;
    }
    const file = fileInput.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
        const csv = event.target.result;
        const rows = csv.split(/\r?\n/).filter(r => r.trim().length > 0);
        if (rows.length < 2) {
            alert('CSV file must have at least one data row.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Import';
            return;
        }
        // Parse header
        const header = rows[0].split(',').map(h => h.trim().toLowerCase());
        // Required fields based on Add Personnel modal
        const required = [
            'name', 'position', 'rate_per_day', 'employment_start', 'employment_end', 'source_of_fund', 'location', 'office_assignment', 'appointment_type', 'item_no', 'employee_id', 'first_name', 'middle_name', 'last_name', 'extension_name', 'gender', 'birthday', 'age'
        ];
        // Map header to indices
        const headerMap = {};
        header.forEach((h, i) => { headerMap[h] = i; });
        // Validate required fields
        for (const field of required) {
            if (!(field in headerMap)) {
                alert('Missing required column: ' + field);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Import';
                return;
            }
        }
        // Build data array
        const data = [];
        for (let i = 1; i < rows.length; i++) {
            const cols = rows[i].split(',');
            if (cols.length < header.length) continue; // skip incomplete rows
            const row = {};
            required.forEach(field => {
                row[field] = cols[headerMap[field]] ? cols[headerMap[field]].trim() : '';
            });
            data.push(row);
        }
        // AJAX POST to backend
        fetch('/appointments/import', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({appointments: data})
        })
        .then(response => response.json())
        .then(result => {
            progressBar.style.width = '100%';
            if (result.success) {
                submitBtn.textContent = 'Imported!';
                location.reload();
            } else {
                let errorMsg = result.message || 'Import failed.';
                if (result.errors && Array.isArray(result.errors)) {
                    errorMsg += '\n' + result.errors.join('\n');
                }
                alert(errorMsg);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Import';
            }
        })
        .catch(err => {
            alert('Import failed.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Import';
        });
    };
    reader.readAsText(file);
});

document.getElementById('importCsvModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('csvImportForm').reset();
    const progressBar = document.querySelector('.progress-bar');
    const progressContainer = document.querySelector('.progress-container');
    if (progressBar) {
        progressBar.style.width = '0%';
    }
    if (progressContainer) {
        progressContainer.style.display = 'none';
    }
    const submitBtn = document.getElementById('importSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Import';
    }
});
