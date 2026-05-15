<?php

namespace App\Authorization;

/**
 * String names of Spatie permissions for the `admin` guard.
 */
final class AdminPermission
{
    public const DASHBOARD_VIEW = 'dashboard.view';

    public const STATIC_PAGES_MANAGE = 'static_pages.manage';

    public const CATEGORY_TREE_MANAGE = 'category_tree.manage';

    public const BANNERS_MANAGE = 'banners.manage';

    public const EMPLOYEES_MANAGE = 'employees.manage';

    public const SEO_REDIRECTS_MANAGE = 'seo_redirects.manage';

    public const MAIN_MENU_MANAGE = 'main_menu.manage';

    public const USERS_MANAGE = 'users.manage';

    public const PERMISSIONS_VIEW = 'permissions.view';

    public const LOG_VIEWER_VIEW = 'log_viewer.view';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::DASHBOARD_VIEW,
            self::STATIC_PAGES_MANAGE,
            self::CATEGORY_TREE_MANAGE,
            self::BANNERS_MANAGE,
            self::EMPLOYEES_MANAGE,
            self::SEO_REDIRECTS_MANAGE,
            self::MAIN_MENU_MANAGE,
            self::USERS_MANAGE,
            self::PERMISSIONS_VIEW,
            self::LOG_VIEWER_VIEW,
        ];
    }
}
