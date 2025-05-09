@extends('Layout.app')

@section('content')
<div class="container-fluid px-2 py-2 profile-container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-3">
        <div class="col-12">
            <div class="card profile-header-card shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="d-flex flex-column flex-md-row align-items-center w-100">
                            <div class="me-md-3 mb-2 mb-md-0 text-center">
                                <img src="{{ asset('storage/' . str_replace('storage/', '', $profile_picture ?? 'default-profile.png')) }}" 
                                     alt="Profile Picture" 
                                     class="rounded-circle shadow-sm profile-image" 
                                     style="width: 200px; height: 200px; object-fit: fit-content;">
                            </div>
                            <div class="profile-info text-center text-md-start flex-grow-1">
                                <div class="user-name mb-2">
                                    <h2 class="h5 mb-0">{{ $first_name }} {{ $middle_name ?? '' }} {{ $last_name }}</h2>
                                </div>
                                <div class="profile-stats d-flex justify-content-center justify-content-md-start gap-3 mt-2">
                                    <div class="role-status-item role-item">
                                        <small class="d-block">Role</small>
                                        <strong class="small">{{ ucfirst($role) }}</strong>
                                    </div>
                                    <div class="role-status-item status-{{ strtolower($status) }}">
                                        <small class="d-block">Status</small>
                                        <strong class="small">{{ ucfirst($status) }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="ms-md-auto mt-2 mt-md-0 text-center">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Personal Information -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 small">
                        <i class="fas fa-user me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-8 mb-2">
                            <label class="form-label small mb-1">Name</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $first_name }} {{ $middle_name ?? 'N/A' }} {{ $last_name }} {{ $extension_name ?? 'N/A' }}" readonly>
                        </div>     
                        <div class="col-4 mb-2">
                            <label class="form-label small mb-1">Employee ID</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $employee_id }}" readonly>
                        </div>        
                        <div class="col-4 mb-2">
                            <label class="form-label small mb-1">Gender</label>
                            <input type="text" class="form-control form-control-sm" value="{{ ucfirst($gender) }}" readonly>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label small mb-1">Age</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $age }}" readonly>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label small mb-1">Birthday</label>
                            <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($birthday)->format('M d, Y') }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label small mb-1">Current Address</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $address_street ?? 'N/A' }}, {{ $address_city ?? 'N/A' }}, {{ $address_state ?? 'N/A' }}, {{ $address_postal_code ?? 'N/A' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 small">
                        <i class="fas fa-user-shield me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <label class="form-label small mb-1">Email Address</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" value="{{ $email }}" readonly>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label small mb-1">Phone Number</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" value="{{ $phone_number ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label small mb-1">Role</label>
                            <input type="text" class="form-control form-control-sm" value="{{ ucfirst($role) }}" readonly>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label small mb-1">Account Status</label>
                            <input type="text" class="form-control form-control-sm" value="{{ ucfirst($status) }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Section -->
    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 small">
                        <i class="fas fa-shield-alt me-2"></i>Account Security
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-12 col-md-6 mb-2">
                            <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <button class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#twoFactorModal">
                                <i class="fas fa-lock me-2"></i>Two-Factor Auth
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Pages/Additional/profile/EditProfileModal')
<script>
    $(document).ready(function() {
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var recipient = button.data('whatever'); // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here:
            var modal = $(this);
            modal.find('.modal-title').text('Edit Profile');
            modal.find('.modal-body input').val(recipient);
        });
    });

    function previewProfilePicture(input) {
        const preview = document.getElementById('profilePreview');
        const file = input.files[0];
        const reader = new FileReader();

        reader.onloadend = function() {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ asset('default-profile.png') }}";
        }
    }
</script>

@endsection