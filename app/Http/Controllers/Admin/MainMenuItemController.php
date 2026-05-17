<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MainMenu\SaveMainMenuItemOrderRequest;
use App\Http\Requests\Admin\MainMenu\StoreMainMenuItemRequest;
use App\Http\Requests\Admin\MainMenu\UpdateMainMenuItemRequest;
use App\Models\MainMenuItem;
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
    public function __construct(private readonly MainMenuItemService $service) {}

    public function index(): View
    {
        $tree = $this->service->buildGroupedTree();
        $nodesMeta = $this->service->allNodesOrderedForMeta();

        $mainMenuMetaForJs = $nodesMeta
            ->map(static fn (MainMenuItem $n): array => [
                'id' => $n->id,
                'parent_id' => (int) $n->parent_id,
                'title' => $n->title,
                'sort_no' => (int) $n->sort_no,
            ])
            ->values()
            ->all();

        return view('admin.pages.main-menu.index', [
            'tree' => $tree,
            'mainMenuMetaForJs' => $mainMenuMetaForJs,
            'saveOrderUrl' => route('admin.main-menu.save-order'),
        ]);
    }

    public function create(): View
    {
        $nodesMeta = $this->service->allNodesOrderedForMeta();
        $selectedParentId = (int) (request()->old('parent_id') ?? 0);

        return view('admin.pages.main-menu.create', [
            'parentOptionsHtml' => $this->service->buildParentOptionsHtml($nodesMeta, $selectedParentId),
        ]);
    }

    public function edit(MainMenuItem $mainMenuItem): View
    {
        $nodesMeta = $this->service->allNodesOrderedForMeta();
        $selectedParentId = (int) (request()->old('parent_id') ?? $mainMenuItem->parent_id);

        return view('admin.pages.main-menu.edit', [
            'mainMenuItem' => $mainMenuItem,
            'parentOptionsHtml' => $this->service->buildParentOptionsHtml($nodesMeta, $selectedParentId),
        ]);
    }

    public function store(StoreMainMenuItemRequest $request): RedirectResponse
    {
        $this->service->createItem($request->validated());

        return redirect()
            ->route('admin.main-menu.index')
            ->with('success', __('Menu item created.'));
    }

    public function saveOrder(SaveMainMenuItemOrderRequest $request): JsonResponse
    {
        $this->service->saveOrder($request->validated('nodes'));

        return response()->json(['success' => true]);
    }

    public function update(UpdateMainMenuItemRequest $request, MainMenuItem $mainMenuItem): RedirectResponse
    {
        $mainMenuItem->update($request->validated());

        return redirect()
            ->route('admin.main-menu.index')
            ->with('success', __('Menu item updated.'));
    }

    public function destroy(Request $request, MainMenuItem $mainMenuItem): JsonResponse|RedirectResponse
    {
        $this->service->deleteNodeReparentingChildren($mainMenuItem);

        $message = __('Menu item deleted.');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.main-menu.index')
            ->with('success', $message);
    }
}
