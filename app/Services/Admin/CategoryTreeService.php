<?php

namespace App\Services\Admin;

use App\Models\CategoryTree;

/**
 * Tree service for the category hierarchy.
 *
 * Structural operations (load, reorder, delete with reparenting) and shared content operations
 * (createItem, buildParentOptionsHtml) are inherited from {@see AbstractTreeService}.
 *
 * @extends AbstractTreeService<CategoryTree>
 */
class CategoryTreeService extends AbstractTreeService
{
    protected function modelClass(): string
    {
        return CategoryTree::class;
    }
}
