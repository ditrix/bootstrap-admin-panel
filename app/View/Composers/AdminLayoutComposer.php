<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLayoutComposer
{
    public function compose(View $view): void
    {
        $view->with('adminUser', Auth::guard('admin')->user());
        $view->with('activeSidebar', $this->resolveActiveSidebar());
    }

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
            default => '',
        };
    }
}
