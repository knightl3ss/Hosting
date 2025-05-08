<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <h2 class="modal-title w-100 text-center" id="loginModalLabel">Login</h2>
      </div>
      <div class="modal-body">
        <div class="main-container">
            <div class="login-container">
                <div class="login-avatar">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Avatar" />
                </div>
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
    </div>
  </div>
</div>
