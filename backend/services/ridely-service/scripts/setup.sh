#!/bin/bash
php artisan key:generate

php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
php artisan config:clear
php artisan l5-swagger:generate

php artisan vendor:publish --tag="health-config"
php artisan vendor:publish --tag="health-migrations"

php artisan migrate