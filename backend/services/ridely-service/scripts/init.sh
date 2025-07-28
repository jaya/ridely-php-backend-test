#!/bin/bash
# php artisan key:generate
# php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
php artisan config:clear
php artisan l5-swagger:generate
composer dump