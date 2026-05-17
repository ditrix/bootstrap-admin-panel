<?php

namespace App\Services\Admin;

use App\Models\Banner;
use App\Models\CategoryTree;
use App\Models\Employee;
use App\Models\StaticPage;

/**
 * Builds summary metrics and links for the admin dashboard.
 */
class AdminDashboardService
{
    /**
     * @return array<int, array{label: string, href: string}>
     */
    public function summaryCards(): array
    {
        return [
            [
                'label' => StaticPage::query()->count().' Static Pages',
                'href' => route('admin.static-pages.index'),
            ],
            [
                'label' => Banner::query()->count().' Banners',
                'href' => route('admin.banners.index'),
            ],
            [
                'label' => Employee::query()->count().' Tables',
                'href' => route('admin.tables.index'),
            ],
            [
                'label' => CategoryTree::query()->count().' Category Tree',
                'href' => route('admin.category-tree.index'),
            ],
        ];
    }
}
