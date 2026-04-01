<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('admin.partials.head')
        @stack('head')
    </head>
    <body class="sb-nav-fixed">
        @include('admin.partials.topnav')
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                @include('admin.partials.sidebar')
            </div>
            <div id="layoutSidenav_content">
                <main>
                    @yield('content')
                </main>
                @include('admin.partials.footer')
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        @vite('resources/views/admin/assets/js/scripts.js')
        @stack('scripts')
    </body>
</html>
