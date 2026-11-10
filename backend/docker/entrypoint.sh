#!/bin/sh
# Container entrypoint (ghaklab Laravel template, adapted from CAMS).
# RUN_MIGRATIONS: true = migrate (safe for production), seed = migrate + seed (first start), false = skip.

echo "🚀 Starting ${APP_NAME:-app} ${APP_VERSION:-dev} (${GIT_COMMIT:-unknown})..."

# Storage directories first (artisan needs them).
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

# APP_KEY: from the environment, or generated once and kept in the storage volume.
APP_KEY_FILE="/var/www/html/storage/.app_key"
if [ -z "$APP_KEY" ]; then
    if [ -f "$APP_KEY_FILE" ]; then
        APP_KEY="$(cat "$APP_KEY_FILE")"
        echo "🔑 Loaded APP_KEY from storage"
    else
        APP_KEY="base64:$(openssl rand -base64 32)"
        echo "$APP_KEY" > "$APP_KEY_FILE"
        echo "🔑 Generated APP_KEY (saved to storage/.app_key)"
    fi
    export APP_KEY
fi

echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
[ -f "$APP_KEY_FILE" ] && chmod 600 "$APP_KEY_FILE"

# Wait for the database.
case "$DB_CONNECTION" in
    pgsql)
        echo "⏳ Waiting for PostgreSQL ($DB_HOST:${DB_PORT:-5432})..."
        until nc -z "$DB_HOST" "${DB_PORT:-5432}"; do sleep 2; done ;;
    mysql|mariadb)
        echo "⏳ Waiting for MySQL ($DB_HOST:${DB_PORT:-3306})..."
        until nc -z "$DB_HOST" "${DB_PORT:-3306}"; do sleep 2; done ;;
esac

php artisan storage:link --force 2>/dev/null || true

case "$RUN_MIGRATIONS" in
    true) echo "🗄️ Migrating..."; php artisan migrate --force --no-interaction ;;
    seed) echo "🗄️ Migrating + seeding..."; php artisan migrate --seed --force --no-interaction ;;
    *)    echo "⏭️ Skipping migrations (RUN_MIGRATIONS=$RUN_MIGRATIONS)" ;;
esac

echo "⚡ Optimizing..."
php artisan optimize:clear
php artisan optimize
php artisan filament:optimize 2>/dev/null || true

mkdir -p /var/log/supervisor /var/log/nginx
chown -R www-data:www-data /var/log/supervisor /var/log/nginx

echo "🎯 Starting nginx, php-fpm, horizon and the scheduler (supervisor)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
