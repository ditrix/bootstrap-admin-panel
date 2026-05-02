<?php

namespace App\Services\Admin;

use App\Models\CategoryTree;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Tree service for the category hierarchy.
 *
 * Structural operations (load, reorder, delete with reparenting) are inherited from
 * {@see AbstractTreeService}. This class adds category-specific operations: item creation
 * and the parent selector HTML fragment used in the index form.
 *
 * @extends AbstractTreeService<CategoryTree>
 */
class CategoryTreeService extends AbstractTreeService
{
    protected function modelClass(): string
    {
        return CategoryTree::class;
    }

    /**
     * Create a new category node, appending it after existing siblings under the same parent.
     *
     * @param  array<string, mixed>  $data  Validated fields from {@see StoreCategoryTreeRequest}
     */
    public function createItem(array $data): CategoryTree
    {
        $parentId = (int) $data['parent_id'];
        $maxSort = (int) CategoryTree::query()
            ->where('parent_id', $parentId)
            ->max('sort_no');

        /** @var CategoryTree */
        return CategoryTree::query()->create(array_merge($data, ['sort_no' => $maxSort + 1]));
    }

    /**
     * Build an HTML string of <option> elements for the "parent" select,
     * indented to reflect the tree depth.
     *
     * @param  EloquentCollection<int, CategoryTree>  $nodesMeta
     */
    public function buildParentOptionsHtml(EloquentCollection $nodesMeta, int $selectedId = 0): string
    {
        $byParent = $nodesMeta
            ->map(fn (CategoryTree $n): array => [
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
}
