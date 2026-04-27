<?php

namespace Tests\Feature\Admin;

use App\Models\Administrator;
use App\Models\SeoRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeoRedirectAdminTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): Administrator
    {
        return Administrator::query()->create([
            'name' => 'Seo Tester',
            'email' => 'seo@example.com',
            'password' => Hash::make('secret'),
            'is_active' => true,
        ]);
    }

    public function test_index_requires_auth(): void
    {
        $this->get(route('admin.seo-redirects.index'))
            ->assertRedirect(route('admin.entry'));
    }

    public function test_crud_flow(): void
    {
        $admin = $this->actingAdmin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.seo-redirects.create'))
            ->assertOk();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.seo-redirects.store'), [
                'slug_from' => 'old-page',
                'slug_to' => 'new-page',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.seo-redirects.index'));

        $this->assertDatabaseHas('seo_redirects', [
            'slug_from' => 'old-page',
            'slug_to' => 'new-page',
        ]);

        $r = SeoRedirect::query()->where('slug_from', 'old-page')->first();
        $this->assertNotNull($r);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.seo-redirects.edit', $r))
            ->assertOk();

        $this->actingAs($admin, 'admin')
            ->put(route('admin.seo-redirects.update', $r), [
                'slug_from' => 'old-page',
                'slug_to' => 'updated-target',
                'is_active' => false,
            ])
            ->assertRedirect(route('admin.seo-redirects.index'));

        $r->refresh();
        $this->assertFalse($r->is_active);
        $this->assertSame('updated-target', $r->slug_to);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.seo-redirects.destroy', $r))
            ->assertRedirect(route('admin.seo-redirects.index'));

        $this->assertDatabaseMissing('seo_redirects', ['id' => $r->id]);
    }

    public function test_middleware_triggers_301_for_active_path(): void
    {
        SeoRedirect::query()->create([
            'slug_from' => 'legacy-xyz',
            'slug_to' => '/',
            'is_active' => true,
        ]);

        $response = $this->get('/legacy-xyz');
        $response->assertStatus(301);
        $response->assertRedirect('/');
    }

    public function test_middleware_ignores_inactive_and_unknown_path(): void
    {
        SeoRedirect::query()->create([
            'slug_from' => 'gone',
            'slug_to' => '/',
            'is_active' => false,
        ]);

        $r = $this->get('/gone');
        $r->assertNotFound();

        $r2 = $this->get('/no-such-redirect');
        $r2->assertNotFound();
    }
}
