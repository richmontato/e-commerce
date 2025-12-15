#!/bin/bash

# Navigate to the Laravel project root
cd /var/www

# Install Composer dependencies if they don't exist
if [ ! -d "vendor" ]; then
    composer install
fi

# Run database migrations and seeders.
# If this fails, the script will now continue anyway.
php artisan migrate:fresh --seed

# Start the Laravel development server
# This should always run now.
php artisan serve --host=0.0.0.0 --port=8000