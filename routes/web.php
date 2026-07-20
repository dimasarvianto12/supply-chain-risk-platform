<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryPageController;
use App\Http\Controllers\WeatherMapController;
use App\Http\Controllers\CurrencyDashboardController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortPageController;
use App\Http\Controllers\VisualizationController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PortController as AdminPortController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\FavoriteController as ApiFavoriteController;

// ================================================================
// 1. AUTHENTICATION (Login, Register, Logout)
// ================================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================================================================
// 2. FITUR UTAMA (Hanya bisa diakses jika sudah login)
// ================================================================
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/api/dashboard/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');

    Route::get('/countries', [CountryPageController::class, 'index'])->name('countries.index');
    Route::get('/api/country/{code}', [CountryPageController::class, 'detail'])->name('country.detail');

    Route::get('/weather-map', [WeatherMapController::class, 'index'])->name('weather.map');

    Route::get('/currency', [CurrencyDashboardController::class, 'index'])->name('currency.dashboard');

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');

    Route::get('/ports', [PortPageController::class, 'index'])->name('ports.index');

    Route::get('/visualization', [VisualizationController::class, 'index'])->name('visualization.index');

    Route::get('/decision', [DecisionController::class, 'index'])->name('decision.index');
    Route::post('/decision/analyze', [DecisionController::class, 'analyze'])->name('decision.analyze');

    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
});

// ================================================================
// 4. API FAVORIT (Dengan Middleware Auth)
// ================================================================
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/favorites', [ApiFavoriteController::class, 'index']);
    Route::post('/favorites/{country}', [ApiFavoriteController::class, 'store']);
    Route::delete('/favorites/{country}', [ApiFavoriteController::class, 'destroy']);
    Route::get('/favorites/check/{country}', [ApiFavoriteController::class, 'check']);
});

// ================================================================
// 5. ADMIN DASHBOARD (Hanya Admin)
// ================================================================
Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', AdminUserController::class);
        Route::resource('ports', AdminPortController::class);
        Route::resource('articles', AdminArticleController::class);
    });

// ================================================================
// 6. DEBUG (Hanya untuk Testing - Hapus jika sudah production)
// ================================================================
Route::get('/admin/debug', function () {
    return [
        'user' => auth()->user(),
        'is_admin' => auth()->user()->is_admin ?? 'null',
    ];
})->middleware(['auth', 'admin']);