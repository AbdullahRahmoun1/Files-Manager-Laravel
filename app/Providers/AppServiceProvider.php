<?php

namespace App\Providers;

use App\Models\FileHistory;
use App\Repositories\FileHistoryRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('firebase', function ($app) {
            return new \App\Services\FirebaseNotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
