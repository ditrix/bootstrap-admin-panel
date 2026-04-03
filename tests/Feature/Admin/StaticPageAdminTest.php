<?php

namespace Tests\Feature\Admin;

use App\Models\Administrator;
use App\Models\StaticPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaticPageAdminTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): Administrator
    {
        return Administrator::query()->create([
            'name' => 'Static Tester',
            'email' => 'static-pages@example.com',
            'password' => Hash::make('secret'),
        ]);
    }

    public function test_static_pages_index_renders_table_view(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.static-pages.index'));

        $response->assertOk()
            ->assertViewIs('admin.pages.static-pages.view')
            ->assertViewHas('tableId')
            ->assertViewHas('dataUrl');
    }

    public function test_static_pages_table_api_returns_bootstrap_table_payload(): void
    {
        $admin = $this->actingAdmin();
        StaticPage::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->getJson(route('admin.api.static-pages.table'));

        $response->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonCount(3, 'rows');
    }

    public function test_static_page_can_be_created_stored_shown_updated_and_deleted(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.static-pages.create'));
        $response->assertOk()->assertViewIs('admin.pages.static-pages.create');

        $store = $this->actingAs($admin, 'admin')->post(route('admin.static-pages.store'), [
            'parent_id' => 0,
            'code' => 'HOME',
            'title' => 'Home page',
            'description' => 'Desc',
            'content' => '<p>Hello</p>',
            'sort_no' => 1,
            'slug' => 'home',
            'is_active' => true,
        ]);
        $store->assertRedirect(route('admin.static-pages.index'));

        $page = StaticPage::query()->where('slug', 'home')->firstOrFail();

        $this->actingAs($admin, 'admin')->get(route('admin.static-pages.show', $page))->assertOk()
            ->assertViewIs('admin.pages.static-pages.show')
            ->assertViewHas('staticPage', $page);

        $update = $this->actingAs($admin, 'admin')->put(route('admin.static-pages.update', $page), [
            'parent_id' => 0,
            'code' => 'HOME',
            'title' => 'Home updated',
            'description' => null,
            'content' => null,
            'sort_no' => 2,
            'slug' => 'home',
            'is_active' => false,
        ]);
        $update->assertRedirect(route('admin.static-pages.index'));
        $page->refresh();
        $this->assertSame('Home updated', $page->title);
        $this->assertFalse($page->is_active);

        $destroy = $this->actingAs($admin, 'admin')->delete(route('admin.static-pages.destroy', $page));
        $destroy->assertRedirect(route('admin.static-pages.index'));
        $this->assertDatabaseMissing('static_pages', ['id' => $page->id]);
    }

    public function test_static_page_cannot_be_deleted_when_children_exist(): void
    {
        $admin = $this->actingAdmin();
        $parent = StaticPage::factory()->create();
        $child = StaticPage::factory()->childOf($parent)->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.static-pages.destroy', $parent));

        $response->assertRedirect(route('admin.static-pages.index'));
        $this->assertNotNull(StaticPage::query()->find($parent->id));
        $this->assertNotNull(StaticPage::query()->find($child->id));
    }
}
