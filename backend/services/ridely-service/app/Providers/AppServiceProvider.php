<?php

namespace App\Providers;

use App\Models\PricingRule;
use App\Models\Ride;
use App\Services\Facades\DriverManagerFacade;
use App\Services\Facades\RideManagerFacade;
use App\Services\Interfaces\DriverServiceInterface;
use App\Services\Interfaces\EstimateRideServiceInterface;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;
use App\Services\RideCacheService;
use App\Services\V1\DriverService;
use App\Services\V1\EstimateRideService;
use App\Services\V1\LocationService;
use App\Services\V1\RideService;
use App\Services\V2\V2DriverService;
use App\Validators\DriverValidator;
use App\Validators\EstimateRideValidator;
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

        $this->app->bind(DriverServiceInterface::class, function ($app) {
            $version = $app->bound('api.version') ? $app->make('api.version') : 'v1';

            return match ($version) {
                'v2' => new V2DriverService(
                    $app->make(DriverValidator::class),
                ),
                default => new DriverService(
                    $app->make(DriverValidator::class),
                ),
            };
        });

        // DriverManagerFacade usando a interface (que agora é resolvível)
        $this->app->singleton(DriverManagerFacade::class, function ($app) {
            return new DriverManagerFacade(
                $app->make(DriverServiceInterface::class),
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
        $this->app->singleton(RideService::class, function ($app) {
           return new RideService(
               $app->make(Ride::class),
               $app->make(RideValidator::class),
               $app->make(LocationServiceInterface::class),
               $app->make(RideCacheService::class)
           );
        });

        $this->app->bind(EstimateRideServiceInterface::class, EstimateRideService::class);
        $this->app->singleton(EstimateRideService::class, function ($app) {
            return new EstimateRideService(
                $app->make(EstimateRideValidator::class),
                $app->make(LocationServiceInterface::class),
            );
        });

        $this->app->singleton(RideManagerFacade::class, function ($app) {
            $rideService = $app->make(RideServiceInterface::class);
            return new RideManagerFacade(
                $rideService,
                $app->make(EstimateRideServiceInterface::class),
                $rideService->getLocationService()
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
