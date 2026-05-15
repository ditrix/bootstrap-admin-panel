<?php

namespace Database\Seeders;

use App\Authorization\AdminRole;
use App\Models\Administrator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()
            ->where('name', AdminRole::ADMIN)
            ->where('guard_name', 'admin')
            ->firstOrFail();

        Administrator::query()->updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'role_id' => $adminRole->getKey(),
            ]
        );

        $managerRole = Role::query()
            ->where('name', AdminRole::MANAGER)
            ->where('guard_name', 'admin')
            ->firstOrFail();

        Administrator::query()->updateOrCreate(
            ['email' => 'manager@mail.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'role_id' => $managerRole->getKey(),
            ]
        );
    }
}
