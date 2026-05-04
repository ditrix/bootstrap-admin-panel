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
     * Create a new tree node, appending it after existing siblings under the same parent.
     *
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function createItem(array $data): Model
    {
        $class = $this->modelClass();
        $parentId = (int) $data['parent_id'];
        $maxSort = (int) $class::query()
            ->where('parent_id', $parentId)
            ->max('sort_no');

        return $class::query()->create(array_merge($data, ['sort_no' => $maxSort + 1]));
    }

    /**
     * Build an HTML string of <option> elements for the "parent" select,
     * indented to reflect the tree depth.
     *
     * @param  EloquentCollection<int, TModel>  $nodesMeta
     */
    public function buildParentOptionsHtml(EloquentCollection $nodesMeta, int $selectedId = 0): string
    {
        $byParent = $nodesMeta
            ->map(static fn ($n): array => [
                'id' => $n->id,
                'parent_id' => (int) $n->parent_id,
                'title' => $n->title,
                'sort_no' => (int) $n->sort_no,
            ])
            ->groupBy('parent_id');

        $selected = $selectedId === 0 ? ' selected' : '';
        $parts = ['<option value="0"'.$selected.'>'.e(__('Root')).'</option>'];
        $walk = function (int $parentId, int $depth) use (&$walk, &$parts, $byParent, $selectedId): void {
            $items = ($byParent->get($parentId) ?? collect())
                ->sortBy([
                    ['sort_no', 'asc'],
                    ['id', 'asc'],
                ]);
            foreach ($items as $n) {
                $indent = $depth > 0 ? str_repeat('— ', $depth).' ' : '';
                $label = $indent.e($n['title']);
                $sel = (int) $n['id'] === $selectedId ? ' selected' : '';
                $parts[] = '<option value="'.(int) $n['id'].'"'.$sel.'>'.$label.'</option>';
                $walk((int) $n['id'], $depth + 1);
            }
        };
        $walk(0, 0);

        return implode('', $parts);
    }

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
