<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Services\Admin\EmployeeListingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeIndexController extends Controller
{
    public function __construct(
        private EmployeeListingService $employeeListingService,
    ) {}

    public function __invoke(): AnonymousResourceCollection
    {
        return EmployeeResource::collection($this->employeeListingService->orderedForDataTable());
    }
}
