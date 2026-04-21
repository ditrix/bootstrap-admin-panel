<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryTree\SaveCategoryTreeOrderRequest;
use App\Services\Admin\CategoryTreeService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CategoryTreeController extends Controller
{
    public function __construct(private readonly CategoryTreeService $service) {}

    public function index(): View
    {
        $tree = $this->service->buildGroupedTree();

        return view('admin.pages.category-tree.index', [
            'tree' => $tree,
        ]);
    }

    public function saveOrder(SaveCategoryTreeOrderRequest $request): JsonResponse
    {
        $this->service->saveOrder($request->validated('nodes'));

        return response()->json(['success' => true]);
    }
}
