<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\HasTreeCrudActions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryTree\SaveCategoryTreeOrderRequest;
use App\Http\Requests\Admin\CategoryTree\StoreCategoryTreeRequest;
use App\Http\Requests\Admin\CategoryTree\UpdateCategoryTreeRequest;
use App\Models\CategoryTree;
use App\Services\Admin\AbstractTreeService;
use App\Services\Admin\CategoryTreeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tree CRUD for categories (drag-and-drop order, page-based create/edit/update/destroy).
 */
class CategoryTreeController extends Controller
{
    use HasTreeCrudActions;

    public function __construct(private readonly CategoryTreeService $service) {}

    protected function getTreeService(): AbstractTreeService
    {
        return $this->service;
    }

    protected function indexView(): string
    {
        return 'admin.pages.category-tree.index';
    }

    protected function createView(): string
    {
        return 'admin.pages.category-tree.create';
    }

    protected function editView(): string
    {
        return 'admin.pages.category-tree.edit';
    }

    protected function editModelKey(): string
    {
        return 'categoryTree';
    }

    protected function jsMetaKey(): string
    {
        return 'categoryTreeMetaForJs';
    }

    protected function saveOrderRouteName(): string
    {
        return 'admin.category-tree.save-order';
    }

    protected function indexRouteName(): string
    {
        return 'admin.category-tree.index';
    }

    protected function storeSuccessMessage(): string
    {
        return __('Category node created.');
    }

    protected function updateSuccessMessage(): string
    {
        return __('Category tree node updated.');
    }

    protected function destroySuccessMessage(): string
    {
        return __('Category tree node deleted.');
    }

    // -------------------------------------------------------------------------
    // Public methods with typed route model binding — delegate to trait helpers
    // -------------------------------------------------------------------------

    public function edit(CategoryTree $categoryTree): View
    {
        return $this->handleEdit($categoryTree);
    }

    public function store(StoreCategoryTreeRequest $request): RedirectResponse
    {
        return $this->handleStore($request);
    }

    public function saveOrder(SaveCategoryTreeOrderRequest $request): JsonResponse
    {
        return $this->executeSaveOrder($request);
    }

    public function update(UpdateCategoryTreeRequest $request, CategoryTree $categoryTree): RedirectResponse
    {
        return $this->handleUpdate($request, $categoryTree);
    }

    public function destroy(Request $request, CategoryTree $categoryTree): JsonResponse|RedirectResponse
    {
        return $this->handleDestroy($request, $categoryTree);
    }
}
