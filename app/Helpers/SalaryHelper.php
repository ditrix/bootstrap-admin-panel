<?php

namespace App\Helpers;

/**
 * Salary display helpers for demos and tables.
 */
final class SalaryHelper
{
    /**
     * Format a numeric amount as USD with thousands separators (no fractional part).
     */
    public static function formatUsd(float|string|int $amount): string
    {
        return '$'.number_format((float) $amount, 0, '.', ',');
    }
}
