<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\RegisterAccountRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.register');
    }

    public function store(RegisterAccountRequest $request): RedirectResponse
    {
        return redirect()
            ->route('admin.register')
            ->with('status', __('admin.register_disabled'));
    }
}
