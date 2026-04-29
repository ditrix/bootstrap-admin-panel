<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Shared utilities for bootstrap-table server-side listing services.
 */
final class BootstrapTableHelper
{
    /**
     * Parse the standard bootstrap-table pagination/sort/search query params.
     *
     * @return array{limit: int, offset: int, search: string, sort: string|null, order: string}
     */
    public static function parsePaginationParams(Request $request): array
    {
        return [
            'limit' => min(max((int) $request->input('limit', 10), 1), 100),
            'offset' => max((int) $request->input('offset', 0), 0),
            'search' => (string) $request->input('search', ''),
            'sort' => $request->input('sort'),
            'order' => strtolower((string) $request->input('order', 'asc')) === 'desc' ? 'desc' : 'asc',
        ];
    }

    /**
     * Return the SQL CAST type for string-based LIKE searches on the current DB driver.
     *
     * @param  Builder<Model>  $query
     */
    public static function stringCastType(Builder $query): string
    {
        return $query->getConnection()->getDriverName() === 'sqlite' ? 'TEXT' : 'CHAR';
    }
}
