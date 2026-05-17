<?php

namespace App\Support;

use App\Authorization\AdminPermission;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * First accessible admin URL for the current administrator (dashboard vs content modules).
 */
final class AdminHomeRedirect
{
    /**
     * @throws HttpException
     */
    public static function url(): string
    {
        Auth::shouldUse('admin');
        $user = Auth::guard('admin')->user();
        if ($user === null) {
            return route('admin.entry');
        }

        /** @var list{array{0: string, 1: string}} $priorities */
        $priorities = [
            [AdminPermission::DASHBOARD_VIEW, 'admin.dashboard'],
            [AdminPermission::STATIC_PAGES_MANAGE, 'admin.static-pages.index'],
            [AdminPermission::BANNERS_MANAGE, 'admin.banners.index'],
            [AdminPermission::EMPLOYEES_MANAGE, 'admin.tables.index'],
            [AdminPermission::CATEGORY_TREE_MANAGE, 'admin.category-tree.index'],
            [AdminPermission::SEO_REDIRECTS_MANAGE, 'admin.seo-redirects.index'],
            [AdminPermission::MAIN_MENU_MANAGE, 'admin.main-menu.index'],
            [AdminPermission::USERS_MANAGE, 'admin.administrators.index'],
            [AdminPermission::PERMISSIONS_VIEW, 'admin.permissions.index'],
        ];

        foreach ($priorities as [$permission, $routeName]) {
            if ($user->can($permission)) {
                return route($routeName);
            }
        }

        abort(403);
    }
}
