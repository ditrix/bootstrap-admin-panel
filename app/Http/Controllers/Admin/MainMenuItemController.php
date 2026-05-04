<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\HasTreeCrudActions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MainMenu\SaveMainMenuItemOrderRequest;
use App\Http\Requests\Admin\MainMenu\StoreMainMenuItemRequest;
use App\Http\Requests\Admin\MainMenu\UpdateMainMenuItemRequest;
use App\Models\MainMenuItem;
use App\Services\Admin\AbstractTreeService;
use App\Services\Admin\MainMenuItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Main menu tree (page-based create/edit, reorder DnD, update/destroy).
 */
class MainMenuItemController extends Controller
{
    use HasTreeCrudActions;

    public function __construct(private readonly MainMenuItemService $service) {}

    protected function getTreeService(): AbstractTreeService
    {
        return $this->service;
    }

    protected function indexView(): string
    {
        return 'admin.pages.main-menu.index';
    }

    protected function createView(): string
    {
        return 'admin.pages.main-menu.create';
    }

    protected function editView(): string
    {
        return 'admin.pages.main-menu.edit';
    }

    protected function editModelKey(): string
    {
        return 'mainMenuItem';
    }

    protected function jsMetaKey(): string
    {
        return 'mainMenuMetaForJs';
    }

    protected function saveOrderRouteName(): string
    {
        return 'admin.main-menu.save-order';
    }

    protected function indexRouteName(): string
    {
        return 'admin.main-menu.index';
    }

    protected function storeSuccessMessage(): string
    {
        return __('Menu item created.');
    }

    protected function updateSuccessMessage(): string
    {
        return __('Menu item updated.');
    }

    protected function destroySuccessMessage(): string
    {
        return __('Menu item deleted.');
    }

    // -------------------------------------------------------------------------
    // Public methods with typed route model binding — delegate to trait helpers
    // -------------------------------------------------------------------------

    public function edit(MainMenuItem $mainMenuItem): View
    {
        return $this->handleEdit($mainMenuItem);
    }

    public function store(StoreMainMenuItemRequest $request): RedirectResponse
    {
        return $this->handleStore($request);
    }

    public function saveOrder(SaveMainMenuItemOrderRequest $request): JsonResponse
    {
        return $this->executeSaveOrder($request);
    }

    public function update(UpdateMainMenuItemRequest $request, MainMenuItem $mainMenuItem): RedirectResponse
    {
        return $this->handleUpdate($request, $mainMenuItem);
    }

    public function destroy(Request $request, MainMenuItem $mainMenuItem): JsonResponse|RedirectResponse
    {
        return $this->handleDestroy($request, $mainMenuItem);
    }
}
