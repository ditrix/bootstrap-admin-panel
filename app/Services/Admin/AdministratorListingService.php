<?php

namespace App\Services\Admin;

use App\Helpers\BootstrapTableHelper;
use App\Models\Administrator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Administrator accounts listing for bootstrap-table (search, sort, pagination).
 */
class AdministratorListingService
{
    /**
     * @var list<string>
     */
    private const SORTABLE = ['id', 'name', 'email', 'is_active', 'created_at', 'updated_at'];

    /**
     * @return array{total: int, rows: Collection<int, Administrator>}
     */
    public function paginateForBootstrapTable(Request $request): array
    {
        ['limit' => $limit, 'offset' => $offset, 'search' => $search, 'sort' => $sort, 'order' => $order]
            = BootstrapTableHelper::parsePaginationParams($request);

        $query = Administrator::query();

        if ($search !== '') {
            $this->applySearch($query, $search);
        }

        if (is_string($sort) && in_array($sort, self::SORTABLE, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('name')->orderBy('id');
        }

        $total = (clone $query)->count();
        $rows = $query->skip($offset)->take($limit)->get();

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }

    /**
     * @param  Builder<Administrator>  $query
     */
    private function applySearch(Builder $query, string $search): void
    {
        $trimmed = trim($search);
        if ($trimmed === '') {
            return;
        }

        $like = '%'.addcslashes($trimmed, '%_\\').'%';
        $cast = BootstrapTableHelper::stringCastType($query);

        $query->where(function (Builder $q) use ($like, $cast, $trimmed) {
            $q->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhereRaw("CAST(id AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(is_active AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(created_at AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(updated_at AS {$cast}) LIKE ?", [$like]);

            if (Str::isAscii($trimmed) && ctype_digit($trimmed)) {
                $int = (int) $trimmed;
                $q->orWhere('id', $int);
            }
        });
    }
}
