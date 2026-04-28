<?php

use App\Http\Controllers\Admin\Api\AdministratorTableDataController;
use App\Http\Controllers\Admin\Api\EmployeeTableDataController;
use App\Http\Controllers\Admin\Api\SeoRedirectTableDataController;
use App\Http\Controllers\Admin\Api\StaticPageTableDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function (): void {
        Route::prefix('api')
            ->name('api.')
            ->group(function (): void {
                Route::get('/employees', EmployeeTableDataController::class)->name('employees');
                Route::get('/static-pages/table', StaticPageTableDataController::class)->name('static-pages.table');
                Route::get('/administrators/table', AdministratorTableDataController::class)->name('administrators.table');
                Route::get('/seo-redirects/table', SeoRedirectTableDataController::class)->name('seo-redirects.table');
            });
    });
