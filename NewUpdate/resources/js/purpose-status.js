document.addEventListener('DOMContentLoaded', function() {
    // Handle purpose selection change
    const purposeSelect = document.getElementById('purpose_type');
    const otherPurposeContainer = document.querySelector('.other-purpose-container');
    
    if (purposeSelect) {
        purposeSelect.addEventListener('change', function() {
            // Show/hide other purpose field based on selection
            if (this.value === 'other') {
                otherPurposeContainer.classList.remove('d-none');
            } else {
                otherPurposeContainer.classList.add('d-none');
            }
        });
    }
    
    // Handle update status button clicks
    const updateStatusButtons = document.querySelectorAll('.update-status-btn');
    console.log('Found update buttons:', updateStatusButtons.length);
    
    updateStatusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const purposeId = this.getAttribute('data-purpose-id');
            const purposeText = this.getAttribute('data-purpose-text');
            const requestedDate = this.getAttribute('data-requested-date');
            
            console.log('Button clicked:', {purposeId, purposeText, requestedDate});
            
            document.getElementById('purpose_id').value = purposeId;
            document.getElementById('purpose_text').textContent = purposeText;
            document.getElementById('requested_date').textContent = requestedDate;
        });
    });
    
    // Add form submission debugging
    const statusForm = document.querySelector('#updateStatusModal form');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            const purposeId = document.getElementById('purpose_id').value;
            const status = document.getElementById('status').value;
            
            console.log('Form submitted:', {purposeId, status});
            
            // If purposeId is empty, prevent submission
            if (!purposeId) {
                e.preventDefault();
                alert('Purpose ID is missing. Please try again.');
                console.error('Form submission prevented: Purpose ID is missing');
            }
        });
    }
});