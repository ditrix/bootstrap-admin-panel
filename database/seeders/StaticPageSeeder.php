<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        $roots = StaticPage::factory()
            ->count(12)
            ->create();

        foreach ($roots->random(min(5, $roots->count())) as $parent) {
            StaticPage::factory()
                ->count(2)
                ->childOf($parent)
                ->create();
        }
    }
}
