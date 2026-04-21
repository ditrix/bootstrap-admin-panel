<?php

namespace App\Services\Admin;

use App\Models\CategoryTree;
use Illuminate\Support\Collection;

class CategoryTreeService
{
    /**
     * Load all nodes sorted by sort_no and group by parent_id.
     *
     * @return Collection<int|string, Collection<int, CategoryTree>>
     */
    public function buildGroupedTree(): Collection
    {
        return CategoryTree::query()
            ->orderBy('sort_no')
            ->orderBy('id')
            ->get()
            ->groupBy('parent_id');
    }

    /**
     * Recursively update parent_id and sort_no for every node in the tree.
     *
     * @param  array<int, array{id: int, children?: array<mixed>}>  $nodes
     */
    public function saveOrder(array $nodes, int $parentId = 0): void
    {
        foreach ($nodes as $sortNo => $node) {
            CategoryTree::query()
                ->whereKey($node['id'])
                ->update([
                    'parent_id' => $parentId,
                    'sort_no' => $sortNo,
                ]);

            if (!empty($node['children'])) {
                $this->saveOrder($node['children'], $node['id']);
            }
        }
    }
}
