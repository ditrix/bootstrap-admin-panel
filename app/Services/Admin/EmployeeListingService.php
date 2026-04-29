<?php

namespace App\Services\Admin;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Employee listing for legacy DataTables and bootstrap-table JSON APIs (search, sort, page).
 */
class EmployeeListingService
{
    /**
     * @var list<string>
     */
    private const SORTABLE = ['id', 'name', 'position', 'office', 'age', 'start_date', 'salary', 'created_at', 'updated_at'];

    /**
     * @return Collection<int, Employee>
     */
    public function orderedForDataTable(): Collection
    {
        return Employee::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{total: int, rows: Collection<int, Employee>}
     */
    public function paginateForBootstrapTable(Request $request): array
    {
        $limit = min(max((int) $request->input('limit', 10), 1), 100);
        $offset = max((int) $request->input('offset', 0), 0);
        $search = (string) $request->input('search', '');
        $sort = $request->input('sort');
        $order = strtolower((string) $request->input('order', 'asc')) === 'desc' ? 'desc' : 'asc';

        $query = Employee::query();

        if ($search !== '') {
            $this->applySearch($query, $search);
        }

        if (is_string($sort) && in_array($sort, self::SORTABLE, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('name');
        }

        $total = (clone $query)->count();
        $rows = $query->skip($offset)->take($limit)->get();

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }

    /**
     * @param  Builder<Employee>  $query
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
            $q->where('name', 'like', $like)
                ->orWhere('position', 'like', $like)
                ->orWhere('office', 'like', $like)
                ->orWhereRaw("CAST(id AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(age AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(salary AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(start_date AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(created_at AS {$cast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(updated_at AS {$cast}) LIKE ?", [$like]);

            if (Str::isAscii($trimmed) && is_numeric($trimmed)) {
                if (ctype_digit($trimmed)) {
                    $int = (int) $trimmed;
                    $q->orWhere('id', $int)->orWhere('age', $int);
                }
                $q->orWhere('salary', $trimmed);
            }
        });
    }

    /**
     * @param  Builder<Employee>  $query
     */
    private function stringCastType(Builder $query): string
    {
        return $query->getConnection()->getDriverName() === 'sqlite' ? 'TEXT' : 'CHAR';
    }
}
