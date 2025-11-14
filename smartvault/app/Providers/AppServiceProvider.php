<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        // Update last_login_at when user logs in (works for both regular and 2FA logins)
        Event::listen(Login::class, function (Login $event) {
            if ($event->user) {
                $event->user->forceFill(['last_login_at' => now()])->save();
            }
        });
    }
}
