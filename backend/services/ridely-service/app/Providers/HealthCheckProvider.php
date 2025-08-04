<?php

namespace App\Providers;

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
        //
        Health::checks([
            UsedDiskSpaceCheck::new(),
            DatabaseCheck::new(),
            RedisCheck::new(),
            PingCheck::new()
                ->name('Keycloak Auth Service')
                ->url('http://localhost:8080/realms/master') // ajuste para o seu path
                ->timeout(2),
        ]);
    }
}
