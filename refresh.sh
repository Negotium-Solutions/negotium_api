#!/bin/bash
# Remove current state of the central database and all tenants
vendor/bin/sail down -v
# Run sail/docker
vendor/bin/sail up -d
# Wait for the database to start before seeding data
sleep 20
# Create and seed the central database data
vendor/bin/sail artisan migrate:fresh --seed
# Create and seed the tenants databases and data
vendor/bin/sail artisan tenants:seed
# Regenerate the API documentation
vendor/bin/sail artisan l5-swagger:generate
