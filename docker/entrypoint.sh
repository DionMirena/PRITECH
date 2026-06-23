#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -f .env ]; then
    echo "[entrypoint] no .env found, copying .env.example"
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "[entrypoint] generating APP_KEY"
    php artisan key:generate --force
fi

echo "[entrypoint] waiting for database at ${DB_HOST:-db}:${DB_PORT:-3306}"
for i in $(seq 1 30); do
    if php -r "exit(@fsockopen(getenv('DB_HOST') ?: 'db', (int)(getenv('DB_PORT') ?: 3306)) ? 0 : 1);"; then
        echo "[entrypoint] database is up"
        break
    fi
    sleep 1
done

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
    echo "[entrypoint] running migrations"
    php artisan migrate --force

    if [ "${RUN_SEED:-0}" = "1" ]; then
        echo "[entrypoint] seeding database"
        php artisan db:seed --force
    fi
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
