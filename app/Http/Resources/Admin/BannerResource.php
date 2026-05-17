<?php

namespace App\Http\Resources\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON row for banners bootstrap-table.
 *
 * @mixin Banner
 */
class BannerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'preview_url' => $this->resource->thumbPublicUrl(),
            'sort_no' => $this->sort_no,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('d.m.Y'),
            'updated_at' => $this->updated_at?->format('d.m.Y'),
        ];
    }
}
