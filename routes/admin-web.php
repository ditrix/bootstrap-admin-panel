<?php

use App\Http\Controllers\Admin\AdminEntryController;
use App\Http\Controllers\Admin\AdminErrorDemoController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\BlankPageController;
use App\Http\Controllers\Admin\CategoryTreeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormsController;
use App\Http\Controllers\Admin\Layout\LightSidenavController;
use App\Http\Controllers\Admin\Layout\StaticNavigationController;
use App\Http\Controllers\Admin\MainMenuItemController;
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

        Route::middleware('auth:admin')->group(function (): void {
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::controller(StaticNavigationController::class)->group(function (): void {
                Route::get('/layouts/static', '__invoke')->name('layouts.static');
            });

            Route::controller(LightSidenavController::class)->group(function (): void {
                Route::get('/layouts/sidenav-light', '__invoke')->name('layouts.sidenav-light');
            });

            Route::controller(TablesController::class)->group(function (): void {
                Route::get('/tables', 'index')->name('tables');
            });

            Route::controller(FormsController::class)->group(function (): void {
                Route::get('/forms', 'index')->name('forms');
            });

            Route::get('/blank', BlankPageController::class)->name('blank');

            Route::controller(AdminErrorDemoController::class)
                ->prefix('errors')
                ->name('errors.')
                ->group(function (): void {
                    Route::get('/401', 'show401')->name('401');
                    Route::get('/404', 'show404')->name('404-demo');
                    Route::get('/500', 'show500')->name('500-demo');
                });

            Route::resource('static-pages', StaticPageController::class);

            Route::controller(CategoryTreeController::class)
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

            Route::controller(MainMenuItemController::class)
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

            Route::resource('seo-redirects', SeoRedirectController::class)->except(['show']);

            Route::resource('administrators', AdministratorController::class)->except(['show']);
        });
    });
