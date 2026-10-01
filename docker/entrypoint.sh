#!/bin/sh
set -e

cd /var/www/html

# Wait for the database when using MySQL
if [ "${DB_CONNECTION}" = "mysql" ] && [ -n "${DB_HOST}" ]; then
    echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
    # --skip-ssl: the MariaDB client in the image rejects MySQL 8's self-signed cert
    until mysqladmin ping --skip-ssl -h"${DB_HOST}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent >/dev/null 2>&1; do
        sleep 2
    done
    echo "MySQL is up."
fi

# Only the main web container runs one-time setup tasks
if [ "${CONTAINER_ROLE:-app}" = "app" ]; then
    if [ -z "${APP_KEY}" ]; then
        echo "WARNING: APP_KEY is empty. Generate one with: docker compose exec app php artisan key:generate --show"
    fi

    php artisan storage:link --force >/dev/null 2>&1 || true

    if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
        php artisan migrate --force
    fi

    if [ "${APP_ENV}" = "production" ]; then
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan filament:optimize || true
    else
        php artisan optimize:clear >/dev/null 2>&1 || true
    fi
fi

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

exec "$@"
