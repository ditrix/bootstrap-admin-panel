<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::factory()->create([
            'code' => 'demo_banner_primary',
            'title' => 'Demo banner — primary slot',
            'sort_no' => 0,
            'is_active' => true,
            'image_path' => null,
        ]);

        Banner::factory()->create([
            'code' => 'demo_banner_secondary',
            'title' => 'Demo banner — secondary slot',
            'sort_no' => 1,
            'is_active' => true,
            'image_path' => null,
        ]);
    }
}
