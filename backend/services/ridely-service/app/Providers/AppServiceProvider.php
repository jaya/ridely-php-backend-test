<?php

namespace App\Providers;

use App\Repositories\V1\DriverRepository;
use App\Services\DriverManagerFacade;
use App\Services\Interfaces\Driver\CreateDriver;
use App\Services\Interfaces\Driver\ReadDriver;
use App\Services\V1\Driver\CreateDriverService as CreateDriverServiceV1;
use App\Services\V1\Driver\ReadDriverService;
use App\Services\V2\Driver\CreateDriverService as CreateDriverServiceV2;
use App\Validator\DriverValidator;
use Illuminate\Support\ServiceProvider;
use L5Swagger\L5SwaggerServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->register(L5SwaggerServiceProvider::class);


        // Conditional binding by version (detected by middleware)
        $this->app->bind(CreateDriver::class, function ($app) {
            $version = $app->bound('api.version') ? $app->make('api.version') : 'v1';

            return match ($version) {
                'v2' => new CreateDriverServiceV2(
                    $app->make(DriverRepository::class),
                    $app->make(DriverValidator::class),
                ),
                default => new CreateDriverServiceV1(
                    $app->make(DriverRepository::class),
                    $app->make(DriverValidator::class),
                ),
            };
        });

        // Conditional binding by version (detected by middleware)
        $this->app->bind(ReadDriver::class, function ($app) {
            $version = $app->bound('api.version') ? $app->make('api.version') : 'v1';

            return match ($version) {
                default => new ReadDriverService(
                    $app->make(DriverRepository::class),
                    $app->make(DriverValidator::class),
                ),
            };
        });

        // DriverManagerFacade usando a interface (que agora é resolvível)
        $this->app->singleton(DriverManagerFacade::class, function ($app) {
            return new DriverManagerFacade(
                $app->make(CreateDriver::class),
                $app->make(ReadDriver::class)
            );
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
