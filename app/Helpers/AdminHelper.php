<?php

namespace App\Helpers;

/**
 * Admin theme asset helpers (e.g. inline SVG data URIs for error pages).
 */
final class AdminHelper
{
    /**
     * @param  string  $relativePath  Path relative to resources/themes/admin/assets/ (e.g. img/error-404-monochrome.svg)
     */
    public static function themeAssetDataUri(string $relativePath): string
    {
        $fullPath = resource_path('themes/admin/assets/'.ltrim($relativePath, '/'));

        if (! is_file($fullPath)) {
            return '';
        }

        $contents = file_get_contents($fullPath);
        if ($contents === false) {
            return '';
        }

        $mime = str_ends_with(strtolower($fullPath), '.svg')
            ? 'image/svg+xml'
            : 'application/octet-stream';

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}
