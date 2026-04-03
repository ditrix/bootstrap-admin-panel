<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticPage\StoreStaticPageRequest;
use App\Http\Requests\Admin\StaticPage\UpdateStaticPageRequest;
use App\Models\StaticPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        return view('admin.pages.static-pages.create', [
            'parents' => StaticPage::query()->ordered()->get(),
        ]);
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
            'parents' => StaticPage::query()
                ->whereKeyNot($staticPage->getKey())
                ->ordered()
                ->get(),
        ]);
    }

    public function update(UpdateStaticPageRequest $request, StaticPage $staticPage): RedirectResponse
    {
        $staticPage->update($request->validated());

        return redirect()
            ->route('admin.static-pages.index')
            ->with('success', __('Static page updated.'));
    }

    public function destroy(StaticPage $staticPage): RedirectResponse
    {
        if (StaticPage::query()->where('parent_id', $staticPage->getKey())->exists()) {
            return redirect()
                ->route('admin.static-pages.index')
                ->with('error', __('Cannot delete a page that has child pages.'));
        }

        $staticPage->delete();

        return redirect()
            ->route('admin.static-pages.index')
            ->with('success', __('Static page deleted.'));
    }
}
