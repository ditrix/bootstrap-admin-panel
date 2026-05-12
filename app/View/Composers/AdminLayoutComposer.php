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
            request()->routeIs('admin.tables.*') => 'tables',
            request()->routeIs('admin.static-pages.*') => 'static-pages',
            request()->routeIs('admin.banners.*') => 'banners',
            request()->routeIs('admin.category-tree.*') => 'category-tree',
            request()->routeIs('admin.seo-redirects.*') => 'settings-redirects',
            request()->routeIs('admin.main-menu.*') => 'settings-main-menu',
            request()->routeIs('admin.administrators.*') => 'settings-administrators',
            default => '',
        };
    }
}
