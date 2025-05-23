<div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl custom-modal-dialog">
        <div class="modal-content custom-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationModalLabel">Register New Admin Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('register.modal') }}" method="POST" id="adminRegistrationForm" enctype="multipart/form-data">
                @csrf
                <div class="border p-3">
                    <h6 class="section-header ">Personal Information</h6>
                    <div class="row ">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" pattern="[A-Za-z\s]+" title="Please enter alphabets only" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" id="middle_name" pattern="[A-Za-z\s]*" title="Please enter alphabets only" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" pattern="[A-Za-z\s]+" title="Please enter alphabets only" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Extension Name (e.g., Jr., Sr.)</label>
                            <input type="text" class="form-control" id="extension_name" name="extension_name" pattern="[A-Za-z\s\.]+" title="Please enter valid extension (e.g., Jr., Sr.)" maxlength="10">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" id="age" min="18" max="100">
                            <div class="invalid-feedback"></div>
                            <small id="ageFeedback" class="form-text"></small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Birthday</label>
                            <input type="date" class="form-control" name="birthday" id="birthday" max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                            <div class="invalid-feedback"></div>
                            <small id="birthdayFeedback" class="form-text"></small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" name="status" id="status" value="Active" readonly>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Gender Identity</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <!-- Address and Account Information -->
                <div class="border p-3 m">
                    <div class="row">
                        <h6 class="section-header">Address Information</h6>
                        <div class="col-md-3 mb-2">
                            <label for="address_street" class="form-label">Street Address</label>
                            <input type="text" class="form-control address-input" id="address_street" name="address_street" maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="address_city" class="form-label">Municipality</label>
                            <input type="text" class="form-control address-input" id="address_city" name="address_city" pattern="[A-Za-z\s]+" title="Please enter alphabets only" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="address_state" class="form-label">Province</label>
                            <input type="text" class="form-control address-input" id="address_state" name="address_state" pattern="[A-Za-z\s]+" title="Please enter alphabets only" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="address_postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control address-input" id="address_postal_code" name="address_postal_code" pattern="[0-9]+" minlength="4" maxlength="10" title="Please enter numbers only">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <!-- Account Information -->
                <div class="border p-3">
                    <div class="row">
                        <h6 class="section-header">Account Information</h6>
                        <div class="col-md-3 mb-2">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" pattern="[A-Za-z0-9\-]+" title="Alphanumeric characters and hyphens only" maxlength="20">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" pattern="[A-Za-z0-9_]+" title="Alphanumeric and underscores only" maxlength="255">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" title="Please enter a valid email address" maxlength="100">
                            <div class="invalid-feedback"></div>
                            <small id="emailFeedback" class="form-text"></small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="09XX XXX XXXX or +63 XXX-XXX-XXXX" maxlength="15" pattern="(\+63|0)9\d{2}[- ]?\d{3}[- ]?\d{4}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" name="role" id="role" value="Admin" readonly>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <!-- Security Information -->
                <div class="border p-3">
                    <div class="row">
                        <h6 class="section-header">Security</h6>
                        <div class="col-md-3 mb-2">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-field-container">
                                <input type="password" class="form-control" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}" title="Must contain at least one number, one uppercase letter, one lowercase letter, one special character, and at least 8 characters">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="adminRegistrationForm" class="btn btn-primary">Register Admin</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl custom-modal-dialog">
        <div class="modal-content custom-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAccountModalLabel">Edit Admin Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('update_account', $user->id ?? '') }}" method="POST" id="editAccountForm">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="border p-3">
                    <h6 class="section-header">Personal Information</h6>
                    <div class="row ">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" pattern="[A-Za-z\s]+" title="Please enter alphabets only" maxlength="50" value="{{ old('first_name', $user->first_name ?? '') }}">
                            @error('first_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" id="edit_middle_name" value="{{ $user->middle_name ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" value="{{ $user->last_name ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Extension Name</label>
                            <input type="text" class="form-control" id="edit_extension_name" name="extension_name" value="{{ $user->extension_name ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" id="edit_age" min="18" max="100" value="{{ $user->age ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Birthday</label>
                            <input type="date" class="form-control" name="birthday" id="edit_birthday" value="{{ $user->birthday ?? '' }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="Active" {{ (isset($user) && $user->status == 'Active') ? 'selected' : '' }}>Active</option>
                                <option value="Pending" {{ (isset($user) && $user->status == 'Pending') ? 'selected' : '' }}>Pending</option>
                                <option value="Blocked" {{ (isset($user) && $user->status == 'Blocked') ? 'selected' : '' }}>Blocked</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Gender Identity</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="male" {{ (isset($user) && $user->gender == 'male') ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ (isset($user) && $user->gender == 'female') ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ (isset($user) && $user->gender == 'other') ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="border p-3 m">
                    <div class="row">
                        <h6 class="section-header">Address Information</h6>
                        <div class="col-md-3 mb-2">
                            <label for="edit_address_street" class="form-label">Street Address</label>
                            <input type="text" class="form-control address-input" id="edit_address_street" name="address_street" value="{{ $user->address_street ?? '' }}" maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="edit_address_city" class="form-label">Municipality</label>
                            <input type="text" class="form-control address-input" id="edit_address_city" name="address_city" value="{{ $user->address_city ?? '' }}" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="edit_address_state" class="form-label">Province</label>
                            <input type="text" class="form-control address-input" id="edit_address_state" name="address_state" value="{{ $user->address_state ?? '' }}" maxlength="50">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="edit_address_postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control address-input" id="edit_address_postal_code" name="address_postal_code" value="{{ $user->address_postal_code ?? '' }}" maxlength="10">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="border p-3">
                    <div class="row">
                        <h6 class="section-header">Account Information</h6>
                        <div class="col-md-3 mb-2">
                            <label for="edit_employee_id" class="form-label">Employee ID</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="edit_employee_id" name="employee_id" value="{{ $user->employee_id ?? '' }}" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.readOnly=false; this.disabled=true;">Edit</button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="edit_username" class="form-label">Username</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="edit_username" name="username" value="{{ $user->username ?? '' }}" maxlength="255" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.readOnly=false; this.disabled=true;">Edit</button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="edit_email" class="form-label">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="edit_email" name="email" value="{{ $user->email ?? '' }}" maxlength="100" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.readOnly=false; this.disabled=true;">Edit</button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="edit_phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="edit_phone_number" name="phone_number" value="{{ $user->phone_number ?? '' }}" maxlength="15">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="hidden" name="role" value="Admin">
                        </div>
                    </div>
                </div>
                <div class="border p-3">
                    <div class="row">
                        <h6 class="section-header">Security</h6>
                        <div class="col-md-3 mb-2">
                            <label for="edit_password" class="form-label">New Password (optional)</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="8">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Leave blank if you don't want to change the password</small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="edit_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation" minlength="8">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editAccountForm" class="btn btn-primary">Update Account</button>
            </div>
        </div>
    </div>
</div>

<!-- Account View Modal (Distinct Design) -->
<div class="modal fade" id="viewAccountModal" tabindex="-1" aria-labelledby="viewAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 2rem; box-shadow: 0 0 30px rgba(0,123,255,.2); border: 2px solid #0d6efd;">
            <div class="modal-header" style="background: linear-gradient(90deg, #0d6efd 60%, #6f42c1 100%); color: #fff; border-top-left-radius: 2rem; border-top-right-radius: 2rem;">
                <h5 class="modal-title" id="viewAccountModalLabel"><i class="fas fa-id-card me-2"></i>Admin Account Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light" style="border-bottom-left-radius: 2rem; border-bottom-right-radius: 2rem;">
                <div class="d-flex flex-column align-items-center mb-4">
                    <div class="bg-white border border-3 border-primary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width:110px;height:110px; box-shadow: 0 0 15px #0d6efd33;">
                        <i class="fas fa-user fa-5x text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="font-size:1.3rem;">{{ $user->first_name }} {{ $user->last_name }}</h6>
                    <span class="badge bg-gradient fw-bold fs-6" style="background: linear-gradient(90deg, #6f42c1 40%, #0d6efd 100%); color:black;" >{{ ucfirst($user->role) }}</span>
                </div>
                <hr class="my-4">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="p-3 bg-white rounded-4 shadow-sm border border-2 border-primary-subtle">
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <span class="text-secondary fw-semibold">Employee ID:</span><br>
                                    <span>{{ $user->employee_id }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-secondary fw-semibold">Full Name:</span><br>
                                    <span>{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }} {{ $user->extension_name }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <span class="text-secondary fw-semibold">Username:</span><br>
                                    <span>{{ $user->username }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-secondary fw-semibold">Email:</span><br>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <span class="text-secondary fw-semibold">Phone Number:</span><br>
                                    <span>{{ $user->phone_number }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-secondary fw-semibold">Role:</span><br>
                                    <span class="badge bg-info text-dark"><i class="fas fa-user-tag me-1"></i>{{ ucfirst($user->role) }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <span class="text-secondary fw-semibold">Status:</span><br>
                                    <span class="badge d-inline-flex align-items-center
                                        @if($user->status == 'active') bg-success 
                                        @elseif($user->status == 'pending') bg-warning text-dark
                                        @else bg-danger 
                                        @endif">
                                        @if($user->status == 'Active')
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        @elseif($user->status == 'Pending')
                                            <i class="fas fa-hourglass-half me-1"></i>Pending
                                        @else
                                            <i class="fas fa-times-circle me-1"></i>Inactive
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-bottom-left-radius: 2rem; border-bottom-right-radius: 2rem;">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Improved Modal CSS -->
<link rel="stylesheet" href="/css/custom-modal.css">

<!-- Modal Responsive Design Fix -->
<style>
    /* Ensure modal fits viewport and prevents body scrolling */
    .custom-modal-dialog {
        max-width: 100vw;
        width: 100%;
        margin: 1.75rem auto;
        display: flex;
        align-items: center;
        min-height: calc(100vh - 3.5rem);
    }
    .custom-modal-content {
        max-height: 90vh;
        overflow-y: auto;
        border-radius: 18px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        padding: 0.5rem 1.5rem 1rem 1.5rem;
        background: #fff;
    }
    @media (max-width: 768px) {
        .custom-modal-dialog {
            max-width: 99vw;
            margin: 0.5rem auto;
            min-height: calc(100vh - 1rem);
        }
        .custom-modal-content {
            padding: 0.5rem 0.5rem 1rem 0.5rem;
        }
    }
    /* Hide modal backdrop scroll */
    body.modal-open {
        overflow: hidden !important;
    }
</style>
