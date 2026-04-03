<?php

namespace App\Http\Resources\Admin;

use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StaticPage
 */
class StaticPageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'sort_no' => $this->sort_no,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('d.m.Y'),
            'updated_at' => $this->updated_at?->format('d.m.Y'),
        ];
    }
}
