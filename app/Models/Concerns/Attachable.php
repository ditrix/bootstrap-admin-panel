<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

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

        /** @var non-falsy-string $normalized */
        $normalized = str_replace('\\', '/', $path);

        return '/storage/'.$normalized;
    }

    public function thumbPublicUrl(): string
    {
        return $this->attachmentPublicUrl() ?? asset('admin/images/no-image.jpg');
    }
}
