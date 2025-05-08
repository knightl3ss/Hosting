<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="main-container">
            <div class="login-container">
                    <h2 class="text-center mb-4" style="color: #ffffff; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">Login</h2>
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach($errors->all() as $error)
                                    <p class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</p>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div id="attempt-info" style="color: #ffc107; font-size: 0.9em;"></div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye-slash" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <div id="password-strength-message" style="color: #ffb347; font-size: 0.9em; margin-top: 5px;"></div>
                        </div>
                        {{-- <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div> --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>
                    {{-- <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register" class="text-primary">
                            <i class="fas fa-user-plus me-1"></i>Register here
                        </a></p>
                    </div> --}}
                </div>
            </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- 1. Limit login attempts ---
        const MAX_ATTEMPTS = 3;
        const LOCKOUT_SECONDS = 30; // changed from minutes to 30 seconds
        const ATTEMPT_KEY = 'login_attempts';
        const LOCKOUT_KEY = 'login_lockout_time';
        
        function getAttempts() {
            return parseInt(localStorage.getItem(ATTEMPT_KEY) || '0', 10);
        }
        function setAttempts(val) {
            localStorage.setItem(ATTEMPT_KEY, val);
        }
        function setLockout() {
            localStorage.setItem(LOCKOUT_KEY, Date.now());
        }
        function getLockout() {
            return parseInt(localStorage.getItem(LOCKOUT_KEY) || '0', 10);
        }
        function isLockedOut() {
            const lockoutTime = getLockout();
            if (!lockoutTime) return false;
            const now = Date.now();
            return (now - lockoutTime) < LOCKOUT_SECONDS * 1000;
        }
        function remainingLockout() {
            const lockoutTime = getLockout();
            if (!lockoutTime) return 0;
            const now = Date.now();
            return Math.max(0, LOCKOUT_SECONDS * 1000 - (now - lockoutTime));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('login') }}"]');
            const submitBtn = form.querySelector('[type="submit"]') || form.querySelector('button');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordMsg = document.getElementById('password-strength-message');
            const attemptInfo = document.getElementById('attempt-info');
            const restrictedDomains = ['@test.com', '@blocked.com']; // Add more as needed
            const restrictedUsernames = ['admin', 'root', 'superuser'];

            function updateAttemptInfo() {
                let attempts = getAttempts();
                let msg = `Attempt ${Math.min(attempts+1, MAX_ATTEMPTS)} of ${MAX_ATTEMPTS}`;
                if (isLockedOut()) {
                    let secs = Math.ceil(remainingLockout() / 1000);
                    msg += ` | Locked out. Try again in <span id='lockout-timer'>${secs}</span> second(s).`;
                }
                attemptInfo.innerHTML = msg;
            }

            function startLockoutCountdown() {
                function tick() {
                    if (!isLockedOut()) {
                        attemptInfo.innerHTML = `Attempt 1 of ${MAX_ATTEMPTS}`;
                        form.querySelectorAll('input, button').forEach(e => e.disabled = false);
                        setAttempts(0);
                        localStorage.removeItem(LOCKOUT_KEY);
                        return;
                    }
                    let secs = Math.ceil(remainingLockout() / 1000);
                    let attempts = getAttempts();
                    attemptInfo.innerHTML = `Attempt ${MAX_ATTEMPTS} of ${MAX_ATTEMPTS} | Locked out. Try again in <span id='lockout-timer'>${secs}</span> second(s).`;
                    setTimeout(tick, 1000);
                }
                tick();
            }

            // 1. Check lockout
            if (isLockedOut()) {
                form.querySelectorAll('input, button').forEach(e => e.disabled = true);
                startLockoutCountdown();
            } else {
                updateAttemptInfo();
            }

            // 2. Password strength check
            passwordInput.addEventListener('input', function() {
                const pwd = passwordInput.value;
                let msg = '';
                if (pwd.length < 8) {
                    msg = 'Password must be at least 8 characters.';
                } else if (!/[A-Z]/.test(pwd)) {
                    msg = 'Include at least one uppercase letter.';
                } else if (!/[a-z]/.test(pwd)) {
                    msg = 'Include at least one lowercase letter.';
                } else if (!/[0-9]/.test(pwd)) {
                    msg = 'Include at least one number.';
                } else if (!/[^A-Za-z0-9]/.test(pwd)) {
                    msg = 'Include at least one special character.';
                }
                passwordMsg.textContent = msg;
            });

            // 3. Prevent paste into password
            passwordInput.addEventListener('paste', function(e) {
                e.preventDefault();
                passwordMsg.textContent = 'Pasting passwords is not allowed.';
            });

            // 4. Block certain usernames/domains
            emailInput.addEventListener('input', function() {
                const email = emailInput.value.trim();
                let blocked = false;
                restrictedDomains.forEach(domain => {
                    if (email.endsWith(domain)) blocked = true;
                });
                restrictedUsernames.forEach(name => {
                    if (email.split('@')[0].toLowerCase() === name) blocked = true;
                });
                if (blocked) {
                    emailInput.setCustomValidity('This email or username is not allowed.');
                } else {
                    emailInput.setCustomValidity('');
                }
            });

            // On submit, check restrictions
            form.addEventListener('submit', function(e) {
                if (isLockedOut()) {
                    e.preventDefault();
                    return false;
                }
                // Password strength
                if (passwordMsg.textContent) {
                    e.preventDefault();
                    passwordInput.focus();
                    return false;
                }
                // Username/domain
                if (!emailInput.checkValidity()) {
                    e.preventDefault();
                    emailInput.reportValidity();
                    return false;
                }
                // Attempt tracking (simulate fail for demo)
                // In real use, you should increment on actual failed login from backend!
                let attempts = getAttempts();
                attempts++;
                setAttempts(attempts);
                updateAttemptInfo();
                if (attempts >= MAX_ATTEMPTS) {
                    setLockout();
                    form.querySelectorAll('input, button').forEach(e => e.disabled = true);
                    startLockoutCountdown();
                }
            });
        });

        // Password Toggle Visibility
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        const passwordToggleIcon = document.getElementById('passwordToggleIcon');

        togglePasswordButton.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggleIcon.classList.remove('fa-eye-slash');
                passwordToggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordToggleIcon.classList.remove('fa-eye');
                passwordToggleIcon.classList.add('fa-eye-slash');
            }
        });

        // Form Validation
        const loginForm = document.querySelector('form');
        loginForm.addEventListener('submit', function(event) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const errorMessages = [];
            
            // Remove any existing validation error message
            const existingAlert = document.getElementById('validation-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                errorMessages.push('Email must be a valid email address');
            }

            // Password validation
            if (passwordInput.value.length < 8) {
                errorMessages.push('Password must be at least 8 characters long');
            }

            // If there are validation errors, create and display a single error message
            if (errorMessages.length > 0) {
                event.preventDefault();
                
                // Create error alert
                const alertDiv = document.createElement('div');
                alertDiv.id = 'validation-alert';
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.setAttribute('role', 'alert');
                
                // Add error icon and message
                const errorContent = document.createElement('p');
                errorContent.className = 'mb-0';
                errorContent.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${errorMessages.join(' and ')}`;
                
                // Add close button
                const closeButton = document.createElement('button');
                closeButton.className = 'btn-close';
                closeButton.setAttribute('type', 'button');
                closeButton.setAttribute('data-bs-dismiss', 'alert');
                closeButton.setAttribute('aria-label', 'Close');
                
                alertDiv.appendChild(errorContent);
                alertDiv.appendChild(closeButton);
                
                // Insert the alert at the top of the form
                loginForm.insertBefore(alertDiv, loginForm.firstChild);
            }
        });
    </script>
</body>
</html>