<?php

namespace App\Providers;

use App\Models\PricingRule;
use App\Models\Ride;
use App\Repositories\V1\DriverRepository;
use App\Services\Facades\DriverManagerFacade;
use App\Services\Facades\RideManagerFacade;
use App\Services\Interfaces\Driver\CreateDriverServiceInterface;
use App\Services\Interfaces\Driver\ReadDriverServiceInterface;
use App\Services\Interfaces\Location\LocationServiceInterface;
use App\Services\Interfaces\Ride\RideServiceInterface;
use App\Services\V1\Driver\CreateDriverService as CreateDriverServiceV1;
use App\Services\V1\Driver\ReadDriverService;
use App\Services\V1\Location\LocationService;
use App\Services\V1\Ride\RideService;
use App\Services\V2\Driver\CreateDriverServiceServiceInterface as CreateDriverServiceV2;
use App\Validators\DriverValidator;
use App\Validators\LocationValidator;
use App\Validators\RideValidator;
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
        $this->app->bind(CreateDriverServiceInterface::class, function ($app) {
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
        $this->app->bind(ReadDriverServiceInterface::class, function ($app) {
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
                $app->make(CreateDriverServiceInterface::class),
                $app->make(ReadDriverServiceInterface::class)
            );
        });

        $this->app->bind(LocationServiceInterface::class, LocationService::class);
        $this->app->singleton(LocationService::class, function ($app) {
            $locationServiceUrl = env('LOCATION_SERVICE_URL', 'https://nominatim.openstreetmap.org/search');
            return new LocationService(
                $app->make(PricingRule::class),
                $app->make(LocationValidator::class),
                $locationServiceUrl
            );
        });

        $this->app->bind(RideServiceInterface::class, RideService::class);
        $this->app->bind(RideService::class, function ($app) {
           return new RideService(
               $app->make(Ride::class),
               $app->make(RideValidator::class),
           );
        });

        $this->app->singleton(RideManagerFacade::class, function ($app) {
            return new RideManagerFacade(
                $app->make(RideService::class),
                $app->make(LocationServiceInterface::class)
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
