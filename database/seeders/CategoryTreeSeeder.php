<?php

namespace Database\Seeders;

use App\Models\CategoryTree;
use Illuminate\Database\Seeder;

class CategoryTreeSeeder extends Seeder
{
    /**
     * Seed 20 nodes with depth 1–4.
     *
     * Tree shape (parent_id = 0 means root):
     *   Root 1 (sort 0)
     *     Child 1.1 (sort 0)
     *       Child 1.1.1 (sort 0)
     *         Child 1.1.1.1 (sort 0)
     *       Child 1.1.2 (sort 1)
     *     Child 1.2 (sort 1)
     *       Child 1.2.1 (sort 0)
     *       Child 1.2.2 (sort 1)
     *   Root 2 (sort 1)
     *     Child 2.1 (sort 0)
     *       Child 2.1.1 (sort 0)
     *       Child 2.1.2 (sort 1)
     *     Child 2.2 (sort 1)
     *   Root 3 (sort 2)
     *     Child 3.1 (sort 0)
     *       Child 3.1.1 (sort 0)
     *       Child 3.1.2 (sort 1)
     *     Child 3.2 (sort 1)
     *   Root 4 (sort 3)
     *     Child 4.1 (sort 0)
     *     Child 4.2 (sort 1)
     *   Root 5 (sort 4)
     */
    public function run(): void
    {
        $titles = [
            'Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports',
            'Phones', 'Laptops', 'Cameras', 'Tablets',
            'Men', 'Women', 'Kids',
            'Fiction', 'Science', 'History',
            'Furniture', 'Kitchen',
            'Football', 'Tennis',
            'Smartphones',
        ];

        $t = $titles;
        $sortRoot = 0;

        $r1 = CategoryTree::factory()->create(['title' => array_shift($t), 'sort_no' => $sortRoot++, 'parent_id' => 0]);
        $r2 = CategoryTree::factory()->create(['title' => array_shift($t), 'sort_no' => $sortRoot++, 'parent_id' => 0]);
        $r3 = CategoryTree::factory()->create(['title' => array_shift($t), 'sort_no' => $sortRoot++, 'parent_id' => 0]);
        $r4 = CategoryTree::factory()->create(['title' => array_shift($t), 'sort_no' => $sortRoot++, 'parent_id' => 0]);
        $r5 = CategoryTree::factory()->create(['title' => array_shift($t), 'sort_no' => $sortRoot, 'parent_id' => 0]);

        // r1 subtree (depth 4)
        $c11 = CategoryTree::factory()->childOf($r1, 0)->create(['title' => array_shift($t)]);
        $c12 = CategoryTree::factory()->childOf($r1, 1)->create(['title' => array_shift($t)]);
        $c111 = CategoryTree::factory()->childOf($c11, 0)->create(['title' => array_shift($t)]);
        $c112 = CategoryTree::factory()->childOf($c11, 1)->create(['title' => array_shift($t)]);
        CategoryTree::factory()->childOf($c111, 0)->create(['title' => array_shift($t)]);

        // r2 subtree (depth 3)
        $c21 = CategoryTree::factory()->childOf($r2, 0)->create(['title' => array_shift($t)]);
        $c22 = CategoryTree::factory()->childOf($r2, 1)->create(['title' => array_shift($t)]);
        CategoryTree::factory()->childOf($c21, 0)->create(['title' => array_shift($t)]);
        CategoryTree::factory()->childOf($c21, 1)->create(['title' => array_shift($t)]);

        // r3 subtree (depth 3)
        $c31 = CategoryTree::factory()->childOf($r3, 0)->create(['title' => array_shift($t)]);
        CategoryTree::factory()->childOf($r3, 1)->create(['title' => array_shift($t)]);
        CategoryTree::factory()->childOf($c31, 0)->create(['title' => array_shift($t)]);
        CategoryTree::factory()->childOf($c31, 1)->create(['title' => array_shift($t)]);

        // r4 subtree (depth 2)
        CategoryTree::factory()->childOf($r4, 0)->create(['title' => array_shift($t)]);
        CategoryTree::factory()->childOf($r4, 1)->create(['title' => array_shift($t)]);
    }
}
