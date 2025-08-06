<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthCheckProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $keycloakUrl = config('keycloak.health_check_url');
//        Log::debug("Keycloak url: $keycloakUrl");

        // TODO adicionar o token
        Health::checks([
            UsedDiskSpaceCheck::new(),
            DatabaseCheck::new()->name("Database")->connectionName(""),
            RedisCheck::new()->name("Cache")->connectionName(""),
            PingCheck::new()
                ->name('Keycloak Auth Service')
                ->url($keycloakUrl)
                ->timeout(2),
        ]);
    }
}
