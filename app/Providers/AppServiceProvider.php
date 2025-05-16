<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Providers\EncryptedUserProvider;

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
        // Register custom authentication provider for encrypted emails
        Auth::provider('encrypted', function ($app, array $config) {
            return new EncryptedUserProvider($app['hash'], $config['model']);
        });
    }
}
