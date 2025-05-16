<!-- Unblock Account Modal -->
<div class="modal fade" id="unblockAccountModal" tabindex="-1" aria-labelledby="unblockAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unblockAccountModalLabel">Unblock Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Confirmation:</strong> You are about to unblock the account <strong id="username-to-unblock"></strong>.
                    <p class="mt-2 mb-0">This will allow the user to log in to the system again.</p>
                </div>
                <form id="unblockAccountForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="confirm_unblock_username" class="form-label">Type the username to confirm</label>
                        <input type="text" class="form-control" id="confirm_unblock_username" placeholder="Enter username to confirm" required>
                        <div class="invalid-feedback">
                            Username does not match.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="unblockAccountForm" class="btn btn-success" id="confirmUnblockBtn">Unblock Account</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Unblock Account Button Clicks
        const unblockButtons = document.querySelectorAll('.unblock-account-btn');
        const unblockForm = document.getElementById('unblockAccountForm');
        const confirmUnblockUsernameInput = document.getElementById('confirm_unblock_username');
        const usernameToUnblockElement = document.getElementById('username-to-unblock');
        
        if (unblockButtons && unblockForm && confirmUnblockUsernameInput) {
            unblockButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Get the user ID and username from the button's data attributes
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    
                    // Update the unblock form action with the correct user ID
                    unblockForm.action = `{{ route('unblock_account', '') }}/${userId}`;
                    
                    // Update the confirmation message with the username
                    if (usernameToUnblockElement) {
                        usernameToUnblockElement.textContent = `"${username}"`;
                    }
                    
                    // Clear the confirmation input field
                    if (confirmUnblockUsernameInput) {
                        confirmUnblockUsernameInput.value = '';
                        confirmUnblockUsernameInput.classList.remove('is-valid', 'is-invalid');
                        confirmUnblockUsernameInput.dataset.usernameToMatch = username;
                    }
                });
            });
            
            // Form validation
            unblockForm.addEventListener('submit', function(event) {
                const usernameToMatch = confirmUnblockUsernameInput.dataset.usernameToMatch || '';
                
                // Validate username confirmation
                if (confirmUnblockUsernameInput.value !== usernameToMatch) {
                    event.preventDefault();
                    confirmUnblockUsernameInput.classList.add('is-invalid');
                    return false;
                }
                
                // If we get here, form is valid
                confirmUnblockUsernameInput.classList.remove('is-invalid');
                return true;
            });
            
            // Real-time validation
            confirmUnblockUsernameInput.addEventListener('input', function() {
                const usernameToMatch = this.dataset.usernameToMatch || '';
                if (this.value === usernameToMatch) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    if (this.value.length > 0) {
                        this.classList.add('is-invalid');
                    }
                }
            });
        }
    });
</script>
