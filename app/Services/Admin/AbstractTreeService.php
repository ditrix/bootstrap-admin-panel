<?php

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Shared structural operations for tree-based admin modules (load, reorder, delete with reparenting).
 *
 * Subclasses must implement {@see treeStructureLogLabel()}, {@see modelClass()}, and {@see maxRecommendedTreeDepth()}.
 * Content fields (fillable, relations) are intentionally model-specific and live in each subclass.
 *
 * @template TModel of Model
 */
abstract class AbstractTreeService
{
    /**
     * Human-readable name for this tree in logs (e.g. "Menu", "Category tree").
     */
    abstract protected function treeStructureLogLabel(): string;

    /**
     * The fully-qualified Eloquent model class managed by this service.
     *
     * @return class-string<TModel>
     */
    abstract protected function modelClass(): string;

    /**
     * Maximum tree depth (root→leaf, in nodes) before a warning is logged.
     */
    abstract protected function maxRecommendedTreeDepth(): int;

    /**
     * Load all nodes sorted by sort_no and group by parent_id.
     *
     * @return Collection<int|string, Collection<int, TModel>>
     */
    public function buildGroupedTree(): Collection
    {
        try {
            $class = $this->modelClass();

            $grouped = $class::query()
                ->orderBy('sort_no')
                ->orderBy('id')
                ->get()
                ->groupBy('parent_id');

            $maxDepth = $this->maxDepthBelowGroup($grouped, 0);
            $depthLimit = $this->maxRecommendedTreeDepth();
            if ($maxDepth > $depthLimit) {
                Log::warning('Tree depth exceeds recommended maximum of (' . $depthLimit . ') levels', [
                    'tree' => $this->treeStructureLogLabel(),
                    'max_depth' => $maxDepth,
                    'recommended_max_depth' => $depthLimit,
                    'file' => __FILE__,
                    'function' => __FUNCTION__,
                    'class' => __CLASS__,
                    'service_class' => static::class,
                ]);
            }

            return $grouped;
        } catch (\Throwable $e) {
            $this->reportTreeThrowable($e, __FUNCTION__);
            throw $e;
        }
    }

    /**
     * @return EloquentCollection<int, TModel>
     */
    public function allNodesOrderedForMeta(): EloquentCollection
    {
        try {
            $class = $this->modelClass();

            return $class::query()
                ->orderBy('sort_no')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            $this->reportTreeThrowable($e, __FUNCTION__);
            throw $e;
        }
    }

    /**
     * Persist a nested node order payload inside a DB transaction.
     *
     * @param  array<int, array{id: int, children?: array<mixed>}>  $nodes
     */
    public function saveOrder(array $nodes, int $parentId = 0): void
    {
        try {
            DB::transaction(function () use ($nodes, $parentId): void {
                $this->applyTreeOrder($nodes, $parentId);
            });
        } catch (\Throwable $e) {
            $this->reportTreeThrowable($e, __FUNCTION__);
            throw $e;
        }
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

        try {
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
        } catch (\Throwable $e) {
            $this->reportTreeThrowable($e, __FUNCTION__);
            throw $e;
        }
    }

    /**
     * Longest root-to-leaf path length (number of nodes), for parent_id = 0 as roots.
     *
     * @param  Collection<int|string, Collection<int, TModel>>  $byParent
     */
    private function maxDepthBelowGroup(Collection $byParent, int $parentId): int
    {
        $children = $byParent->get($parentId);
        if ($children === null || $children->isEmpty()) {
            return 0;
        }

        $maxBelow = 0;
        foreach ($children as $node) {
            $maxBelow = max(
                $maxBelow,
                1 + $this->maxDepthBelowGroup($byParent, (int) $node->getKey())
            );
        }

        return $maxBelow;
    }

    private function reportTreeThrowable(\Throwable $e, string $function): void
    {
        $context = [
            'tree' => $this->treeStructureLogLabel(),
            'message' => $e->getMessage(),
            'file' => __FILE__,
            'function' => $function,
            'class' => __CLASS__,
            'service_class' => static::class,
        ];

        if ($e instanceof \Error) {
            Log::critical('Tree operation failed', $context);
        } else {
            Log::error('Tree operation failed', $context);
        }
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
