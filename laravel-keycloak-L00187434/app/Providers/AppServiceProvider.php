<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\KeycloakSocialiteProvider;

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
    public function boot()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('keycloak', function ($app) use ($socialite) {
            return $socialite->buildProvider(
                KeycloakSocialiteProvider::class,  // Fully qualified name
                $app['config']['services.keycloak']
            );
        });
    }

}
