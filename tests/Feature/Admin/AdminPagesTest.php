<?php

namespace Tests\Feature\Admin;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): Administrator
    {
        return Administrator::query()->create([
            'name' => 'Page Tester',
            'email' => 'pages@example.com',
            'password' => Hash::make('secret'),
        ]);
    }

    public function test_dashboard_renders_for_authenticated_admin(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewIs('admin.dashboard')
            ->assertViewHas('cards')
            ->assertViewHas('tableId')
            ->assertViewHas('dataUrl');
    }

    public function test_tables_page_renders_with_bootstrap_table_config(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.tables'));

        $response->assertOk()
            ->assertViewIs('admin.tables')
            ->assertViewHas('tableId')
            ->assertViewHas('dataUrl');
    }

    public function test_forms_page_renders(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.forms'));

        $response->assertOk()
            ->assertViewIs('admin.forms');
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
