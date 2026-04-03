<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\StaticPageResource;
use App\Services\Admin\StaticPageListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaticPageTableDataController extends Controller
{
    public function __construct(
        private StaticPageListingService $staticPageListingService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->staticPageListingService->paginateForBootstrapTable($request);

        return response()->json([
            'total' => $result['total'],
            'rows' => $result['rows']
                ->map(fn ($page) => (new StaticPageResource($page))->toArray($request))
                ->values()
                ->all(),
        ]);
    }
}
