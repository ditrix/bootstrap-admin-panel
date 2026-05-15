<?php

namespace Tests\Feature\Admin;

use App\Authorization\AdminRole;
use App\Models\Administrator;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeAdmin(string $email, bool $isActive = true): Administrator
    {
        $role = Role::findByName(AdminRole::ADMIN, 'admin');

        return Administrator::query()->create([
            'name' => 'Tester',
            'email' => $email,
            'password' => Hash::make('secret'),
            'is_active' => $isActive,
            'role_id' => $role->getKey(),
        ]);
    }

    public function test_adm_entry_shows_login_when_guest(): void
    {
        $response = $this->get('/admin');

        $response->assertOk()
            ->assertViewIs('admin.auth.login');
    }

    public function test_adm_entry_redirects_to_dashboard_when_authenticated(): void
    {
        $admin = $this->makeAdmin('t@example.com');

        $response = $this->actingAs($admin, 'admin')->get('/admin');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        $this->makeAdmin('auth@example.com');

        $response = $this->post('/admin/login', [
            'email' => 'auth@example.com',
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(Administrator::query()->where('email', 'auth@example.com')->first(), 'admin');
    }

    public function test_login_with_invalid_credentials_fails_validation(): void
    {
        $this->makeAdmin('bad@example.com');

        $response = $this->from(route('admin.entry'))->post('/admin/login', [
            'email' => 'bad@example.com',
            'password' => 'wrong',
        ]);

        $response->assertRedirect(route('admin.entry'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_logout_clears_session_and_redirects_to_entry(): void
    {
        $admin = $this->makeAdmin('out@example.com');

        $response = $this->actingAs($admin, 'admin')->post('/admin/logout');

        $response->assertRedirect(route('admin.entry'));
        $this->assertGuest('admin');
    }

    public function test_protected_admin_route_redirects_guest_to_login_entry(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('admin.entry'));
    }

    public function test_register_creates_administrator_and_login_succeeds(): void
    {
        $response = $this->post('/admin/register', [
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'newuser@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertRedirect(route('admin.entry'));
        $this->assertDatabaseHas('administrators', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);
        $this->assertGuest('admin');

        $login = $this->post('/admin/login', [
            'email' => 'newuser@example.com',
            'password' => 'Password1!',
        ]);

        $login->assertRedirect(route('admin.static-pages.index'));
        $this->assertAuthenticatedAs(
            Administrator::query()->where('email', 'newuser@example.com')->first(),
            'admin'
        );
    }

    public function test_login_rejects_inactive_account_with_valid_password(): void
    {
        $this->makeAdmin('inactive@example.com', false);

        $response = $this->from(route('admin.entry'))->post('/admin/login', [
            'email' => 'inactive@example.com',
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('admin.entry'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_register_rejects_duplicate_email(): void
    {
        $this->makeAdmin('dup@example.com');

        $response = $this->from(route('admin.register'))->post('/admin/register', [
            'first_name' => 'Other',
            'last_name' => 'Person',
            'email' => 'dup@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertRedirect(route('admin.register'));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, Administrator::query()->where('email', 'dup@example.com')->count());
    }
}
