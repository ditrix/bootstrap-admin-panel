<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryTree\SaveCategoryTreeOrderRequest;
use App\Http\Requests\Admin\CategoryTree\StoreCategoryTreeRequest;
use App\Http\Requests\Admin\CategoryTree\UpdateCategoryTreeRequest;
use App\Models\CategoryTree;
use App\Services\Admin\CategoryTreeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tree CRUD for categories (drag-and-drop order, page-based create/edit/update/destroy).
 */
class CategoryTreeController extends Controller
{
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

        return view('admin.pages.category-tree.index', [
            'tree' => $tree,
            'categoryTreeMetaForJs' => $categoryTreeMetaForJs,
            'saveOrderUrl' => route('admin.category-tree.save-order'),
        ]);
    }

    public function create(): View
    {
        $nodesMeta = $this->service->allNodesOrderedForMeta();
        $selectedParentId = (int) (request()->old('parent_id') ?? 0);

        return view('admin.pages.category-tree.create', [
            'parentOptionsHtml' => $this->service->buildParentOptionsHtml($nodesMeta, $selectedParentId),
        ]);
    }

    public function edit(CategoryTree $categoryTree): View
    {
        $nodesMeta = $this->service->allNodesOrderedForMeta();
        $selectedParentId = (int) (request()->old('parent_id') ?? $categoryTree->parent_id);

        return view('admin.pages.category-tree.edit', [
            'categoryTree' => $categoryTree,
            'parentOptionsHtml' => $this->service->buildParentOptionsHtml($nodesMeta, $selectedParentId),
        ]);
    }

    public function store(StoreCategoryTreeRequest $request): RedirectResponse
    {
        $this->service->createItem($request->validated());

        return redirect()
            ->route('admin.category-tree.index')
            ->with('success', __('Category node created.'));
    }

    public function saveOrder(SaveCategoryTreeOrderRequest $request): JsonResponse
    {
        $this->service->saveOrder($request->validated('nodes'));

        return response()->json(['success' => true]);
    }

    public function update(UpdateCategoryTreeRequest $request, CategoryTree $categoryTree): RedirectResponse
    {
        $categoryTree->update($request->validated());

        return redirect()
            ->route('admin.category-tree.index')
            ->with('success', __('Category tree node updated.'));
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
