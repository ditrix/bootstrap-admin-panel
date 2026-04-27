<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeoRedirect\StoreSeoRedirectRequest;
use App\Http\Requests\Admin\SeoRedirect\UpdateSeoRedirectRequest;
use App\Models\SeoRedirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoRedirectController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.seo-redirects.index', [
            'tableId' => 'seo-redirects-bootstrap-table',
            'dataUrl' => route('admin.api.seo-redirects.table'),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.seo-redirects.create');
    }

    public function store(StoreSeoRedirectRequest $request): RedirectResponse
    {
        SeoRedirect::query()->create($request->validated());

        return redirect()
            ->route('admin.seo-redirects.index')
            ->with('success', __('Redirect created.'));
    }

    public function edit(SeoRedirect $seoRedirect): View
    {
        return view('admin.pages.seo-redirects.edit', [
            'seoRedirect' => $seoRedirect,
        ]);
    }

    public function update(UpdateSeoRedirectRequest $request, SeoRedirect $seoRedirect): RedirectResponse
    {
        $seoRedirect->update($request->validated());

        return redirect()
            ->route('admin.seo-redirects.index')
            ->with('success', __('Redirect updated.'));
    }

    public function destroy(Request $request, SeoRedirect $seoRedirect): JsonResponse|RedirectResponse
    {
        $seoRedirect->delete();
        $message = __('Redirect deleted.');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.seo-redirects.index')
            ->with('success', $message);
    }
}
