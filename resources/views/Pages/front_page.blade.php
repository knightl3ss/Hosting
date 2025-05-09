<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Front Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
    <style>
        /* Overlay logo background styling */
        .logo-bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 0;
            opacity: 50; /* Adjust for subtlety */
            pointer-events: none; /* Let clicks pass through */
            user-select: none;
        }
        .main-content {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    <!-- Logo Overlay Background -->
    <div class="logo-bg-overlay">
        <img src="{{ asset('images/Municipal Logo of Magallanes.png') }}" alt="Background Logo" style="max-width:60vw; max-height:60vh;">
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2 w-100" style="position:fixed;top:0;left:0;z-index:1050;">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#" style="font-size:1.5rem;">
          <img src="{{ asset('images/logo.png') }}" alt="Logo" width="40" height="40" class="d-inline-block align-text-top me-2" style="border-radius:50%;background:#fff;padding:2px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
          <span>LGU Capstone</span>
        </a>
        <div class="ms-auto">
          @if(auth()->check())
            <li class="nav-item dropdown list-unstyled d-inline-block">
                <a class="nav-link dropdown-toggle mt-1" href="#" role="button" data-bs-toggle="dropdown">
                    <img src="{{ asset(auth()->user()->profile_picture ?? 'default-profile.png') }}"
                         class="rounded-circle me-2"
                         style="width: 50px; height: 50px; object-fit: cover;">
                    <span class="d-none d-md-inline">{{ auth()->user()->first_name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end bg-dark">
                    <li class="text-center mb-2">
                        <img src="{{ asset(auth()->user()->profile_picture ?? 'default-profile.png') }}"
                             class="rounded-circle mb-2 shadow-sm"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="text-light">{{ auth()->user()->first_name }} {{ auth()->user()->middle_name ?? '' }} {{ auth()->user()->last_name }}</div>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-light dropdown-align" href="/profile">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-light dropdown-align" href="/settings">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-light dropdown-align">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                </ul>
            </li>
          @else
            <a href="#" id="showLoginModalBtn" class="btn-navbar-login d-inline-flex align-items-center" style="font-size: 1.15rem;">
              <i class="fas fa-sign-in-alt me-2"></i>Login
            </a>
          @endif
        </div>
      </div>
    </nav>

    <!-- Add spacing below navbar for fixed positioning -->
    <div style="height:68px;"></div>

    <!-- Login Modal -->
    @include('components.login_modal')

    <script>
        // =============================
        // Constants & Utility Functions
        // =============================
        const MAX_ATTEMPTS = 5;
        const LOCKOUT_SECONDS = 30;
        const ATTEMPT_KEY = 'login_attempts';
        const LOCKOUT_KEY = 'login_lockout_time';
        const RESTRICTED_DOMAINS = ['@test.com', '@blocked.com'];
        const RESTRICTED_USERNAMES = ['admin', 'root', 'superuser'];

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

        // =============================
        // Main Logic
        // =============================
        document.addEventListener('DOMContentLoaded', function() {
            // --- Element References ---
            const form = document.querySelector('form[action="{{ route('login') }}"]');
            if (!form) return;
            const submitBtn = form.querySelector('[type="submit"]') || form.querySelector('button');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordMsg = document.getElementById('password-strength-message');
            const attemptInfo = document.getElementById('attempt-info');

            // --- Attempt Info & Lockout ---
            function updateAttemptInfo() {
                // Only show attempt info if there is an error alert (failed login or validation error)
                const errorAlert = document.querySelector('.alert-danger');
                if (!errorAlert) {
                    attemptInfo.innerHTML = '';
                    attemptInfo.style.display = 'none';
                    return;
                }
                let attempts = getAttempts();
                let msg = `Attempt ${Math.min(attempts+1, MAX_ATTEMPTS)} of ${MAX_ATTEMPTS}`;
                if (isLockedOut()) {
                    let secs = Math.ceil(remainingLockout() / 1000);
                    msg += ` | Locked out. Try again in <span id='lockout-timer'>${secs}</span> second(s).`;
                }
                attemptInfo.innerHTML = msg;
                attemptInfo.style.display = '';
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
                    attemptInfo.innerHTML = `Attempt ${MAX_ATTEMPTS} of ${MAX_ATTEMPTS} | Locked out. Try again in <span id='lockout-timer'>${secs}</span> second(s).`;
                    setTimeout(tick, 1000);
                }
                tick();
            }

            // --- Password Strength ---
            function checkPasswordStrength(pwd) {
                if (pwd.length < 8) {
                    return 'Password must be at least 8 characters.';
                } else if (!/[A-Z]/.test(pwd)) {
                    return 'Include at least one uppercase letter.';
                } else if (!/[a-z]/.test(pwd)) {
                    return 'Include at least one lowercase letter.';
                } else if (!/[0-9]/.test(pwd)) {
                    return 'Include at least one number.';
                } else if (!/[^A-Za-z0-9]/.test(pwd)) {
                    return 'Include at least one special character.';
                }
                return '';
            }

            // --- Restriction Checks ---
            function isRestrictedEmail(email) {
                return RESTRICTED_DOMAINS.some(domain => email.endsWith(domain));
            }
            function isRestrictedUsername(email) {
                const username = email.split('@')[0];
                return RESTRICTED_USERNAMES.includes(username.toLowerCase());
            }

            // =============================
            // Event Listeners
            // =============================

            // 1. Check lockout on page load
            if (isLockedOut()) {
                form.querySelectorAll('input, button').forEach(e => e.disabled = true);
                startLockoutCountdown();
            } else {
                updateAttemptInfo();
            }

            // 2. Password strength check
            passwordInput.addEventListener('input', function() {
                passwordMsg.textContent = checkPasswordStrength(passwordInput.value);
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
                if (isRestrictedEmail(email) || isRestrictedUsername(email)) blocked = true;
                if (blocked) {
                    emailInput.setCustomValidity('This email or username is not allowed.');
                    emailInput.reportValidity();
                } else {
                    emailInput.setCustomValidity('');
                }
            });

            // 5. Form submit: handle lockout only (do NOT increment attempts here)
            form.addEventListener('submit', function(e) {
                if (isLockedOut()) {
                    e.preventDefault();
                    return false;
                }
                // Attempts are now only incremented after a failed login (see below)
                let attempts = getAttempts();
                if (attempts >= MAX_ATTEMPTS - 1) {
                    setLockout();
                    form.querySelectorAll('input, button').forEach(e => e.disabled = true);
                    startLockoutCountdown();
                    setAttempts(MAX_ATTEMPTS);
                    e.preventDefault();
                }
            });

            // 5b. On page load, increment attempts if there was a login error (failed login)
            const loginError = document.querySelector('.alert-danger');
            if (loginError) {
                let attempts = getAttempts();
                if (attempts < MAX_ATTEMPTS) {
                    setAttempts(attempts + 1);
                }
                updateAttemptInfo();
            }

            // 6. Password Toggle Visibility (if present)
            const togglePasswordButton = document.getElementById('togglePassword');
            const passwordToggleIcon = document.getElementById('passwordToggleIcon');
            if (togglePasswordButton && passwordInput && passwordToggleIcon) {
                togglePasswordButton.addEventListener('click', function() {
                    const isVisible = passwordInput.type === 'text';
                    passwordInput.type = isVisible ? 'password' : 'text';
                    passwordToggleIcon.classList.toggle('fa-eye', isVisible);
                    passwordToggleIcon.classList.toggle('fa-eye-slash', !isVisible);
                });
            }
        });
    </script>

    <!-- Centered Logo and Main Content -->
    <div class="container d-flex flex-column align-items-center justify-content-center main-content" style="min-height:60vh;">
        <!--<img src="{{ asset('images/Municipal Logo of Magallanes.png') }}" alt="Logo" width="400" height="400" class="my-4" style="border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.08);">-->
        @if(auth()->check())
        <!-- Action Links (only show if logged in) -->
        <div class="d-flex gap-4 justify-content-center my-4">
            <a href="{{ url('/dashboard') }}" class="btn-dashboard d-flex align-items-center">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="{{ url('/appointments') }}" class="btn-appointment d-flex align-items-center">
                <i class="fas fa-calendar-check me-2"></i> Appointment
            </a>
        </div>
        @endif
        <!-- Add more content here as needed -->
    </div>

    <!-- Mini Footer -->
    <footer class="bg-dark text-light py-2 mt-auto w-100 shadow-sm mini-footer" style="position:fixed;bottom:0;left:0;z-index:1050;">
      <div class="container text-center small">
        &copy; {{ date('Y') }} LGU Capstone. All rights reserved.
      </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Only show modal when clicking login link
        document.addEventListener('DOMContentLoaded', function() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            document.getElementById('showLoginModalBtn').addEventListener('click', function(e) {
                e.preventDefault();
                loginModal.show();
            });
            // Auto-show modal if there are login errors (after failed attempt)
            @if($errors->any() || session('error'))
                loginModal.show();
            @endif
        });
    </script>
</body>
</html>