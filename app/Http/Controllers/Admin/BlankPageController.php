<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Blank starter page shell (SB Admin).
 */
class BlankPageController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.blank');
    }
}
