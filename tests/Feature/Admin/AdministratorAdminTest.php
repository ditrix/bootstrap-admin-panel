<?php

namespace Tests\Feature\Admin;

use App\Authorization\AdminRole;
use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdministratorAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_bootstrap_table_view(): void
    {
        $admin = $this->adminWithFullAccess(['email' => 'manager@example.com']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.administrators.index'))
            ->assertOk()
            ->assertViewHas('tableId')
            ->assertViewHas('dataUrl')
            ->assertViewHas('currentAdminId', (int) $admin->getKey());
    }

    public function test_administrators_table_api_returns_bootstrap_table_payload(): void
    {
        $admin = $this->adminWithFullAccess(['email' => 'table-api@example.com']);

        $role = Role::findByName(AdminRole::ADMIN, 'admin');
        Administrator::factory()->count(2)->create(['role_id' => $role->getKey()]);

        $response = $this->actingAs($admin, 'admin')->getJson(route('admin.api.administrators.table'));

        $response->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonCount(3, 'rows');
    }

    public function test_index_and_create_require_auth(): void
    {
        $this->get(route('admin.administrators.index'))->assertRedirect(route('admin.entry'));
        $this->get(route('admin.administrators.create'))->assertRedirect(route('admin.entry'));
    }

    public function test_store_and_edit_update_delete(): void
    {
        $admin = $this->adminWithFullAccess(['email' => 'crud@example.com']);
        $role = Role::findByName(AdminRole::ADMIN, 'admin');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.administrators.store'), [
                'name' => 'New Admin',
                'email' => 'newadmin@example.com',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
                'is_active' => true,
                'role_id' => $role->getKey(),
            ])
            ->assertRedirect(route('admin.administrators.index'));

        $created = Administrator::query()->where('email', 'newadmin@example.com')->first();
        $this->assertNotNull($created);

        $this->actingAs($admin, 'admin')
            ->put(route('admin.administrators.update', $created), [
                'name' => 'Renamed',
                'email' => 'newadmin@example.com',
                'is_active' => true,
                'role_id' => $role->getKey(),
            ])
            ->assertRedirect(route('admin.administrators.index'));

        $created->refresh();
        $this->assertSame('Renamed', $created->name);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.administrators.destroy', $created))
            ->assertRedirect(route('admin.administrators.index'));

        $this->assertDatabaseMissing('administrators', ['email' => 'newadmin@example.com']);
    }

    public function test_cannot_delete_self(): void
    {
        $admin = $this->adminWithFullAccess(['email' => 'self@example.com']);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.administrators.destroy', $admin))
            ->assertRedirect(route('admin.administrators.index'))
            ->assertSessionHas('error');
    }
}
