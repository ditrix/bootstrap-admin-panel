<?php

namespace Database\Seeders;

use App\Authorization\AdminPermission;
use App\Authorization\AdminRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds Spatie roles (admin / manager) and permissions for the `admin` guard.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    private const GUARD = 'admin';

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (AdminPermission::all() as $name) {
            Permission::query()->firstOrCreate(
                ['name' => $name, 'guard_name' => self::GUARD]
            );
        }

        $adminRole = Role::query()->firstOrCreate(
            ['name' => AdminRole::ADMIN, 'guard_name' => self::GUARD]
        );

        $managerRole = Role::query()->firstOrCreate(
            ['name' => AdminRole::MANAGER, 'guard_name' => self::GUARD]
        );

        $adminRole->syncPermissions(Permission::query()->where('guard_name', self::GUARD)->get());

        $managerRole->syncPermissions(
            Permission::query()->where('guard_name', self::GUARD)->whereIn('name', [
                AdminPermission::STATIC_PAGES_MANAGE,
                AdminPermission::CATEGORY_TREE_MANAGE,
                AdminPermission::BANNERS_MANAGE,
                AdminPermission::EMPLOYEES_MANAGE,
            ])->get()
        );
    }
}
