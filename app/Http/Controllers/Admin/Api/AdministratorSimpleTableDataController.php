<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdministratorResource;
use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Full administrators list for a client-side bootstrap-table (no pagination / search / sort).
 */
class AdministratorSimpleTableDataController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $rows = Administrator::query()
            ->with('adminRole')
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        $payload = $rows->map(fn ($row) => (new AdministratorResource($row))->toArray($request))->values()->all();

        return response()->json($payload);
    }
}
