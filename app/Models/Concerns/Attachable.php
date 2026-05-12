<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;

/**
 * Stored file path on a configurable public disk (single column per model).
 */
trait Attachable
{
    public static function attachmentColumn(): string
    {
        return 'image_path';
    }

    public static function attachmentDisk(): string
    {
        return 'public';
    }

    public static function attachmentDirectory(): string
    {
        return 'banners';
    }

    public function attachmentPublicUrl(): ?string
    {
        $column = static::attachmentColumn();
        $path = $this->{$column};
        if (! is_string($path) || $path === '') {
            return null;
        }

        if (str_contains($path, '..')) {
            return null;
        }

        $disk = static::attachmentDisk();
        if (! Storage::disk($disk)->exists($path)) {
            return null;
        }

        $normalized = str_replace('\\', '/', $path);

        return '/storage/'.$normalized;
    }

    public static function attachmentPlaceholderSourcePath(): string
    {
        return 'resources/themes/admin/assets/img/no-image.jpg';
    }

    /**
     * Resolves placeholder image URL via the Vite manifest (requires build or dev server).
     */
    public static function attachmentPlaceholderPublicUrl(): string
    {
        return Vite::asset(static::attachmentPlaceholderSourcePath());
    }

    public function thumbPublicUrl(): string
    {
        return $this->attachmentPublicUrl() ?? static::attachmentPlaceholderPublicUrl();
    }
}
