<?php

namespace App\Models;

use App\Models\Concerns\Attachable;
use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Admin-managed banner with image attachment.
 */
class Banner extends Model
{
    use Attachable;

    /** @use HasFactory<BannerFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'sort_no',
        'is_active',
        'image_path',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @param  Builder<Banner>  $query
     * @return Builder<Banner>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_no')->orderBy('id');
    }
}
