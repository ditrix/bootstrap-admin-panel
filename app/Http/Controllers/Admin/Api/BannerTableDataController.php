<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BannerResource;
use App\Services\Admin\BannerListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON endpoint for banners consumed by bootstrap-table (total + rows).
 */
class BannerTableDataController extends Controller
{
    public function __construct(
        private BannerListingService $bannerListingService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->bannerListingService->paginateForBootstrapTable($request);

        return response()->json([
            'total' => $result['total'],
            'rows' => $result['rows']
                ->map(fn ($banner) => (new BannerResource($banner))->toArray($request))
                ->values()
                ->all(),
        ]);
    }
}
