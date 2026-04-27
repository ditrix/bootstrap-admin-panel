<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdministratorResource;
use App\Services\Admin\AdministratorListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdministratorTableDataController extends Controller
{
    public function __construct(
        private AdministratorListingService $administratorListingService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->administratorListingService->paginateForBootstrapTable($request);

        return response()->json([
            'total' => $result['total'],
            'rows' => $result['rows']
                ->map(fn ($row) => (new AdministratorResource($row))->toArray($request))
                ->values()
                ->all(),
        ]);
    }
}
