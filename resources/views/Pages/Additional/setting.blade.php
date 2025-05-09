@extends('Layout.app')

@section('content')
<div class="container-fluid">
    <div class="settings-wrapper">
        <!-- Settings Header -->
        <div class="settings-header">
            <h4 class="settings-title">Settings</h4>
            <p class="settings-subtitle">Manage your account settings and preferences</p>
        </div>

        <!-- Settings Content -->
        <div class="row">
            <!-- Settings Navigation -->
            <div class="col-md-3">
                <div class="settings-nav card">
                    <div class="list-group list-group-flush" id="settings-tabs" role="tablist">
                        <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#account" role="tab">
                            <i class="fas fa-user-circle me-2"></i>Account
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#security" role="tab">
                            <i class="fas fa-shield-alt me-2"></i>Security
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#notifications" role="tab">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#appearance" role="tab">
                            <i class="fas fa-paint-brush me-2"></i>Appearance
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#privacy" role="tab">
                            <i class="fas fa-lock me-2"></i>Privacy
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Account Settings -->
                    <div class="tab-pane fade show active" id="account" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Account Settings</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="johndoe">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="john.doe@example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Language</label>
                                        <select class="form-select">
                                            <option value="en">English</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Security Settings</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="twoFactorAuth">
                                            <label class="form-check-label" for="twoFactorAuth">Enable Two-Factor Authentication</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Notification Preferences</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                                            <label class="form-check-label" for="emailNotif">Email Notifications</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="browserNotif">
                                            <label class="form-check-label" for="browserNotif">Browser Notifications</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="updateNotif" checked>
                                            <label class="form-check-label" for="updateNotif">System Update Notifications</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Preferences</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance Settings -->
                    <div class="tab-pane fade" id="appearance" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Appearance Settings</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Theme</label>
                                        <select class="form-select">
                                            <option value="light">Light</option>
                                            <option value="dark">Dark</option>
                                            <option value="system">System Default</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Font Size</label>
                                        <select class="form-select">
                                            <option value="small">Small</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="large">Large</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Apply Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div class="tab-pane fade" id="privacy" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Privacy Settings</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="profileVisibility" checked>
                                            <label class="form-check-label" for="profileVisibility">Profile Visibility</label>
                                        </div>
                                        <small class="text-muted">Make your profile visible to other users</small>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="activityStatus" checked>
                                            <label class="form-check-label" for="activityStatus">Online Status</label>
                                        </div>
                                        <small class="text-muted">Show when you're active</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Privacy</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.settings-wrapper {
    padding: 20px;
}

.settings-header {
    margin-bottom: 30px;
}

.settings-title {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
}

.settings-subtitle {
    color: #6c757d;
    margin: 5px 0 0;
}

.settings-nav {
    margin-bottom: 20px;
}

.settings-nav .list-group-item {
    border: none;
    padding: 12px 20px;
    color: #2c3e50;
    background: transparent;
    border-radius: 0;
}

.settings-nav .list-group-item:hover {
    background-color: rgba(0,0,0,0.05);
}

.settings-nav .list-group-item.active {
    background-color: #4e73df;
    color: #fff;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: #fff;
    border-bottom: 1px solid #e3e6f0;
    padding: 15px 20px;
}

.card-title {
    color: #2c3e50;
}

.card-body {
    padding: 20px;
}

.form-label {
    font-weight: 500;
    color: #2c3e50;
}

.form-control,
.form-select {
    border: 1px solid #e3e6f0;
    border-radius: 4px;
    padding: 8px 12px;
}

.form-control:focus,
.form-select:focus {
    border-color: #4e73df;
    box-shadow: none;
}

.form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2e59d9;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .settings-nav {
        margin-bottom: 20px;
    }
    
    .settings-nav .list-group-item {
        padding: 10px 15px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .settings-wrapper {
        padding: 10px;
    }
    
    .settings-header {
        margin-bottom: 20px;
    }
    
    .settings-title {
        font-size: 20px;
    }
    
    .card-header {
        padding: 12px 15px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tab switching
    const tabs = document.querySelectorAll('[data-bs-toggle="list"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            // You can add logic here when tabs are switched
            console.log('Tab switched to:', e.target.getAttribute('href'));
        });
    });
    
    // Handle form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your form submission logic here
            console.log('Form submitted:', e.target);
        });
    });
    
    // Handle theme changes
    const themeSelect = document.querySelector('select[name="theme"]');
    if (themeSelect) {
        themeSelect.addEventListener('change', function(e) {
            // Add your theme switching logic here
            console.log('Theme changed to:', e.target.value);
        });
    }
});
</script>
@endpush
@endsection