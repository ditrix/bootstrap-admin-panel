<?php

namespace App\Http\Resources\Admin;

use App\Helpers\SalaryHelper;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON row for admin employees table (formatted salary and dates).
 *
 * @mixin Employee
 */
class EmployeeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'office' => $this->office,
            'age' => $this->age,
            'start_date' => $this->start_date?->format('Y/m/d'),
            'salary' => SalaryHelper::formatUsd($this->salary),
            'created_at' => $this->created_at?->format('d.m.Y'),
            'updated_at' => $this->updated_at?->format('d.m.Y'),
        ];
    }
}
