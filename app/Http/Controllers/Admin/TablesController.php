<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\View\View;

/**
 * Employees bootstrap-table listing ({@see Employee} JSON API).
 */
class TablesController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.tables.index', [
            'tableId' => 'employees-bootstrap-table',
            'dataUrl' => route('admin.api.employees'),
        ]);
    }
}
