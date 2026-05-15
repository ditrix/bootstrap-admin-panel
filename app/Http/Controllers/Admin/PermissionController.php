<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Read-only list of Spatie permissions for the admin guard.
 */
class PermissionController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.permissions.index', [
            'tableId' => 'permissions-bootstrap-table',
            'dataUrl' => route('admin.api.permissions.simple'),
        ]);
    }
}
