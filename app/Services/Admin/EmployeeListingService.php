<?php

namespace App\Services\Admin;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class EmployeeListingService
{
    /**
     * @return Collection<int, Employee>
     */
    public function orderedForDataTable(): Collection
    {
        return Employee::query()
            ->orderBy('name')
            ->get();
    }
}
