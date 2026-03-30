@php
    $sidenavClass = $sidenavClass ?? 'sb-sidenav-dark';
@endphp
<nav class="sb-sidenav accordion {{ $sidenavClass }}" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>
            <a class="nav-link {{ $activeSidebar === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Dashboard
            </a>
            <div class="sb-sidenav-menu-heading">Interface</div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                Layouts
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse {{ in_array($activeSidebar, ['layout-static', 'layout-sidenav-light'], true) ? 'show' : '' }}" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link {{ $activeSidebar === 'layout-static' ? 'active' : '' }}" href="{{ route('admin.layouts.static') }}">Static Navigation</a>
                    <a class="nav-link {{ $activeSidebar === 'layout-sidenav-light' ? 'active' : '' }}" href="{{ route('admin.layouts.sidenav-light') }}">Light Sidenav</a>
                </nav>
            </div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                Pages
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse {{ str_starts_with($activeSidebar, 'error') || in_array($activeSidebar, ['register', 'password'], true) ? 'show' : '' }}" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                        Authentication
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ in_array($activeSidebar, ['register', 'password'], true) ? 'show' : '' }}" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('admin.entry') }}">Login</a>
                            <a class="nav-link {{ $activeSidebar === 'register' ? 'active' : '' }}" href="{{ route('admin.register') }}">Register</a>
                            <a class="nav-link {{ $activeSidebar === 'password' ? 'active' : '' }}" href="{{ route('admin.password.request') }}">Forgot Password</a>
                        </nav>
                    </div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                        Error
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ str_starts_with($activeSidebar, 'error') ? 'show' : '' }}" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ $activeSidebar === 'error-401' ? 'active' : '' }}" href="{{ route('admin.errors.401') }}">401 Page</a>
                            <a class="nav-link {{ $activeSidebar === 'error-404' ? 'active' : '' }}" href="{{ route('admin.errors.404-demo') }}">404 Page</a>
                            <a class="nav-link {{ $activeSidebar === 'error-500' ? 'active' : '' }}" href="{{ route('admin.errors.500-demo') }}">500 Page</a>
                        </nav>
                    </div>
                </nav>
            </div>
            <div class="sb-sidenav-menu-heading">Addons</div>
            <a class="nav-link {{ $activeSidebar === 'charts' ? 'active' : '' }}" href="{{ route('admin.charts') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                Charts
            </a>
            <a class="nav-link {{ $activeSidebar === 'tables' ? 'active' : '' }}" href="{{ route('admin.tables') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                Tables
            </a>
            <a class="nav-link {{ $activeSidebar === 'forms' ? 'active' : '' }}" href="{{ route('admin.forms') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file-lines"></i></div>
                Forms
            </a>
            <a class="nav-link {{ $activeSidebar === 'blank' ? 'active' : '' }}" href="{{ route('admin.blank') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file"></i></div>
                Blank Page
            </a>
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Logged in as:</div>
        {{ $adminUser?->name ?? 'Start Bootstrap' }}
    </div>
</nav>
