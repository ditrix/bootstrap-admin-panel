<?php

namespace App\Services\Admin;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaticPageListingService
{
    /**
     * @var list<string>
     */
    private const SORTABLE = ['id', 'parent_id', 'code', 'title', 'sort_no', 'slug', 'is_active', 'created_at', 'updated_at'];

    /**
     * @return array{total: int, rows: Collection<int, StaticPage>}
     */
    public function paginateForBootstrapTable(Request $request): array
    {
        $limit = min(max((int) $request->input('limit', 10), 1), 100);
        $offset = max((int) $request->input('offset', 0), 0);
        $search = (string) $request->input('search', '');
        $sort = $request->input('sort');
        $order = strtolower((string) $request->input('order', 'asc')) === 'desc' ? 'desc' : 'asc';

        $query = StaticPage::query();

        if ($search !== '') {
            $this->applySearch($query, $search);
        }

        if (is_string($sort) && in_array($sort, self::SORTABLE, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->ordered();
        }

        $total = (clone $query)->count();
        $rows = $query->skip($offset)->take($limit)->get();

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }

    /**
     * @param  Builder<StaticPage>  $query
     */
    private function applySearch(Builder $query, string $search): void
    {
        $trimmed = trim($search);
        if ($trimmed === '') {
            return;
        }

        $like = '%'.addcslashes($trimmed, '%_\\').'%';
        $cast = $this->stringCastType($query);

        $query->where(function (Builder $q) use ($like, $cast, $trimmed) {
            $q->where('title', 'like', $like)
                ->orWhere('code', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhereRaw("CAST(id AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(parent_id AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(sort_no AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(is_active AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(created_at AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(updated_at AS {$cast}) LIKE ?", [$like]);

            if (Str::isAscii($trimmed) && ctype_digit($trimmed)) {
                $int = (int) $trimmed;
                $q->orWhere('id', $int)
                    ->orWhere('parent_id', $int)
                    ->orWhere('sort_no', $int);
            }
        });
    }

    /**
     * @param  Builder<StaticPage>  $query
     */
    private function stringCastType(Builder $query): string
    {
        return $query->getConnection()->getDriverName() === 'sqlite' ? 'TEXT' : 'CHAR';
    }
}
