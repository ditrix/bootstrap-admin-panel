<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TablesController extends Controller
{
    public function index(): View
    {
        return view('admin.tables', [
            'tableId' => 'employees-bootstrap-table',
            'dataUrl' => route('admin.api.employees'),
        ]);
    }
}
