#!/bin/bash
# Usado no Job: job-migrate.yaml para criar o banco de dados e popular com dados iniciais
php artisan migrate --force
php artisan db:seed