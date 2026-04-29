<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryTree\SaveCategoryTreeOrderRequest;
use App\Http\Requests\Admin\CategoryTree\UpdateCategoryTreeRequest;
use App\Models\CategoryTree;
use App\Services\Admin\CategoryTreeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tree CRUD for categories (drag-and-drop order, inline JSON update/destroy).
 */
class CategoryTreeController extends Controller
{
    /**
     * Numeric placeholder for generating the client-side URL template (replaced with "__ID__" in JS).
     */
    private const UPDATE_ROUTE_PLACEHOLDER_TREE_ID = 2147483646;

    public function __construct(private readonly CategoryTreeService $service) {}

    public function index(): View
    {
        $tree = $this->service->buildGroupedTree();
        $nodesMeta = $this->service->allNodesOrderedForMeta();

        $categoryTreeMetaForJs = $nodesMeta
            ->map(static fn (CategoryTree $n): array => [
                'id' => $n->id,
                'parent_id' => (int) $n->parent_id,
                'title' => $n->title,
                'sort_no' => (int) $n->sort_no,
            ])
            ->values()
            ->all();

        $categoryTreeEditorForJs = $nodesMeta
            ->mapWithKeys(static fn (CategoryTree $n): array => [
                $n->id => [
                    'title' => $n->title,
                    'slug' => $n->slug,
                    'description' => $n->description,
                    'parent_id' => (int) $n->parent_id,
                    'is_active' => (bool) $n->is_active,
                ],
            ])
            ->all();

        $updateUrlTemplate = str_replace(
            (string) self::UPDATE_ROUTE_PLACEHOLDER_TREE_ID,
            '__ID__',
            route('admin.category-tree.update', ['category_tree' => self::UPDATE_ROUTE_PLACEHOLDER_TREE_ID]),
        );

        return view('admin.pages.category-tree.index', [
            'tree' => $tree,
            'nodesMeta' => $nodesMeta,
            'categoryTreeMetaForJs' => $categoryTreeMetaForJs,
            'categoryTreeEditorForJs' => $categoryTreeEditorForJs,
            'categoryTreeUpdateUrlTemplate' => $updateUrlTemplate,
        ]);
    }

    public function saveOrder(SaveCategoryTreeOrderRequest $request): JsonResponse
    {
        $this->service->saveOrder($request->validated('nodes'));

        return response()->json(['success' => true]);
    }

    public function update(UpdateCategoryTreeRequest $request, CategoryTree $categoryTree): JsonResponse|RedirectResponse
    {
        $categoryTree->update($request->validated());

        $message = __('Category tree node updated.');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.category-tree.index')
            ->with('success', $message);
    }

    public function destroy(Request $request, CategoryTree $categoryTree): JsonResponse|RedirectResponse
    {
        $this->service->deleteNodeReparentingChildren($categoryTree);

        $message = __('Category tree node deleted.');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('admin.category-tree.index')
            ->with('success', $message);
    }
}
