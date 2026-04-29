<?php

namespace App\Http\Controllers\Admin\Layout;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Light sidenav layout preview.
 */
class LightSidenavController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.layout-sidenav-light');
    }
}
