#!/bin/sh
set -eu

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

if [ ! -e public/storage ]; then
    php artisan storage:link --no-interaction
fi

if [ -z "${APP_KEY:-}" ]; then
    key_file="storage/app/.docker_app_key"

    if [ -s "$key_file" ]; then
        APP_KEY="$(cat "$key_file")"
    else
        APP_KEY="$(php artisan key:generate --show --no-interaction)"
        printf '%s' "$APP_KEY" > "$key_file"
        chmod 600 "$key_file"
    fi

    export APP_KEY
fi

php artisan package:discover --ansi

if [ "${DB_CONNECTION:-mysql}" = "pgsql" ]; then
    if [ -n "${DB_URL:-}" ]; then
        postgres_ready() {
            pg_isready --dbname="$DB_URL"
        }
    else
        postgres_ready() {
            pg_isready \
                --host="${DB_HOST:-127.0.0.1}" \
                --port="${DB_PORT:-5432}" \
                --username="${DB_USERNAME:-postgres}" \
                --dbname="${DB_DATABASE:-postgres}"
        }
    fi

    until postgres_ready; do
        echo "Esperando a PostgreSQL..."
        sleep 2
    done
else
    until mysqladmin ping \
        --host="${DB_HOST:-database}" \
        --port="${DB_PORT:-3306}" \
        --user="${DB_USERNAME:-root}" \
        --password="${DB_PASSWORD:-root}" \
        --silent; do
        echo "Esperando a MySQL..."
        sleep 2
    done
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${RUN_SEEDERS:-true}" = "true" ]; then
    php artisan db:seed --force --no-interaction
fi

exec "$@"
