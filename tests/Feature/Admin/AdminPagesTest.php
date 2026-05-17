<?php

namespace Tests\Feature\Admin;

use App\Authorization\AdminRole;
use App\Models\Administrator;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function actingAdmin(): Administrator
    {
        $role = Role::findByName(AdminRole::ADMIN, 'admin');

        return Administrator::query()->create([
            'name' => 'Page Tester',
            'email' => 'pages@example.com',
            'password' => Hash::make('secret'),
            'is_active' => true,
            'role_id' => $role->getKey(),
        ]);
    }

    public function test_dashboard_renders_for_authenticated_admin(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewIs('admin.dashboard')
            ->assertViewHas('cards');
    }

    public function test_tables_page_renders_with_bootstrap_table_config(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.tables.index'));

        $response->assertOk()
            ->assertViewIs('admin.pages.tables.index')
            ->assertViewHas('tableId')
            ->assertViewHas('dataUrl');
    }

    public function test_admin_employees_table_api_returns_bootstrap_table_payload(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->getJson(route('admin.api.employees'));

        $response->assertOk()
            ->assertJsonStructure([
                'total',
                'rows',
            ]);
    }
}
