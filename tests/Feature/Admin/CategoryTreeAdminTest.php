<?php

namespace Tests\Feature\Admin;

use App\Models\Administrator;
use App\Models\CategoryTree;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoryTreeAdminTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): Administrator
    {
        return Administrator::query()->create([
            'name'     => 'Tree Tester',
            'email'    => 'category-tree@example.com',
            'password' => Hash::make('secret'),
        ]);
    }

    public function test_category_tree_index_renders_for_authenticated_admin(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.category-tree.index'));

        $response->assertOk()
            ->assertViewIs('admin.pages.category-tree.index')
            ->assertViewHas('tree');
    }

    public function test_category_tree_index_requires_auth(): void
    {
        $response = $this->get(route('admin.category-tree.index'));

        $response->assertRedirect(route('admin.entry'));
    }

    public function test_save_order_updates_parent_id_and_sort_no(): void
    {
        $admin = $this->actingAdmin();

        $root1 = CategoryTree::factory()->create(['parent_id' => 0, 'sort_no' => 0, 'title' => 'Root 1']);
        $root2 = CategoryTree::factory()->create(['parent_id' => 0, 'sort_no' => 1, 'title' => 'Root 2']);
        $child = CategoryTree::factory()->childOf($root1, 0)->create(['title' => 'Child']);

        // New structure: root2 first, root1 second with child moved to root2
        $nodes = [
            [
                'id'       => $root2->id,
                'children' => [
                    ['id' => $child->id],
                ],
            ],
            ['id' => $root1->id],
        ];

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.category-tree.save-order'), ['nodes' => $nodes]);

        $response->assertOk()->assertJsonPath('success', true);

        $root2->refresh();
        $root1->refresh();
        $child->refresh();

        $this->assertSame(0, $root2->parent_id);
        $this->assertSame(0, $root2->sort_no);

        $this->assertSame(0, $root1->parent_id);
        $this->assertSame(1, $root1->sort_no);

        $this->assertSame($root2->id, $child->parent_id);
        $this->assertSame(0, $child->sort_no);
    }

    public function test_save_order_rejects_nonexistent_ids(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.category-tree.save-order'), [
                'nodes' => [['id' => 99999]],
            ]);

        $response->assertUnprocessable();
    }

    public function test_save_order_requires_nodes(): void
    {
        $admin = $this->actingAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.category-tree.save-order'), []);

        $response->assertUnprocessable();
    }

    public function test_database_structure_matches_after_save_order(): void
    {
        $admin = $this->actingAdmin();

        $a = CategoryTree::factory()->create(['parent_id' => 0, 'sort_no' => 0, 'title' => 'A']);
        $b = CategoryTree::factory()->create(['parent_id' => 0, 'sort_no' => 1, 'title' => 'B']);
        $c = CategoryTree::factory()->create(['parent_id' => 0, 'sort_no' => 2, 'title' => 'C']);

        // Move B under A; C becomes second root
        $nodes = [
            [
                'id'       => $a->id,
                'children' => [['id' => $b->id]],
            ],
            ['id' => $c->id],
        ];

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.category-tree.save-order'), ['nodes' => $nodes])
            ->assertOk();

        $this->assertDatabaseHas('category_trees', ['id' => $a->id, 'parent_id' => 0, 'sort_no' => 0]);
        $this->assertDatabaseHas('category_trees', ['id' => $b->id, 'parent_id' => $a->id, 'sort_no' => 0]);
        $this->assertDatabaseHas('category_trees', ['id' => $c->id, 'parent_id' => 0, 'sort_no' => 1]);
    }
}
