<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use Illuminate\View\View;

/**
 * Admin dashboard with summary cards and embedded employees bootstrap-table.
 */
class DashboardController extends Controller
{
    public function __construct(
        private AdminDashboardService $dashboardService,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'cards' => $this->dashboardService->summaryCards(),
            'tableId' => 'dashboard-employees-bootstrap-table',
            'dataUrl' => route('admin.api.employees'),
        ]);
    }
}
