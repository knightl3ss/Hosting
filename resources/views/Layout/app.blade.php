<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <!-- Removed cache-control meta tags for asset caching -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/layout-components.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>
<body class="mini-view">
    @include('Layout.Partial.Sidebar')
    @include('Layout.Partial.Navbar')
   
    <!-- Main Content -->
    <div class="main-content-wrapper">
        <div class="main-content">
            <!-- Page Content -->
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Plantilla Content -->
    <div class="container-fluid1">
        @yield('content1')
    </div>

    @include('Layout.Partial.footer')

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/layout_functional.js') }}"></script>
    <script src="{{ asset('js/pages_functional.js') }}"></script>

    <!-- Page Specific Scripts -->
    @stack('scripts')

    <!-- Prevent back button after login -->
    <script>
        // Clear browser history state to prevent going back to login page
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                // If page is loaded from cache (browser back button)
                window.location.reload();
            }
        });
        
        // Add to browser history to replace login page in history
        window.history.pushState({page: 1}, "", "");
        window.onpopstate = function(event) {
            if(event) {
                // If user tries to go back, push another state to prevent it
                window.history.pushState({page: 1}, "", "");
            }
        };
    </script>
</body>
</html>
