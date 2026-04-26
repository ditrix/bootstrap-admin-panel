<?php

namespace App\Models;

use Database\Factories\MainMenuItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainMenuItem extends Model
{
    /** @use HasFactory<MainMenuItemFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'sort_no',
        'title',
        'slug',
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
     * @param  Builder<MainMenuItem>  $query
     * @return Builder<MainMenuItem>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_no')->orderBy('id');
    }

    public function getDepthFromRootAttribute(): int
    {
        $d = 1;
        $p = $this;
        while ((int) $p->parent_id !== 0) {
            $d++;
            if (! $p->relationLoaded('parent')) {
                $p->load('parent');
            }
            $next = $p->parent;
            if ($next === null) {
                break;
            }
            $p = $next;
        }

        return $d;
    }
}
