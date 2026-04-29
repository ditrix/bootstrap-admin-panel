<?php

namespace App\Http\Middleware;

use App\Models\SeoRedirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies 301 redirects from `/seo_redirects` for GET requests outside the admin UI.
 */
class ApplySeoRedirectMiddleware
{
    /**
     * @param  \Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        if (! Schema::hasTable('seo_redirects')) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');

        $match = SeoRedirect::query()
            ->active()
            ->where('slug_from', $path)
            ->first();

        if ($match === null) {
            return $next($request);
        }

        $to = $match->slug_to;
        if (str_starts_with($to, 'http://') || str_starts_with($to, 'https://')) {
            return redirect()->away($to, 301);
        }

        if (str_starts_with($to, '/')) {
            return redirect($to, 301);
        }

        return redirect('/'.ltrim($to, '/'), 301);
    }
}
