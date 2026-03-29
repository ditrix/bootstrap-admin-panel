<?php

namespace Tests\Unit\Services;

use App\Models\Employee;
use App\Services\Admin\AdminDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_cards_contains_four_entries(): void
    {
        Employee::factory()->count(3)->create();

        $service = new AdminDashboardService;
        $cards = $service->summaryCards();

        $this->assertCount(4, $cards);
        $this->assertSame('Employees', $cards[2]['label']);
    }
}
