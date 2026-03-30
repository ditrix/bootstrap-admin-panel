<?php

namespace App\Helpers;

final class AdminHelper
{
    public static function maketAsset(string $path): string
    {
        return asset('maket/'.ltrim($path, '/'));
    }
}
