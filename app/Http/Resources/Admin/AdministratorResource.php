<?php

namespace App\Http\Resources\Admin;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON row for administrators bootstrap-table (no secrets exposed).
 *
 * @mixin Administrator
 */
class AdministratorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'role_name' => $this->adminRole?->name,
            'created_at' => $this->created_at?->format('d.m.y'),
            'updated_at' => $this->updated_at?->format('d.m.y'),
        ];
    }
}
