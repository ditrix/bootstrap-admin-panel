<?php

namespace App\Http\Requests\Admin\CategoryTree;

use App\Http\Requests\Admin\AbstractSaveTreeOrderRequest;
use App\Models\CategoryTree;
use App\Services\Admin\CategoryTreeService;

/**
 * Validates nested tree payload persisted by {@see CategoryTreeService::saveOrder()}.
 */
class SaveCategoryTreeOrderRequest extends AbstractSaveTreeOrderRequest
{
    protected function modelClass(): string
    {
        return CategoryTree::class;
    }

    protected function invalidIdsMessage(): string
    {
        return __('One or more category tree IDs are invalid.');
    }
}
