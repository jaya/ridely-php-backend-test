<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Http\Middleware\ValidateKeycloakJwt;
use App\Services\Facades\DriverManagerFacade;
use App\Services\JWTKeysService;
use GuzzleHttp\Client;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //

    }

    public function register(): void
    {
        $this->app->singleton(JWTKeysService::class, function ($app) {
         return new JWTKeysService($app->make(Client::class));
        });
        $this->app->singleton(ValidateKeycloakJwt::class, function ($app) {
            return new ValidateKeycloakJwt($app->make(JWTKeysService::class));
        });
    }
}
