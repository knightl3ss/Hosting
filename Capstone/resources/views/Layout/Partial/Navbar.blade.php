<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm animate-navbar">
    <div class="container">
        <!-- Sidebar Toggle Button -->
        <button class="btn btn-outline-light me-3" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand Text -->
        <a class="navbar-brand me-auto fw-bold" href="#">LGU HR Management</a>

        <!-- Toggle Button for Mobile -->
      

        <!-- Navbar Collapse -->
        
                <!-- Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle mt-1" href="#" role="button" data-bs-toggle="dropdown">
                        @if(auth()->check())
                            <img src="{{ asset(auth()->user()->profile_picture ?? 'default-profile.png') }}" 
                                 class="rounded-circle me-2" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            <span class="d-none d-md-inline">{{ auth()->user()->first_name }}</span>
                        @else
                            <i class="fas fa-user-circle fa-lg"></i>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end bg-dark animate-dropdown">
                        <li class="text-center mb-2">
                            @if(auth()->check())
                                <img src="{{ asset(auth()->user()->profile_picture ?? 'default-profile.png') }}" 
                                     class="rounded-circle mb-2 shadow-sm" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                                <div class="text-light">{{ auth()->user()->first_name }} {{ auth()->user()->middle_name ?? '' }} {{ auth()->user()->last_name }}</div>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            @endif
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
            </ul>
        </div>
    </div>
</nav>

<!-- Navbar Animation Script (optional, lightweight) -->
<script>
    // Animate dropdowns with fade/slide
    document.querySelectorAll('.animate-dropdown').forEach(function(menu) {
        menu.addEventListener('show.bs.dropdown', function () {
            menu.classList.add('show-anim');
        });
        menu.addEventListener('hide.bs.dropdown', function () {
            menu.classList.remove('show-anim');
        });
    });
</script>
