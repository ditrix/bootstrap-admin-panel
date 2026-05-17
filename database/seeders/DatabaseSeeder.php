<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $this->call(AdminSeeder::class);

        $this->call(StaticPageSeeder::class);

        $this->call(BannerSeeder::class);

        $this->call(CategoryTreeSeeder::class);

        $this->call(ConfigurationModuleSeeder::class);

        Employee::factory(50)->create();
    }
}
