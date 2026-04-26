<?php

namespace Tests\Feature\Admin;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdministratorAdminTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): Administrator
    {
        return Administrator::query()->create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('Password1!'),
            'is_active' => true,
        ]);
    }

    public function test_index_and_create_require_auth(): void
    {
        $this->get(route('admin.administrators.index'))->assertRedirect(route('admin.entry'));
        $this->get(route('admin.administrators.create'))->assertRedirect(route('admin.entry'));
    }

    public function test_store_and_edit_update_delete(): void
    {
        $admin = $this->actingAdmin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.administrators.store'), [
                'name' => 'New Admin',
                'email' => 'newadmin@example.com',
                'password' => 'Password1!',
                'password_confirmation' => 'Password1!',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.administrators.index'));

        $created = Administrator::query()->where('email', 'newadmin@example.com')->first();
        $this->assertNotNull($created);

        $this->actingAs($admin, 'admin')
            ->put(route('admin.administrators.update', $created), [
                'name' => 'Renamed',
                'email' => 'newadmin@example.com',
                'is_active' => true,
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
        $admin = $this->actingAdmin();

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.administrators.destroy', $admin))
            ->assertRedirect(route('admin.administrators.index'))
            ->assertSessionHas('error');
    }
}
