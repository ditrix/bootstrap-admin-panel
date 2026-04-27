<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SeoRedirectResource;
use App\Services\Admin\SeoRedirectListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoRedirectTableDataController extends Controller
{
    public function __construct(
        private SeoRedirectListingService $seoRedirectListingService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->seoRedirectListingService->paginateForBootstrapTable($request);

        return response()->json([
            'total' => $result['total'],
            'rows' => $result['rows']
                ->map(fn ($row) => (new SeoRedirectResource($row))->toArray($request))
                ->values()
                ->all(),
        ]);
    }
}
