<?php

namespace App\Services\Admin;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Persists and removes banner image files on the public disk.
 */
class BannerAttachmentService
{
    public function storeUploadedImage(Banner $banner, UploadedFile $file): void
    {
        $column = Banner::attachmentColumn();
        $this->deletePathIfAny($banner->{$column});

        $relative = $file->store(Banner::attachmentDirectory(), Banner::attachmentDisk());
        $banner->forceFill([$column => $relative])->save();
    }

    public function clearStoredImage(Banner $banner): void
    {
        $column = Banner::attachmentColumn();
        $current = $banner->{$column};
        $this->deletePathIfAny($current);

        $banner->forceFill([$column => null])->save();
    }

    private function deletePathIfAny(mixed $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }
        $disk = Banner::attachmentDisk();
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
