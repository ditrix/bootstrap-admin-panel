<?php

namespace App\Models;

use Database\Factories\StaticPageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CMS static page row with slugs and ordering.
 */
class StaticPage extends Model
{
    /** @use HasFactory<StaticPageFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
        'content',
        'sort_no',
        'slug',
        'is_active',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @param  Builder<StaticPage>  $query
     * @return Builder<StaticPage>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_no')->orderBy('id');
    }
}
