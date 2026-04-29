<?php

namespace App\Services\Admin;

use App\Models\CategoryTree;

/**
 * Tree service for the category hierarchy.
 *
 * Structural operations (load, reorder, delete with reparenting) are inherited from
 * {@see AbstractTreeService}. This class binds the {@see CategoryTree} model.
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
