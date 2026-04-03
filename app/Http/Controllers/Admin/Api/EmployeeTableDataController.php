<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Services\Admin\EmployeeListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeTableDataController extends Controller
{
    public function __construct(
        private EmployeeListingService $employeeListingService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->employeeListingService->paginateForBootstrapTable($request);

        return response()->json([
            'total' => $result['total'],
            'rows' => $result['rows']
                ->map(fn ($employee) => (new EmployeeResource($employee))->toArray($request))
                ->values()
                ->all(),
        ]);
    }
}
