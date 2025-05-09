<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <h4 class="border-bottom border-black pb-2">Personal Information</h4>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="{{ $first_name }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" value="{{ $middle_name }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="{{ $last_name }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Extension Name</label>
                            <input type="text" class="form-control" name="extension_name" value="{{ $extension_name }}">
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-3 mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" class="form-control" name="employee_id" value="{{ $employee_id }}" min="18" max="100" read>
                        </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled {{ empty($gender) ? 'selected' : '' }}>Select Gender</option>
                            <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ $gender === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" value="{{ $age }}" min="18" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Birthday</label>
                            <input type="date" class="form-control" name="birthday" value="{{ $birthday }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <h4 class="border-bottom border-black pb-2">Address information</h4>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Street</label>
                            <input type="text" class="form-control" id="street_address" name="address_street" value="{{ $address_street }}" required>
                            @error('address_street')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" id="city_address" name="address_city" value="{{ $address_city }}" required>
                            @error('address_city')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" id="state_address" name="address_state" value="{{ $address_state }}" required>
                            @error('address_state')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="postal_code" name="address_postal_code" value="{{ $address_postal_code }}" required>
                            @error('address_postal_code')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>  
                    </div>
                    <div class="row">
                        <h4 class="border-bottom border-black pb-2">Account Information</h4>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $email) }}" required>
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone_number" value="{{ old('phone_number', $phone_number) }}" pattern="[0-9]{10,15}" title="Please enter a valid phone number (10-15 digits)" required>
                            @error('phone_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($role) }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Status</label>
                            <input type="text" class="form-control" value="{{ ucfirst($status) }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Profile Picture</label>
                            <div class="d-flex flex-column align-items-center mb-2">
                                <img id="profilePreview" src="{{ asset('storage/' . str_replace('storage/', '', $profile_picture ?? 'default-profile.png')) }}" 
                                     class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            <input type="file" class="form-control" name="profile_picture" accept="image/*" onchange="previewProfilePicture(this)">
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
