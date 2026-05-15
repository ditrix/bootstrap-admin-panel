<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminHomeRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Landing route: redirect authenticated admins to dashboard or show login view.
 */
class AdminEntryController extends Controller
{
    public function __invoke(): RedirectResponse|View
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->to(AdminHomeRedirect::url());
        }

        return view('admin.auth.login');
    }
}
