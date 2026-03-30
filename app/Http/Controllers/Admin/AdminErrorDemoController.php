<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminErrorPage;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminErrorDemoController extends Controller
{
    public function show401(): View
    {
        return view('admin.errors.401', [
            'page' => AdminErrorPage::Unauthorized,
        ]);
    }

    public function show404(): View
    {
        return view('admin.errors.404', [
            'page' => AdminErrorPage::NotFound,
        ]);
    }

    public function show500(): View
    {
        return view('admin.errors.500', [
            'page' => AdminErrorPage::ServerError,
        ]);
    }
}
