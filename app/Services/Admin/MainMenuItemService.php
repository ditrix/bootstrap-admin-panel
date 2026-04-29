<?php

namespace App\Services\Admin;

use App\Models\MainMenuItem;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Business logic for the main navigation tree (load, reorder, delete with reparenting).
 */
class MainMenuItemService
{
    /**
     * @return Collection<int|string, Collection<int, MainMenuItem>>
     */
    public function buildGroupedTree(): Collection
    {
        return MainMenuItem::query()
            ->orderBy('sort_no')
            ->orderBy('id')
            ->get()
            ->groupBy('parent_id');
    }

    /**
     * @return EloquentCollection<int, MainMenuItem>
     */
    public function allNodesOrderedForMeta(): EloquentCollection
    {
        return MainMenuItem::query()
            ->orderBy('sort_no')
            ->orderBy('id')
            ->get();
    }

    /**
     * Persist tree order inside a DB transaction.
     *
     * @param  array<int, array{id: int, children?: array<mixed>}>  $nodes
     */
    public function saveOrder(array $nodes, int $parentId = 0): void
    {
        DB::transaction(function () use ($nodes, $parentId): void {
            $this->applyTreeOrder($nodes, $parentId);
        });
    }

    /**
     * Recursively assigns `parent_id` and `sort_no` from the nested payload.
     *
     * @param  array<int, array{id: int, children?: array<mixed>}>  $nodes
     */
    private function applyTreeOrder(array $nodes, int $parentId): void
    {
        foreach ($nodes as $sortNo => $node) {
            MainMenuItem::query()
                ->whereKey($node['id'])
                ->update([
                    'parent_id' => $parentId,
                    'sort_no' => $sortNo,
                ]);

            if (! empty($node['children'])) {
                $this->applyTreeOrder($node['children'], $node['id']);
            }
        }
    }

    /**
     * Direct children of the node are linked to the node's parent, preserving relative order; then the node is removed.
     */
    public function deleteNodeReparentingChildren(MainMenuItem $node): void
    {
        MainMenuItem::query()->getConnection()->transaction(function () use ($node): void {
            $newParentId = (int) $node->parent_id;

            $children = MainMenuItem::query()
                ->where('parent_id', $node->getKey())
                ->orderBy('sort_no')
                ->orderBy('id')
                ->get();

            $maxSortAmongSiblings = (int) MainMenuItem::query()
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
