// editPersonnel.js - Handles the edit personnel modal population and AJAX update

document.addEventListener('DOMContentLoaded', function() {
    // Use data-* attributes to populate modal fields when edit button is clicked
    document.querySelectorAll('.edit-personnel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Fill modal fields using data-* attributes
            document.getElementById('personnelId').value = this.dataset.id || '';
            document.getElementById('editItemNo').value = this.dataset.item_no || '';
            document.getElementById('editPosition').value = this.dataset.position || '';
            document.getElementById('editSalaryGrade').value = this.dataset.salary_grade || '';
            document.getElementById('editAuthorizedSalary').value = this.dataset.authorized_salary || '';
            document.getElementById('editActualSalary').value = this.dataset.actual_salary || '';
            document.getElementById('editStep').value = this.dataset.step || '';
            document.getElementById('editCode').value = this.dataset.code || '';
            document.getElementById('editType').value = this.dataset.type || '';
            document.getElementById('editLevel').value = this.dataset.level || '';
            document.getElementById('editLastName').value = this.dataset.last_name || '';
            document.getElementById('editFirstName').value = this.dataset.first_name || '';
            document.getElementById('editMiddleName').value = this.dataset.middle_name || '';
            document.getElementById('editBirthday').value = this.dataset.birthday || '';
            document.getElementById('editEmploymentStart').value = this.dataset.employment_start || '';
            document.getElementById('editEmploymentEnd').value = this.dataset.employment_end || '';
            document.getElementById('editStatus').value = this.dataset.status || '';
            // You can still fetch via AJAX if you want to refresh from backend
        });
    });

    // Populate modal fields when modal content is loaded via AJAX
    const modal = document.getElementById('editPersonnelModal');
    if (!modal) return;

    // Delegate event for when the modal is shown
    modal.addEventListener('show.bs.modal', function (event) {
        // Optionally, you can do something when the modal is shown
    });

    // Save changes button
    const saveBtn = modal.querySelector('#saveChangesBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', async function() {
            const form = modal.querySelector('#editPersonnelForm');
            const spinner = document.getElementById('editPersonnelSpinner');
            const alertBox = document.getElementById('editPersonnelAlert');
            // Hide previous alerts
            alertBox.classList.add('d-none');
            alertBox.innerHTML = '';
            // Show spinner
            spinner.classList.remove('d-none');
            // Clear previous validation
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            // Gather form data
            const formData = new FormData(form);
            const personnelId = formData.get('personnel_id');
            try {
                const response = await fetch(`/personnel/${personnelId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                const data = await response.json();
                spinner.classList.add('d-none');
                if (response.ok) {
                    alertBox.classList.remove('d-none', 'alert-danger');
                    alertBox.classList.add('alert-success');
                    alertBox.innerHTML = 'Personnel updated successfully!';
                    setTimeout(() => {
                        alertBox.classList.add('d-none');
                        $('#editPersonnelModal').modal('hide');
                        window.location.reload();
                    }, 1200);
                } else if (response.status === 422 && data.errors) {
                    // Validation errors
                    Object.entries(data.errors).forEach(([field, messages]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        const feedback = document.getElementById(field + 'Error');
                        if (input) input.classList.add('is-invalid');
                        if (feedback) feedback.textContent = messages[0];
                    });
                    alertBox.classList.remove('d-none', 'alert-success');
                    alertBox.classList.add('alert-danger');
                    alertBox.innerHTML = 'Please fix the errors below.';
                } else {
                    alertBox.classList.remove('d-none', 'alert-success');
                    alertBox.classList.add('alert-danger');
                    alertBox.innerHTML = data.message || 'An error occurred.';
                }
            } catch (err) {
                spinner.classList.add('d-none');
                alertBox.classList.remove('d-none', 'alert-success');
                alertBox.classList.add('alert-danger');
                alertBox.innerHTML = 'Failed to update personnel.';
            }
        });
    }

    // Reset modal on close
    modal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('editPersonnelForm').reset();
        document.getElementById('editPersonnelAlert').classList.add('d-none');
        document.getElementById('editPersonnelSpinner').classList.add('d-none');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    });
});
