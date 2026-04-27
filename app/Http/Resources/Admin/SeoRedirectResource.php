<?php

namespace App\Http\Resources\Admin;

use App\Models\SeoRedirect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SeoRedirect
 */
class SeoRedirectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug_from' => $this->slug_from,
            'slug_to' => $this->slug_to,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('d.m.Y'),
            'updated_at' => $this->updated_at?->format('d.m.Y'),
        ];
    }
}
