<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MainMenu\SaveMainMenuItemOrderRequest;
use App\Http\Requests\Admin\MainMenu\StoreMainMenuItemRequest;
use App\Http\Requests\Admin\MainMenu\UpdateMainMenuItemRequest;
use App\Models\MainMenuItem;
use App\Services\Admin\MainMenuItemService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Main menu tree (create item, reorder DnD, edit/destroy with reparenting).
 */
class MainMenuItemController extends Controller
{
    private const UPDATE_ROUTE_PLACEHOLDER_ID = 2147483646;

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

        $mainMenuEditorForJs = $nodesMeta
            ->mapWithKeys(static fn (MainMenuItem $n): array => [
                $n->id => [
                    'title' => $n->title,
                    'slug' => $n->slug,
                    'parent_id' => (int) $n->parent_id,
                    'is_active' => (bool) $n->is_active,
                ],
            ])
            ->all();

        $updateUrlTemplate = str_replace(
            (string) self::UPDATE_ROUTE_PLACEHOLDER_ID,
            '__ID__',
            route('admin.main-menu.update', ['main_menu_item' => self::UPDATE_ROUTE_PLACEHOLDER_ID]),
        );

        return view('admin.pages.main-menu.index', [
            'tree' => $tree,
            'nodesMeta' => $nodesMeta,
            'mainMenuMetaForJs' => $mainMenuMetaForJs,
            'mainMenuEditorForJs' => $mainMenuEditorForJs,
            'mainMenuUpdateUrlTemplate' => $updateUrlTemplate,
            'parentOptionsHtml' => $this->buildParentOptionsHtml($nodesMeta),
        ]);
    }

    /**
     * @param  EloquentCollection<int, MainMenuItem>  $nodesMeta
     */
    private function buildParentOptionsHtml(EloquentCollection $nodesMeta): string
    {
        $byParent = $nodesMeta
            ->map(fn (MainMenuItem $n): array => [
                'id' => $n->id,
                'parent_id' => (int) $n->parent_id,
                'title' => $n->title,
                'sort_no' => (int) $n->sort_no,
            ])
            ->groupBy('parent_id');

        $parts = ['<option value="0">'.e(__('Root')).'</option>'];
        $walk = function (int $parentId, int $depth) use (&$walk, &$parts, $byParent): void {
            $items = ($byParent->get($parentId) ?? collect())
                ->sortBy([
                    ['sort_no', 'asc'],
                    ['id', 'asc'],
                ]);
            foreach ($items as $n) {
                $indent = $depth > 0 ? str_repeat('— ', $depth).' ' : '';
                $label = $indent.e($n['title']);
                $parts[] = '<option value="'.(int) $n['id'].'">'.$label.'</option>';
                $walk((int) $n['id'], $depth + 1);
            }
        };
        $walk(0, 0);

        return implode('', $parts);
    }

    public function store(StoreMainMenuItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $parentId = (int) $data['parent_id'];
        $maxSort = (int) MainMenuItem::query()
            ->where('parent_id', $parentId)
            ->max('sort_no');

        MainMenuItem::query()->create(array_merge(
            $data,
            [
                'sort_no' => $maxSort + 1,
            ],
        ));

        return redirect()
            ->route('admin.main-menu.index')
            ->with('success', __('Menu item created.'));
    }

    public function saveOrder(SaveMainMenuItemOrderRequest $request): JsonResponse
    {
        $this->service->saveOrder($request->validated('nodes'));

        return response()->json(['success' => true]);
    }

    public function update(UpdateMainMenuItemRequest $request, MainMenuItem $mainMenuItem): JsonResponse|RedirectResponse
    {
        $mainMenuItem->update($request->validated());

        $message = __('Menu item updated.');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.main-menu.index')
            ->with('success', $message);
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
