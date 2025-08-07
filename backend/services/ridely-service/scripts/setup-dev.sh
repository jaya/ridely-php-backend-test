#!/bin/bash
# only for the first execution
# php artisan key:generate

# only after the installation of the packages
#php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
#php artisan vendor:publish --tag="health-config"
#php artisan vendor:publish --tag="health-migrations"

php artisan config:clear
php artisan l5-swagger:generate

php artisan migrate
php artisan db:seed
