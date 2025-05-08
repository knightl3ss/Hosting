<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7; /* Light gray background */
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            padding: 20px;
            margin: auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1, h2 {
            text-align: center;
            color: #0056b3;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn {
            background-color: #0056b3;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #004494;
        }
        .registration-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 1rem;
        }
        .registration-container {
            width: 80%;
            max-width: 100%;
            height: 100vh;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background-color: #f4f6f9;
            border-radius: 8px;
            overflow: hidden;
            padding: 1rem;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            padding: 0.75rem;
        }
        .card-body {
            padding: 1rem;
            overflow-y: auto;
            max-height: calc(100vh - 150px); /* Adjust based on header and footer height */
        }
        .profile-upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1rem;
        }
        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 0.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .section-header {
            color: #6c757d;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.25rem;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            height: 40px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0,123,255,0.25);
        }
        .form-select {
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
        }
        .address-input {
            width: 100%;
            height: 40px;
        }
        .border {
            border: 1px solid #dee2e6;
        }
        @media (max-width: 768px) {
            .registration-container {
                max-width: 100%;
                max-height: 100vh;
            }
            .card-body {
                max-height: calc(100vh - 150px); /* Adjust based on header and footer height */
            }
        }
    </style>
</head>
<body>
    <div class="registration-wrapper">
        <div class="registration-container">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Register New Admin Account</h5>
                <a href="login_page" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Return to Login
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('register') }}" method="POST" id="adminRegistrationForm" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Personal Information -->
                    <div class="border p-3">
                        <h6 class="section-header ">Personal Information</h6>
                    
                    <div class="row ">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                            @error('first_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name">
                            @error('middle_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                            @error('last_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Extension Name (e.g., Jr., Sr.)</label>
                            <input type="text" class="form-control" id="extension_name" name="extension_name">
                            @error('extension_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" min="18" max="100" required>
                            @error('age')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Birthday</label>
                            <input type="date" class="form-control" name="birthday" required>
                            @error('birthday')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="blocked">Blocked</option>
                            </select>
                            @error('status')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Gender Identity</label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    </div>
                    
        
                            <!-- Profile Picture Upload -->
                            <div class="profile-upload-container mb-3">
                                <div class="text-center">
                                    <img src="{{ asset('default-profile.png') }}" alt="Profile Preview" 
                                         class="img-thumbnail rounded-circle mb-3" 
                                         id="profilePreview" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    
                                    <div class="mb-3">
                                        <label for="profilePicture" class="form-label">Profile Picture</label>
                                        <input type="file" 
                                               class="form-control @error('profile_picture') is-invalid @enderror" 
                                               id="profilePicture" 
                                               name="profile_picture" 
                                               accept="image/jpeg,image/png,image/gif"
                                               onchange="previewProfilePicture(this)">
                                        
                                        @error('profile_picture')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Upload a profile picture (JPG, PNG, GIF). Max size: 5MB.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Address and Account Information -->
                            
                            <div class="border p-3 m">
                            <div class="row">
                            <h6 class="section-header">Address Information</h6>
                            <div class="col-md-3 mb-2">
                                <label for="address_street" class="form-label">Street Address</label>
                                <input type="text" class="form-control address-input" id="address_street" name="address_street" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="address_city" class="form-label">City</label>
                                <input type="text" class="form-control address-input" id="address_city" name="address_city" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="address_state" class="form-label">State</label>
                                <input type="text" class="form-control address-input" id="address_state" name="address_state" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="address_postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control address-input" id="address_postal_code" name="address_postal_code" required>
                            </div>
                        </div>
                        </div>
                        

                            <!-- Account Information -->
                            <div class="border p-3">
                            <div class="row">
                            <h6 class="section-header">Account Information</h6>
                            <div class="col-md-3 mb-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" pattern="[0-9]{10,15}" title="Please enter a valid phone number (10-15 digits)" required>
                                @error('phone_number')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                </select>
                                @error('role')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        </div>

                            <!-- Security Information -->
                            <div class="border p-3">
                            <div class="row">
                            <h6 class="section-header">Security</h6>
                            <div class="col-md-3 mb-2">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                @error('password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                @error('password_confirmation')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Register Admin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
</body>
</html>