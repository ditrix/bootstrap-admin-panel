<?php

use App\Authorization\AdminPermission;
use App\Http\Controllers\Admin\Api\AdministratorSimpleTableDataController;
use App\Http\Controllers\Admin\Api\AdministratorTableDataController;
use App\Http\Controllers\Admin\Api\BannerTableDataController;
use App\Http\Controllers\Admin\Api\EmployeeTableDataController;
use App\Http\Controllers\Admin\Api\PermissionSimpleTableDataController;
use App\Http\Controllers\Admin\Api\SeoRedirectTableDataController;
use App\Http\Controllers\Admin\Api\StaticPageTableDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', 'use_admin_guard'])
    ->group(function (): void {
        Route::prefix('api')
            ->name('api.')
            ->group(function (): void {
                Route::middleware('permission:'.AdminPermission::EMPLOYEES_MANAGE)
                    ->get('/employees', EmployeeTableDataController::class)->name('employees');

                Route::middleware('permission:'.AdminPermission::STATIC_PAGES_MANAGE)
                    ->get('/static-pages/table', StaticPageTableDataController::class)->name('static-pages.table');

                Route::middleware('permission:'.AdminPermission::BANNERS_MANAGE)
                    ->get('/banners/table', BannerTableDataController::class)->name('banners.table');

                Route::middleware('permission:'.AdminPermission::USERS_MANAGE)
                    ->group(function (): void {
                        Route::get('/administrators/table', AdministratorTableDataController::class)->name('administrators.table');
                        Route::get('/administrators/simple', AdministratorSimpleTableDataController::class)->name('administrators.simple');
                    });

                Route::middleware('permission:'.AdminPermission::SEO_REDIRECTS_MANAGE)
                    ->get('/seo-redirects/table', SeoRedirectTableDataController::class)->name('seo-redirects.table');

                Route::middleware('permission:'.AdminPermission::PERMISSIONS_VIEW)
                    ->get('/permissions/simple', PermissionSimpleTableDataController::class)->name('permissions.simple');
            });
    });
