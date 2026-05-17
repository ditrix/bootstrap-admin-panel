<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticPage\StoreStaticPageRequest;
use App\Http\Requests\Admin\StaticPage\UpdateStaticPageRequest;
use App\Models\StaticPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Static pages CRUD (bootstrap-table index, forms).
 */
class StaticPageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.static-pages.view', [
            'tableId' => 'static-pages-bootstrap-table',
            'dataUrl' => route('admin.api.static-pages.table'),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.static-pages.create');
    }

    public function store(StoreStaticPageRequest $request): RedirectResponse
    {
        StaticPage::query()->create($request->validated());

        return redirect()
            ->route('admin.static-pages.index')
            ->with('success', __('Static page created.'));
    }

    public function show(StaticPage $staticPage): View
    {
        return view('admin.pages.static-pages.show', [
            'staticPage' => $staticPage,
        ]);
    }

    public function edit(StaticPage $staticPage): View
    {
        return view('admin.pages.static-pages.edit', [
            'staticPage' => $staticPage,
        ]);
    }

    public function update(UpdateStaticPageRequest $request, StaticPage $staticPage): RedirectResponse
    {
        $staticPage->update($request->validated());

        return redirect()
            ->route('admin.static-pages.index')
            ->with('success', __('Static page updated.'));
    }

    public function destroy(Request $request, StaticPage $staticPage): JsonResponse|RedirectResponse
    {
        $staticPage->delete();
        $message = __('Static page deleted.');
        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.static-pages.index')
            ->with('success', $message);
    }
}
