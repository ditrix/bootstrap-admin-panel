<?php

namespace Tests;

use App\Authorization\AdminRole;
use App\Models\Administrator;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Administrator with the `admin` role and all permissions (for feature tests).
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function adminWithFullAccess(array $attributes = []): Administrator
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $role = Role::findByName(AdminRole::ADMIN, 'admin');

        return Administrator::query()->create(array_merge([
            'name' => 'Admin',
            'email' => 'full-access@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
            'role_id' => $role->getKey(),
        ], $attributes));
    }
}
