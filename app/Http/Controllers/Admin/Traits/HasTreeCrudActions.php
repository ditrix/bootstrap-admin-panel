<?php

namespace App\Http\Controllers\Admin\Traits;

use App\Http\Requests\Admin\AbstractSaveTreeOrderRequest;
use App\Services\Admin\AbstractTreeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Shared HTTP actions for tree CRUD admin modules.
 *
 * Provides fully generic implementations of index(), create() and saveOrder().
 * Methods edit(), store(), update() and destroy() require a concrete public method
 * in the controller for proper route model binding; the controller delegates to
 * the protected handle*() helpers defined here.
 *
 * Required in the consuming class:
 *  - abstract getTreeService(): AbstractTreeService
 *  - abstract indexView(): string
 *  - abstract createView(): string
 *  - abstract editView(): string
 *  - abstract editModelKey(): string      key used in edit view data (e.g. 'categoryTree')
 *  - abstract jsMetaKey(): string         JS meta variable name (e.g. 'categoryTreeMetaForJs')
 *  - abstract saveOrderRouteName(): string
 *  - abstract indexRouteName(): string
 *  - abstract storeSuccessMessage(): string
 *  - abstract updateSuccessMessage(): string
 *  - abstract destroySuccessMessage(): string
 */
trait HasTreeCrudActions
{
    abstract protected function getTreeService(): AbstractTreeService;

    abstract protected function indexView(): string;

    abstract protected function createView(): string;

    abstract protected function editView(): string;

    /** Key used to pass the model to the edit view (e.g. 'categoryTree', 'mainMenuItem'). */
    abstract protected function editModelKey(): string;

    /** View data key for the JS metadata array (e.g. 'categoryTreeMetaForJs'). */
    abstract protected function jsMetaKey(): string;

    abstract protected function saveOrderRouteName(): string;

    abstract protected function indexRouteName(): string;

    abstract protected function storeSuccessMessage(): string;

    abstract protected function updateSuccessMessage(): string;

    abstract protected function destroySuccessMessage(): string;

    // -------------------------------------------------------------------------
    // Fully generic public actions (no model binding needed)
    // -------------------------------------------------------------------------

    public function index(): View
    {
        $service = $this->getTreeService();
        $tree = $service->buildGroupedTree();
        $nodesMeta = $service->allNodesOrderedForMeta();

        $metaForJs = $nodesMeta
            ->map(static fn ($n): array => [
                'id' => $n->id,
                'parent_id' => (int) $n->parent_id,
                'title' => $n->title,
                'sort_no' => (int) $n->sort_no,
            ])
            ->values()
            ->all();

        return view($this->indexView(), [
            'tree' => $tree,
            $this->jsMetaKey() => $metaForJs,
            'saveOrderUrl' => route($this->saveOrderRouteName()),
        ]);
    }

    public function create(): View
    {
        $service = $this->getTreeService();
        $nodesMeta = $service->allNodesOrderedForMeta();
        $selectedParentId = (int) (request()->old('parent_id') ?? 0);

        return view($this->createView(), [
            'parentOptionsHtml' => $service->buildParentOptionsHtml($nodesMeta, $selectedParentId),
        ]);
    }

    /**
     * Execute the save-order logic.
     *
     * Controllers must define a public saveOrder() with the concrete Request type for
     * proper route model binding, then delegate here:
     *   public function saveOrder(SaveXxxOrderRequest $r): JsonResponse { return $this->executeSaveOrder($r); }
     */
    protected function executeSaveOrder(AbstractSaveTreeOrderRequest $request): JsonResponse
    {
        $this->getTreeService()->saveOrder($request->validated('nodes'));

        return response()->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // Protected helpers — called by concrete controller methods that own
    // the typed route model binding signature.
    // -------------------------------------------------------------------------

    protected function handleEdit(Model $node): View
    {
        $service = $this->getTreeService();
        $nodesMeta = $service->allNodesOrderedForMeta();
        $selectedParentId = (int) (request()->old('parent_id') ?? $node->parent_id);

        return view($this->editView(), [
            $this->editModelKey() => $node,
            'parentOptionsHtml' => $service->buildParentOptionsHtml($nodesMeta, $selectedParentId),
        ]);
    }

    protected function handleStore(FormRequest $request): RedirectResponse
    {
        $this->getTreeService()->createItem($request->validated());

        return redirect()
            ->route($this->indexRouteName())
            ->with('success', $this->storeSuccessMessage());
    }

    protected function handleUpdate(FormRequest $request, Model $node): RedirectResponse
    {
        $node->update($request->validated());

        return redirect()
            ->route($this->indexRouteName())
            ->with('success', $this->updateSuccessMessage());
    }

    protected function handleDestroy(Request $request, Model $node): JsonResponse|RedirectResponse
    {
        $this->getTreeService()->deleteNodeReparentingChildren($node);

        $message = $this->destroySuccessMessage();

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route($this->indexRouteName())
            ->with('success', $message);
    }
}
