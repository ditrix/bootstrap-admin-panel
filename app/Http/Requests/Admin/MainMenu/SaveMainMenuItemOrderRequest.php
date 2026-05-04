<?php

namespace App\Http\Requests\Admin\MainMenu;

use App\Http\Requests\Admin\AbstractSaveTreeOrderRequest;
use App\Models\MainMenuItem;
use App\Services\Admin\MainMenuItemService;

/**
 * Validates nested tree payload for {@see MainMenuItemService::saveOrder()}.
 */
class SaveMainMenuItemOrderRequest extends AbstractSaveTreeOrderRequest
{
    protected function modelClass(): string
    {
        return MainMenuItem::class;
    }

    protected function invalidIdsMessage(): string
    {
        return __('One or more main menu item IDs are invalid.');
    }
}
