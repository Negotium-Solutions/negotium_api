#!/bin/bash
# Delete tenant databases
php artisan tenants:drop-databases
# Stop the app
php artisan down
# Create and seed the central database data
php artisan migrate:fresh --seed
# Create and seed the tenants databases and data
php artisan tenants:seed
# Regenerate the API documentation
php artisan l5-swagger:generate
# Start the app
php artisan up
