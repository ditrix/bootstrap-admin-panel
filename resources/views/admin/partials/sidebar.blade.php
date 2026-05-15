@php
    $sidenavClass = $sidenavClass ?? 'sb-sidenav-dark';
    $settingsMenuOpen = in_array(
        $activeSidebar,
        ['settings-redirects', 'settings-main-menu', 'settings-administrators', 'settings-permissions'],
        true
    );
@endphp
<nav class="sb-sidenav accordion {{ $sidenavClass }}" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>
            @can('dashboard.view')
                <a class="nav-link {{ $activeSidebar === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
            @endcan
            <div class="sb-sidenav-menu-heading">Addons</div>
            @can('static_pages.manage')
                <a class="nav-link {{ $activeSidebar === 'static-pages' ? 'active' : '' }}" href="{{ route('admin.static-pages.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    {{ __('Static pages') }}
                </a>
            @endcan
            @can('banners.manage')
                <a class="nav-link {{ $activeSidebar === 'banners' ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-image"></i></div>
                    {{ __('Banners') }}
                </a>
            @endcan
            @can('employees.manage')
                <a class="nav-link {{ $activeSidebar === 'tables' ? 'active' : '' }}" href="{{ route('admin.tables.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    {{ __('Tables') }}
                </a>
            @endcan
            @can('category_tree.manage')
                <a class="nav-link {{ $activeSidebar === 'category-tree' ? 'active' : '' }}" href="{{ route('admin.category-tree.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-sitemap"></i></div>
                    {{ __('Category Tree') }}
                </a>
            @endcan
            @canany(['seo_redirects.manage', 'main_menu.manage', 'users.manage', 'permissions.view'])
                <a class="nav-link{{ $settingsMenuOpen ? '' : ' collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSettings" aria-expanded="{{ $settingsMenuOpen ? 'true' : 'false' }}" aria-controls="collapseSettings">
                    <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                    {{ __('Settings') }}
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse {{ $settingsMenuOpen ? 'show' : '' }}" id="collapseSettings" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        @can('seo_redirects.manage')
                            <a class="nav-link {{ $activeSidebar === 'settings-redirects' ? 'active' : '' }}" href="{{ route('admin.seo-redirects.index') }}">{{ __('Redirects') }}</a>
                        @endcan
                        @can('main_menu.manage')
                            <a class="nav-link {{ $activeSidebar === 'settings-main-menu' ? 'active' : '' }}" href="{{ route('admin.main-menu.index') }}">{{ __('Main menu') }}</a>
                        @endcan
                        @can('users.manage')
                            <a class="nav-link {{ $activeSidebar === 'settings-administrators' ? 'active' : '' }}" href="{{ route('admin.administrators.index') }}">{{ __('Users') }}</a>
                        @endcan
                        @can('permissions.view')
                            <a class="nav-link {{ $activeSidebar === 'settings-permissions' ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">{{ __('Permissions') }}</a>
                        @endcan
                    </nav>
                </div>
            @endcanany
            @can('log_viewer.view')
                <a class="nav-link" href="{{ url(config('log-viewer.route.attributes.prefix', 'log-viewer')) }}">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-bug-slash"></i></div>
                    Log
                </a>
            @endcan
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Logged in as:</div>
        {{ $adminUser?->name ?? 'BS Admin' }}
    </div>
</nav>
