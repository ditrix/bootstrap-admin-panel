<?php

use App\Http\Controllers\Admin\AdminEntryController;
use App\Http\Controllers\Admin\AdminErrorDemoController;
use App\Http\Controllers\Admin\Api\EmployeeIndexController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\BlankPageController;
use App\Http\Controllers\Admin\ChartsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormsController;
use App\Http\Controllers\Admin\Layout\LightSidenavController;
use App\Http\Controllers\Admin\Layout\StaticNavigationController;
use App\Http\Controllers\Admin\TablesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('adm')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', AdminEntryController::class)->name('entry');

        Route::middleware('guest:admin')->group(function (): void {
            Route::post('/login', [LoginController::class, 'store'])->name('login.store');
            Route::get('/register', [RegisterController::class, 'create'])->name('register');
            Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
            Route::get('/password', [PasswordResetLinkController::class, 'create'])->name('password.request');
            Route::post('/password', [PasswordResetLinkController::class, 'store'])->name('password.email');
            Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
            Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
        });

        Route::middleware('auth:admin')->group(function (): void {
            Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/layouts/static', StaticNavigationController::class)->name('layouts.static');
            Route::get('/layouts/sidenav-light', LightSidenavController::class)->name('layouts.sidenav-light');
            Route::get('/charts', [ChartsController::class, 'index'])->name('charts');
            Route::get('/tables', [TablesController::class, 'index'])->name('tables');
            Route::get('/forms', [FormsController::class, 'index'])->name('forms');
            Route::get('/blank', BlankPageController::class)->name('blank');
            Route::get('/errors/401', [AdminErrorDemoController::class, 'show401'])->name('errors.401');
            Route::get('/errors/404', [AdminErrorDemoController::class, 'show404'])->name('errors.404-demo');
            Route::get('/errors/500', [AdminErrorDemoController::class, 'show500'])->name('errors.500-demo');
            Route::get('/api/employees', EmployeeIndexController::class)->name('api.employees');
        });
    });
