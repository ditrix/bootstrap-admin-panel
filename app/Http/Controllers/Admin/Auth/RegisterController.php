<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Authorization\AdminRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\RegisterAccountRequest;
use App\Models\Administrator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Self-service registration of administrator accounts (demo flow).
 */
class RegisterController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.register');
    }

    public function store(RegisterAccountRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $name = trim($data['first_name'].' '.$data['last_name']);

        $managerRole = Role::query()
            ->where('name', AdminRole::MANAGER)
            ->where('guard_name', 'admin')
            ->first();

        Administrator::query()->create([
            'name' => $name,
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => now(),
            'is_active' => true,
            'role_id' => $managerRole?->getKey(),
        ]);

        return redirect()->route('admin.entry');
    }
}
