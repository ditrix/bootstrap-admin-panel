<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\MainMenuItem;
use App\Models\SeoRedirect;
use Illuminate\Database\Seeder;

class ConfigurationModuleSeeder extends Seeder
{
    /**
     * Сидер модулей из задачи 11: Main menu, 301 Redirects, Administrators.
     * По 10 демо-записей на модуль (фабрики).
     */
    public function run(): void
    {
        MainMenuItem::factory(10)->create();

        SeoRedirect::factory(10)->create();

        Administrator::factory(10)->create();
    }
}
