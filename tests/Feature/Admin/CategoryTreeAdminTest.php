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
            'name' => 'Tree Tester',
            'email' => 'category-tree@example.com',
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
            ->assertViewHas('tree')
            ->assertViewHas('categoryTreeMetaForJs')
            ->assertViewHas('saveOrderUrl');
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
                'id' => $root2->id,
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
                'id' => $a->id,
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

    public function test_update_redirects_to_index_with_success(): void
    {
        $admin = $this->actingAdmin();
        $node = CategoryTree::factory()->create([
            'parent_id' => 0,
            'title' => 'Node A',
            'slug' => 'node-a',
            'description' => 'Desc',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.category-tree.update', $node), [
                'parent_id' => 0,
                'title' => 'Node A updated',
                'slug' => 'node-a-upd',
                'description' => 'New desc',
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.category-tree.index'));
        $node->refresh();
        $this->assertSame('Node A updated', $node->title);
        $this->assertSame('node-a-upd', $node->slug);
        $this->assertSame('New desc', $node->description);
        $this->assertFalse($node->is_active);
    }

    public function test_delete_reparents_direct_children_to_grandparent(): void
    {
        $admin = $this->actingAdmin();
        $root = CategoryTree::factory()->create(['parent_id' => 0, 'sort_no' => 0, 'title' => 'R']);
        $mid = CategoryTree::factory()->childOf($root, 0)->create(['title' => 'M']);
        $leaf = CategoryTree::factory()->childOf($mid, 0)->create(['title' => 'L']);

        $response = $this->actingAs($admin, 'admin')
            ->deleteJson(route('admin.category-tree.destroy', $mid));

        $response->assertOk()
            ->assertJsonPath('message', __('Category tree node deleted.'));

        $this->assertDatabaseMissing('category_trees', ['id' => $mid->id]);
        $this->assertDatabaseHas('category_trees', [
            'id' => $leaf->id,
            'parent_id' => $root->id,
        ]);
    }

    public function test_delete_leaf_removes_node(): void
    {
        $admin = $this->actingAdmin();
        $node = CategoryTree::factory()->create(['parent_id' => 0, 'title' => 'Only']);

        $this->actingAs($admin, 'admin')
            ->deleteJson(route('admin.category-tree.destroy', $node))
            ->assertOk()
            ->assertJsonPath('message', __('Category tree node deleted.'));

        $this->assertDatabaseMissing('category_trees', ['id' => $node->id]);
    }

    public function test_update_rejects_parent_cycle(): void
    {
        $admin = $this->actingAdmin();
        $a = CategoryTree::factory()->create(['parent_id' => 0, 'title' => 'A']);
        $b = CategoryTree::factory()->childOf($a, 0)->create(['title' => 'B']);
        $c = CategoryTree::factory()->childOf($b, 0)->create(['title' => 'C']);

        $response = $this->actingAs($admin, 'admin')
            ->putJson(route('admin.category-tree.update', $a), [
                'parent_id' => $c->id,
                'title' => 'A',
                'slug' => null,
                'description' => null,
                'is_active' => true,
            ]);

        $response->assertUnprocessable();
    }

    public function test_update_rejects_duplicate_slug(): void
    {
        $admin = $this->actingAdmin();
        CategoryTree::factory()->create([
            'parent_id' => 0,
            'title' => 'First',
            'slug' => 'taken-slug',
        ]);
        $other = CategoryTree::factory()->create([
            'parent_id' => 0,
            'title' => 'Second',
            'slug' => 'other-slug',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->putJson(route('admin.category-tree.update', $other), [
                'parent_id' => 0,
                'title' => 'Second',
                'slug' => 'taken-slug',
                'description' => null,
                'is_active' => true,
            ]);

        $response->assertUnprocessable();
    }

    public function test_delete_reparents_multiple_children_preserving_relative_order(): void
    {
        $admin = $this->actingAdmin();
        $root = CategoryTree::factory()->create(['parent_id' => 0, 'sort_no' => 0, 'title' => 'R']);
        $mid = CategoryTree::factory()->childOf($root, 0)->create(['title' => 'M']);
        $leafFirst = CategoryTree::factory()->childOf($mid, 0)->create(['title' => 'L1']);
        $leafSecond = CategoryTree::factory()->childOf($mid, 1)->create(['title' => 'L2']);

        $this->actingAs($admin, 'admin')
            ->deleteJson(route('admin.category-tree.destroy', $mid))
            ->assertOk();

        $leafFirst->refresh();
        $leafSecond->refresh();

        $this->assertSame($root->id, $leafFirst->parent_id);
        $this->assertSame($root->id, $leafSecond->parent_id);
        $this->assertLessThan($leafSecond->sort_no, $leafFirst->sort_no);
    }
}
