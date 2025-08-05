#!/bin/bash
# only for the first execution
# php artisan key:generate

# only for the development
#php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
#php artisan vendor:publish --tag="health-config"
#php artisan vendor:publish --tag="health-migrations"

php artisan config:clear
php artisan l5-swagger:generate

php artisan migrate

# Only for dev
# php artisan db:seed