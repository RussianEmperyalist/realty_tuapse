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

# Clear config cache so runtime env vars (App Platform panel) take effect
php artisan config:clear --quiet || true

# Apply pending migrations (idempotent; safe on every container start)
php artisan migrate --force --no-interaction

# Seed once on first boot: enable via REALTY_SEED_ON_START=true (e.g. production)
if [ "$REALTY_SEED_ON_START" = "true" ] && [ ! -f storage/app/.seeded ]; then
    php artisan db:seed --force --no-interaction
    touch storage/app/.seeded
fi

exec "$@"
