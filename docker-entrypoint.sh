#!/bin/bash
set -e

echo "=========================================================="
echo " Starting Naivas Marketing Command Center (Render Startup) "
echo "=========================================================="

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and optimize config & routes cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations with force flag for PostgreSQL
echo "Running PostgreSQL database migrations..."
php artisan migrate --force

# Seed mock database if empty
echo "Ensuring mock data is seeded..."
php artisan db:seed --force

echo "Starting server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
