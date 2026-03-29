<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\EmployeeListingService;
use Illuminate\View\View;

class TablesController extends Controller
{
    public function __construct(
        private EmployeeListingService $employeeListingService,
    ) {}

    public function index(): View
    {
        return view('admin.tables', [
            'employees' => $this->employeeListingService->orderedForDataTable(),
        ]);
    }
}
