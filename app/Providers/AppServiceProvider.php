<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

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
    {   // Personalizar la URL de restablecimiento de contraseña
         ResetPassword::createUrlUsing(function ($user, string $token) {
        // Esto construye el link que llegará al correo del usuario
        return env('FRONTEND_URL').'/#/home/reset-password?token='.$token.'&email='.$user->email;
    });
    }
}
