<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use App\Services\Admin\EmployeeListingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private AdminDashboardService $dashboardService,
        private EmployeeListingService $employeeListingService,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'cards' => $this->dashboardService->summaryCards(),
            'dashboardEmployees' => $this->employeeListingService->orderedForDataTable(),
        ]);
    }
}
