<?php

use App\Authorization\AdminPermission;
use App\Http\Controllers\Admin\AdminEntryController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryTreeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MainMenuItemController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SeoRedirectController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\TablesController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', AdminEntryController::class)->name('entry');

        Route::middleware('guest:admin')->group(function (): void {
            Route::post('/login', [LoginController::class, 'store'])->name('login.store');

            Route::controller(RegisterController::class)->group(function (): void {
                Route::get('/register', 'create')->name('register');
                Route::post('/register', 'store')->name('register.store');
            });

            Route::controller(PasswordResetLinkController::class)->group(function (): void {
                Route::get('/password', 'create')->name('password.request');
                Route::post('/password', 'store')->name('password.email');
            });

            Route::controller(NewPasswordController::class)->group(function (): void {
                Route::get('/reset-password/{token}', 'create')->name('password.reset');
                Route::post('/reset-password', 'store')->name('password.update');
            });
        });

        Route::middleware(['auth:admin', 'use_admin_guard'])->group(function (): void {
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

            Route::middleware('permission:'.AdminPermission::DASHBOARD_VIEW)
                ->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::middleware('permission:'.AdminPermission::EMPLOYEES_MANAGE)
                ->resource('tables', TablesController::class)->only(['index']);

            Route::middleware('permission:'.AdminPermission::STATIC_PAGES_MANAGE)
                ->resource('static-pages', StaticPageController::class);

            Route::middleware('permission:'.AdminPermission::BANNERS_MANAGE)->group(function (): void {
                Route::delete('/banners/{banner}/image', [BannerController::class, 'destroyImage'])->name('banners.image.destroy');
                Route::resource('banners', BannerController::class)->except(['show']);
            });

            Route::middleware('permission:'.AdminPermission::CATEGORY_TREE_MANAGE)
                ->controller(CategoryTreeController::class)
                ->prefix('category-tree')
                ->name('category-tree.')
                ->group(function (): void {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::post('/save-order', 'saveOrder')->name('save-order');
                    Route::get('/{category_tree}/edit', 'edit')->name('edit');
                    Route::put('/{category_tree}', 'update')->name('update');
                    Route::delete('/{category_tree}', 'destroy')->name('destroy');
                });

            Route::middleware('permission:'.AdminPermission::MAIN_MENU_MANAGE)
                ->controller(MainMenuItemController::class)
                ->prefix('main-menu')
                ->name('main-menu.')
                ->group(function (): void {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::post('/save-order', 'saveOrder')->name('save-order');
                    Route::get('/{main_menu_item}/edit', 'edit')->name('edit');
                    Route::put('/{main_menu_item}', 'update')->name('update');
                    Route::delete('/{main_menu_item}', 'destroy')->name('destroy');
                });

            Route::middleware('permission:'.AdminPermission::SEO_REDIRECTS_MANAGE)
                ->resource('seo-redirects', SeoRedirectController::class)->except(['show']);

            Route::middleware('permission:'.AdminPermission::USERS_MANAGE)
                ->resource('administrators', AdministratorController::class)->except(['show']);

            Route::middleware('permission:'.AdminPermission::PERMISSIONS_VIEW)
                ->get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        });
    });
