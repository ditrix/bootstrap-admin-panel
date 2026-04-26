<?php

namespace App\Models;

use Database\Factories\CategoryTreeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryTree extends Model
{
    /** @use HasFactory<CategoryTreeFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'sort_no',
        'title',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->orderBy('sort_no');
    }

    /**
     * @param  Builder<CategoryTree>  $query
     * @return Builder<CategoryTree>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_no')->orderBy('id');
    }
}
