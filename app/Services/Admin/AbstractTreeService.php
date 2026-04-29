<?php

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Shared structural operations for tree-based admin modules (load, reorder, delete with reparenting).
 *
 * Subclasses must implement {@see modelClass()} to bind a concrete Eloquent model.
 * Content fields (fillable, relations) are intentionally model-specific and live in each subclass.
 *
 * @template TModel of Model
 */
abstract class AbstractTreeService
{
    /**
     * The fully-qualified Eloquent model class managed by this service.
     *
     * @return class-string<TModel>
     */
    abstract protected function modelClass(): string;

    /**
     * Load all nodes sorted by sort_no and group by parent_id.
     *
     * @return Collection<int|string, Collection<int, TModel>>
     */
    public function buildGroupedTree(): Collection
    {
        $class = $this->modelClass();

        return $class::query()
            ->orderBy('sort_no')
            ->orderBy('id')
            ->get()
            ->groupBy('parent_id');
    }

    /**
     * @return EloquentCollection<int, TModel>
     */
    public function allNodesOrderedForMeta(): EloquentCollection
    {
        $class = $this->modelClass();

        return $class::query()
            ->orderBy('sort_no')
            ->orderBy('id')
            ->get();
    }

    /**
     * Persist a nested node order payload inside a DB transaction.
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
     * Direct children of the node are re-linked to the node's parent (preserving relative order),
     * then the node itself is removed — all inside a single DB transaction.
     *
     * @param  TModel  $node
     */
    public function deleteNodeReparentingChildren(Model $node): void
    {
        $class = $this->modelClass();

        DB::transaction(function () use ($node, $class): void {
            $newParentId = (int) $node->parent_id;

            $children = $class::query()
                ->where('parent_id', $node->getKey())
                ->orderBy('sort_no')
                ->orderBy('id')
                ->get();

            $maxSortAmongSiblings = (int) $class::query()
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

    /**
     * Recursively assigns parent_id and sort_no from the nested payload.
     *
     * @param  array<int, array{id: int, children?: array<mixed>}>  $nodes
     */
    private function applyTreeOrder(array $nodes, int $parentId): void
    {
        $class = $this->modelClass();

        foreach ($nodes as $sortNo => $node) {
            $class::query()
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
}
