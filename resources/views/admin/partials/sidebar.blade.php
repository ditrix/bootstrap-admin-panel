@php
    $sidenavClass = $sidenavClass ?? 'sb-sidenav-dark';
    $layoutsMenuOpen = in_array($activeSidebar, ['layout-static', 'layout-sidenav-light'], true);
    $pagesMenuOpen = str_starts_with($activeSidebar, 'error') || in_array($activeSidebar, ['register', 'password'], true);
    $pagesAuthOpen = in_array($activeSidebar, ['register', 'password'], true);
    $pagesErrorOpen = str_starts_with($activeSidebar, 'error');
    $settingsMenuOpen = in_array($activeSidebar, ['settings-redirects', 'settings-main-menu', 'settings-administrators'], true);
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
            <a class="nav-link{{ $layoutsMenuOpen ? '' : ' collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="{{ $layoutsMenuOpen ? 'true' : 'false' }}" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                Layouts
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse {{ $layoutsMenuOpen ? 'show' : '' }}" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link {{ $activeSidebar === 'layout-static' ? 'active' : '' }}" href="{{ route('admin.layouts.static') }}">Static Navigation</a>
                    <a class="nav-link {{ $activeSidebar === 'layout-sidenav-light' ? 'active' : '' }}" href="{{ route('admin.layouts.sidenav-light') }}">Light Sidenav</a>
                </nav>
            </div>
            <a class="nav-link{{ $pagesMenuOpen ? '' : ' collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="{{ $pagesMenuOpen ? 'true' : 'false' }}" aria-controls="collapsePages">
                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                Pages
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse {{ $pagesMenuOpen ? 'show' : '' }}" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                    <a class="nav-link{{ $pagesAuthOpen ? '' : ' collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="{{ $pagesAuthOpen ? 'true' : 'false' }}" aria-controls="pagesCollapseAuth">
                        Authentication
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $pagesAuthOpen ? 'show' : '' }}" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{ route('admin.entry') }}">Login</a>
                            <a class="nav-link {{ $activeSidebar === 'register' ? 'active' : '' }}" href="{{ route('admin.register') }}">Register</a>
                            <a class="nav-link {{ $activeSidebar === 'password' ? 'active' : '' }}" href="{{ route('admin.password.request') }}">Forgot Password</a>
                        </nav>
                    </div>
                    <a class="nav-link{{ $pagesErrorOpen ? '' : ' collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="{{ $pagesErrorOpen ? 'true' : 'false' }}" aria-controls="pagesCollapseError">
                        Error
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $pagesErrorOpen ? 'show' : '' }}" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ $activeSidebar === 'error-401' ? 'active' : '' }}" href="{{ route('admin.errors.401') }}">401 Page</a>
                            <a class="nav-link {{ $activeSidebar === 'error-404' ? 'active' : '' }}" href="{{ route('admin.errors.404-demo') }}">404 Page</a>
                            <a class="nav-link {{ $activeSidebar === 'error-500' ? 'active' : '' }}" href="{{ route('admin.errors.500-demo') }}">500 Page</a>
                        </nav>
                    </div>
                </nav>
            </div>
            <div class="sb-sidenav-menu-heading">Addons</div>
            <a class="nav-link {{ $activeSidebar === 'static-pages' ? 'active' : '' }}" href="{{ route('admin.static-pages.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                {{ __('Static pages') }}
            </a>
            <a class="nav-link {{ $activeSidebar === 'category-tree' ? 'active' : '' }}" href="{{ route('admin.category-tree.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-sitemap"></i></div>
                {{ __('Category Tree') }}
            </a>
            <a class="nav-link{{ $settingsMenuOpen ? '' : ' collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSettings" aria-expanded="{{ $settingsMenuOpen ? 'true' : 'false' }}" aria-controls="collapseSettings">
                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                {{ __('Settings') }}
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse {{ $settingsMenuOpen ? 'show' : '' }}" id="collapseSettings" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link {{ $activeSidebar === 'settings-redirects' ? 'active' : '' }}" href="{{ route('admin.seo-redirects.index') }}">{{ __('Redirects') }}</a>
                    <a class="nav-link {{ $activeSidebar === 'settings-main-menu' ? 'active' : '' }}" href="{{ route('admin.main-menu.index') }}">{{ __('Main menu') }}</a>
                    <a class="nav-link {{ $activeSidebar === 'settings-administrators' ? 'active' : '' }}" href="{{ route('admin.administrators.index') }}">{{ __('Users') }}</a>
                </nav>
            </div>
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
