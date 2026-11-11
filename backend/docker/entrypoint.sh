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

# Passport keys sign the AI assistant (MCP) tokens (docs/mcp.md): from PASSPORT_PRIVATE_KEY / PASSPORT_PUBLIC_KEY,
# or generated once into the storage volume. Never regenerate them on every start: that would disconnect every assistant.
if [ -z "$PASSPORT_PRIVATE_KEY" ] && [ ! -f /var/www/html/storage/oauth-private.key ]; then
    echo "🔑 Generating Passport keys (saved to storage/)..."
    for attempt in 1 2 3; do
        php artisan passport:keys --force --no-interaction && [ -f /var/www/html/storage/oauth-private.key ] && break
        echo "⚠️ Passport key generation failed (attempt $attempt), retrying..."
        rm -f /var/www/html/storage/oauth-private.key /var/www/html/storage/oauth-public.key
    done
    [ -f /var/www/html/storage/oauth-private.key ] || echo "❌ No Passport keys: AI (MCP) connections will not work. Run 'php artisan passport:keys' in the container."
fi

echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
# league/oauth2-server refuses Passport key files unless they are 600 or 660 (every /mcp and OAuth request would fail).
for secret in "$APP_KEY_FILE" /var/www/html/storage/oauth-private.key /var/www/html/storage/oauth-public.key; do
    [ -f "$secret" ] && chmod 600 "$secret"
done

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

# Roles/permissions, fixed pages and sections, first super admin (idempotent, keeps dashboard edits).
if [ "$RUN_MIGRATIONS" = "true" ] || [ "$RUN_MIGRATIONS" = "seed" ]; then
    echo "🧭 Syncing site structure..."
    php artisan site:sync --no-interaction

    # Passport personal access client: needed for personal access tokens of AI assistants (docs/mcp.md). Idempotent.
    if ! php artisan tinker --execute="echo \\Illuminate\\Support\\Facades\\DB::table('oauth_clients')->where('grant_types', 'like', '%personal_access%')->where('revoked', false)->exists() ? 'yes' : '';" 2>/dev/null | grep -q yes; then
        echo "🔑 Creating the Passport personal access client..."
        php artisan passport:client --personal --name="Personal Access" --provider=users --no-interaction || echo "⚠️ Could not create the Passport personal access client"
    fi
fi

echo "⚡ Optimizing..."
php artisan optimize:clear
php artisan optimize
php artisan filament:optimize 2>/dev/null || true

mkdir -p /var/log/supervisor /var/log/nginx
chown -R www-data:www-data /var/log/supervisor /var/log/nginx

echo "🎯 Starting nginx, php-fpm, horizon and the scheduler (supervisor)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
