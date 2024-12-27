<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            //registers all route files inside api folder
            foreach (glob(base_path('routes/api/*.php')) as $file) {
                Route::group(['middleware' => ['transactional']], function () use ($file) {
                    Route::prefix('api')
                        ->middleware('api')
                        ->namespace($this->namespace)
                        ->group($file);
                });
            }
            // Add here any new route file
            //
            //
            //


            // Route::middleware('web')
            //     ->group(base_path('routes/web.php'));
        });
    }
}
