<?php

namespace App\Helpers;

final class SalaryHelper
{
    public static function formatUsd(float|string|int $amount): string
    {
        return '$'.number_format((float) $amount, 0, '.', ',');
    }
}
