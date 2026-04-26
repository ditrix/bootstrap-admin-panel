<?php

namespace App\Services\Admin;

use App\Models\CategoryTree;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
     * @return EloquentCollection<int, CategoryTree>
     */
    public function allNodesOrderedForMeta(): EloquentCollection
    {
        return CategoryTree::query()
            ->orderBy('sort_no')
            ->orderBy('id')
            ->get();
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

            if (! empty($node['children'])) {
                $this->saveOrder($node['children'], $node['id']);
            }
        }
    }

    /**
     * Direct children of the node are linked to the node's parent, preserving relative order; then the node is removed.
     */
    public function deleteNodeReparentingChildren(CategoryTree $node): void
    {
        CategoryTree::query()->getConnection()->transaction(function () use ($node): void {
            $newParentId = (int) $node->parent_id;

            $children = CategoryTree::query()
                ->where('parent_id', $node->getKey())
                ->orderBy('sort_no')
                ->orderBy('id')
                ->get();

            $maxSortAmongSiblings = (int) CategoryTree::query()
                ->where('parent_id', $newParentId)
                ->where('id', '!=', $node->getKey())
                ->max('sort_no');

            $nextSort = $maxSortAmongSiblings + 1;
            foreach ($children as $child) {
                $child->update([
                    'parent_id' => $newParentId,
                    'sort_no' => $nextSort,
                ]);
                $nextSort++;
            }

            $node->delete();
        });
    }
}
