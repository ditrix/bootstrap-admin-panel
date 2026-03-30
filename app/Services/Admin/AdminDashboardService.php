<?php

namespace App\Services\Admin;

use App\Models\Employee;

class AdminDashboardService
{
    /**
     * @return array<int, array{label: string, variant: string, href: string}>
     */
    public function summaryCards(): array
    {
        $count = Employee::query()->count();

        return [
            [
                'label' => 'Primary Card',
                'variant' => 'primary',
                'href' => route('admin.tables'),
            ],
            [
                'label' => 'Warning Card',
                'variant' => 'warning',
                'href' => route('admin.charts'),
            ],
            [
                'label' => 'Employees',
                'variant' => 'success',
                'href' => route('admin.tables'),
            ],
            [
                'label' => $count.' records',
                'variant' => 'danger',
                'href' => route('admin.tables'),
            ],
        ];
    }
}
