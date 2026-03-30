<?php

namespace Tests\Unit\Helpers;

use App\Helpers\SalaryHelper;
use PHPUnit\Framework\TestCase;

class SalaryHelperTest extends TestCase
{
    public function test_formats_usd_with_commas(): void
    {
        $this->assertSame('$320,800', SalaryHelper::formatUsd(320800));
        $this->assertSame('$1,234', SalaryHelper::formatUsd(1234));
    }
}
