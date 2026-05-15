<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

/**
 * Full permissions list for a client-side bootstrap-table.
 */
class PermissionSimpleTableDataController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $rows = Permission::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get();

        $payload = $rows->map(fn ($row) => (new PermissionResource($row))->toArray($request))->values()->all();

        return response()->json($payload);
    }
}
