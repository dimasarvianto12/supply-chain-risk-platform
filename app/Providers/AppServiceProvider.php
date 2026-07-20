<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL; // <-- 1. Tambahkan import URL ini

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
        Paginator::useBootstrapFive();

        // <-- 2. Tambahkan baris ini untuk memaksa HTTPS di Railway
        if ($this->app->environment('production') || config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}