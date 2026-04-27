<?php

namespace Tests\Feature\Admin;

use App\Models\Administrator;
use App\Models\MainMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MainMenuItemAdminTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): Administrator
    {
        return Administrator::query()->create([
            'name' => 'Menu Tester',
            'email' => 'main-menu@example.com',
            'password' => Hash::make('secret'),
            'is_active' => true,
        ]);
    }

    public function test_main_menu_index_renders_for_authenticated_admin(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.main-menu.index'));

        $response->assertOk()
            ->assertViewIs('admin.pages.main-menu.index')
            ->assertViewHas('tree')
            ->assertViewHas('nodesMeta');
    }

    public function test_main_menu_index_requires_auth(): void
    {
        $response = $this->get(route('admin.main-menu.index'));

        $response->assertRedirect(route('admin.entry'));
    }

    public function test_store_creates_node(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.main-menu.store'), [
                'parent_id' => 0,
                'title' => 'Home',
                'slug' => 'home',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.main-menu.index'));
        $this->assertDatabaseHas('main_menu_items', [
            'title' => 'Home',
            'slug' => 'home',
            'parent_id' => 0,
        ]);
    }

    public function test_store_allows_node_under_deep_parent(): void
    {
        $admin = $this->actingAdmin();
        $a = MainMenuItem::factory()->create(['parent_id' => 0, 'title' => 'A']);
        $b = MainMenuItem::factory()->childOf($a, 0)->create(['title' => 'B']);
        $c = MainMenuItem::factory()->childOf($b, 0)->create(['title' => 'C']);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.main-menu.store'), [
                'parent_id' => $c->id,
                'title' => 'D',
                'slug' => 'd',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.main-menu.index'));
        $this->assertDatabaseHas('main_menu_items', [
            'title' => 'D',
            'slug' => 'd',
            'parent_id' => $c->id,
        ]);
    }

    public function test_save_order_accepts_deep_tree(): void
    {
        $admin = $this->actingAdmin();
        $a = MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 0, 'title' => 'A']);
        $b = MainMenuItem::factory()->childOf($a, 0)->create(['title' => 'B']);
        $c = MainMenuItem::factory()->childOf($b, 0)->create(['title' => 'C']);
        $d = MainMenuItem::factory()->childOf($c, 0)->create(['title' => 'D']);

        $nodes = [
            [
                'id' => $a->id,
                'children' => [
                    [
                        'id' => $b->id,
                        'children' => [
                            [
                                'id' => $c->id,
                                'children' => [
                                    ['id' => $d->id],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.main-menu.save-order'), ['nodes' => $nodes]);

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_update_returns_json_with_message(): void
    {
        $admin = $this->actingAdmin();
        $node = MainMenuItem::factory()->create([
            'parent_id' => 0,
            'title' => 'Item',
            'slug' => 'item',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->putJson(route('admin.main-menu.update', $node), [
                'parent_id' => 0,
                'title' => 'Item updated',
                'slug' => 'item-upd',
                'is_active' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('message', __('Menu item updated.'));
        $node->refresh();
        $this->assertSame('Item updated', $node->title);
    }

    public function test_delete_leaf_removes_node(): void
    {
        $admin = $this->actingAdmin();
        $node = MainMenuItem::factory()->create(['parent_id' => 0, 'title' => 'X']);

        $this->actingAs($admin, 'admin')
            ->deleteJson(route('admin.main-menu.destroy', $node))
            ->assertOk()
            ->assertJsonPath('message', __('Menu item deleted.'));

        $this->assertDatabaseMissing('main_menu_items', ['id' => $node->id]);
    }
}
