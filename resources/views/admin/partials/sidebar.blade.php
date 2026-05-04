@php
    $sidenavClass = $sidenavClass ?? 'sb-sidenav-dark';
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
            <div class="sb-sidenav-menu-heading">Addons</div>
            <a class="nav-link {{ $activeSidebar === 'static-pages' ? 'active' : '' }}" href="{{ route('admin.static-pages.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                {{ __('Static pages') }}
            </a>
            <a class="nav-link {{ $activeSidebar === 'tables' ? 'active' : '' }}" href="{{ route('admin.tables.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                {{ __('Tables') }}
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
            <a class="nav-link" href="/log-viewer">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-bug-slash"></i></div>
                Log
            </a>
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Logged in as:</div>
        {{ $adminUser?->name ?? 'BS Admin' }}
    </div>
</nav>
