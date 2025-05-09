<!-- Mobile Navigation Toggle -->
<button class="nav-toggle" id="sidebar-mobile-toggle" aria-label="Open sidebar">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo small">
        <img src="{{ asset('images/Municipal Logo of Magallanes.png') }}" alt="Logo">
    </div>
    <ul class="nav-list">
        <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
            <a href="{{ url('/') }}" class="small"><i class="fas fa-home"></i><span class="fs-6">Home</span></a>
        </li>
        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="small"><i class="fas fa-tachometer-alt"></i><span class="fs-6">Dashboard</span></a>
        </li>
        <li class="nav-item {{ request()->is('appointments*') ? 'active' : '' }}">
            <a href="{{ route('appointments') }}" class="small"><i class="fas fa-calendar-check"></i><span class="fs-6">Appointment</span></a>
        </li>
        <!-- Employee link removed as functionality has been integrated into Appointments -->
       
        <li class="nav-item {{ request()->is('service_records*') ? 'active' : '' }}">
            <a href="{{ route('service_records') }}" class="small has-arrow"><i class="fas fa-file-alt"></i><span class="fs-6">Service Record</span></a>
        </li>
        <li class="nav-item {{ request()->is('account_list*', 'edit_account*', 'view_account*') ? 'active' : '' }}">
            <a href="{{ route('account_list') }}" class="small has-arrow"><i class="fas fa-user-cog"></i><span class="fs-6">Account</span></a>
        </li>
    </ul>
</div>

<!-- Sidebar Animation Script (optimized, lightweight) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggle = document.getElementById('sidebar-mobile-toggle');
        const spinner = document.getElementById('page-loading-spinner');
        // Sidebar open/close logic
        function openSidebar() {
            sidebar.classList.add('active');
            overlay.style.display = 'block';
            setTimeout(() => overlay.classList.add('active'), 10);
        }
        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            setTimeout(() => overlay.style.display = 'none', 300);
        }
        toggle.addEventListener('click', openSidebar);
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });
        // Show spinner on navigation
        document.querySelectorAll('.nav-list a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Only show spinner for normal left-click navigation
                if (!e.ctrlKey && !e.shiftKey && !e.metaKey && e.button === 0 && this.target !== '_blank') {
                    spinner.style.display = 'flex';
                }
            });
        });
        // Hide spinner if user navigates back/forward
        window.addEventListener('pageshow', function() {
            spinner.style.display = 'none';
        });
    });
</script>

{{-- <li class="nav-item {{ request()->is('Plantilla*') ? 'active' : '' }}">
<a href="{{ route('Plantilla') }}" class="small"><i class="fas fa-users"></i><span class="fs-6">Plantilla</span></a>
--}}
