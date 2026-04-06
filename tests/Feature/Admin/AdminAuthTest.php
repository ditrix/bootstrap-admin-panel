<?php

namespace Tests\Feature\Admin;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_adm_entry_shows_login_when_guest(): void
    {
        $response = $this->get('/adm');

        $response->assertOk()
            ->assertViewIs('admin.auth.login');
    }

    public function test_adm_entry_redirects_to_dashboard_when_authenticated(): void
    {
        $admin = Administrator::query()->create([
            'name' => 'Tester',
            'email' => 't@example.com',
            'password' => Hash::make('secret'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/adm');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        Administrator::query()->create([
            'name' => 'Tester',
            'email' => 'auth@example.com',
            'password' => Hash::make('secret'),
        ]);

        $response = $this->post('/adm/login', [
            'email' => 'auth@example.com',
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(Administrator::query()->where('email', 'auth@example.com')->first(), 'admin');
    }

    public function test_login_with_invalid_credentials_fails_validation(): void
    {
        Administrator::query()->create([
            'name' => 'Tester',
            'email' => 'bad@example.com',
            'password' => Hash::make('secret'),
        ]);

        $response = $this->from(route('admin.entry'))->post('/adm/login', [
            'email' => 'bad@example.com',
            'password' => 'wrong',
        ]);

        $response->assertRedirect(route('admin.entry'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_logout_clears_session_and_redirects_to_entry(): void
    {
        $admin = Administrator::query()->create([
            'name' => 'Tester',
            'email' => 'out@example.com',
            'password' => Hash::make('secret'),
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/adm/logout');

        $response->assertRedirect(route('admin.entry'));
        $this->assertGuest('admin');
    }

    public function test_protected_admin_route_redirects_guest_to_login_entry(): void
    {
        $response = $this->get('/adm/dashboard');

        $response->assertRedirect(route('admin.entry'));
    }

    public function test_register_creates_administrator_and_login_succeeds(): void
    {
        $response = $this->post('/adm/register', [
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

        $login = $this->post('/adm/login', [
            'email' => 'newuser@example.com',
            'password' => 'Password1!',
        ]);

        $login->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(
            Administrator::query()->where('email', 'newuser@example.com')->first(),
            'admin'
        );
    }

    public function test_register_rejects_duplicate_email(): void
    {
        Administrator::query()->create([
            'name' => 'Existing',
            'email' => 'dup@example.com',
            'password' => Hash::make('secret'),
        ]);

        $response = $this->from(route('admin.register'))->post('/adm/register', [
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
