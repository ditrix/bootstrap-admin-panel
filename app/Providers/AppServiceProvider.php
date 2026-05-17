<?php

namespace App\Providers;

use App\View\Composers\AdminLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Application-wide bootstrapping: view composers, shared config, etc.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(
            [
                'admin.layouts.sb-admin',
                'admin.pages.*',
            ],
            AdminLayoutComposer::class
        );
    }
}
