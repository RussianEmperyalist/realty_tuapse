#!/bin/sh

set -e

# Create storage link if it doesn't exist
php artisan storage:link --force --quiet

# Ensure sqlite database file exists for local/dev default
if [ -z "$DB_CONNECTION" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
    fi
fi

exec "$@"
