<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the default auth guard to `admin` so `auth()` and Blade `@can` check permissions for the admin user.
 */
class UseAdminAuthGuard
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('admin');

        return $next($request);
    }
}
