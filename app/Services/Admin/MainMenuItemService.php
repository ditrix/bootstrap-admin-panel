<?php

namespace App\Services\Admin;

use App\Models\MainMenuItem;

/**
 * Tree service for the main navigation menu.
 *
 * Structural operations (load, reorder, delete with reparenting) and shared content operations
 * (createItem, buildParentOptionsHtml) are inherited from {@see AbstractTreeService}.
 *
 * @extends AbstractTreeService<MainMenuItem>
 */
class MainMenuItemService extends AbstractTreeService
{
    protected function modelClass(): string
    {
        return MainMenuItem::class;
    }
}
