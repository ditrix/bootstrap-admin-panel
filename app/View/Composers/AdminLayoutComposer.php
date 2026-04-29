<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Shares authenticated admin user and active sidebar key with SB Admin layouts.
 */
class AdminLayoutComposer
{
    /**
     * Bind data to the view before it is rendered.
     */
    public function compose(View $view): void
    {
        $view->with('adminUser', Auth::guard('admin')->user());
        $view->with('activeSidebar', $this->resolveActiveSidebar());
    }

    /**
     * Map the current route to a sidebar activation key for SB Admin navigation.
     */
    private function resolveActiveSidebar(): string
    {
        return match (true) {
            request()->routeIs('admin.dashboard') => 'dashboard',
            request()->routeIs('admin.layouts.static') => 'layout-static',
            request()->routeIs('admin.layouts.sidenav-light') => 'layout-sidenav-light',
            request()->routeIs('admin.charts') => 'charts',
            request()->routeIs('admin.tables') => 'tables',
            request()->routeIs('admin.forms') => 'forms',
            request()->routeIs('admin.blank') => 'blank',
            request()->routeIs('admin.register') => 'register',
            request()->routeIs('admin.password.request') => 'password',
            request()->routeIs('admin.errors.401') => 'error-401',
            request()->routeIs('admin.errors.404-demo') => 'error-404',
            request()->routeIs('admin.errors.500-demo') => 'error-500',
            request()->routeIs('admin.static-pages.*') => 'static-pages',
            request()->routeIs('admin.category-tree.*') => 'category-tree',
            request()->routeIs('admin.seo-redirects.*') => 'settings-redirects',
            request()->routeIs('admin.main-menu.*') => 'settings-main-menu',
            request()->routeIs('admin.administrators.*') => 'settings-administrators',
            default => '',
        };
    }
}
