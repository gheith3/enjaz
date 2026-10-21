#!/bin/sh

# CAMS Docker entrypoint
echo "🚀 Starting CAMS ${APP_VERSION:-dev} (${GIT_COMMIT:-unknown})..."

# Create storage directories FIRST (required for artisan commands)
echo "📁 Setting up storage directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

# Ensure APP_KEY exists (required for encryption). Persist in storage volume for local Docker.
APP_KEY_FILE="/var/www/html/storage/.app_key"
if [ -z "$APP_KEY" ]; then
    if [ -f "$APP_KEY_FILE" ]; then
        APP_KEY="$(cat "$APP_KEY_FILE")"
        export APP_KEY
        echo "🔑 Loaded APP_KEY from storage"
    else
        APP_KEY="base64:$(openssl rand -base64 32)"
        export APP_KEY
        echo "$APP_KEY" > "$APP_KEY_FILE"
        chown www-data:www-data "$APP_KEY_FILE" 2>/dev/null || true
        echo "🔑 Generated new APP_KEY (saved to storage/.app_key)"
    fi
fi

# Passport keys sign the AI (MCP) connection tokens. Use PASSPORT_PRIVATE_KEY /
# PASSPORT_PUBLIC_KEY env vars, or keep generated keys in the storage volume. Never
# regenerate them on every start: that would disconnect every AI connection.
if [ -z "$PASSPORT_PRIVATE_KEY" ] && [ ! -f /var/www/html/storage/oauth-private.key ]; then
    echo "🔑 Generating Passport keys (saved to storage/)..."
    # Key generation occasionally fails on first try ("Unable to read key"); retry.
    for attempt in 1 2 3; do
        php artisan passport:keys --force --no-interaction && [ -f /var/www/html/storage/oauth-private.key ] && break
        echo "⚠️ Passport key generation failed (attempt $attempt), retrying..."
        rm -f /var/www/html/storage/oauth-private.key /var/www/html/storage/oauth-public.key
    done
    [ -f /var/www/html/storage/oauth-private.key ] || echo "❌ No Passport keys: AI (MCP) connections will not work. Run 'php artisan passport:keys' in the container."
fi
# Set proper permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Secrets in storage: readable by the app user only (after the recursive chmod above).
# league/oauth2-server refuses BOTH Passport keys unless they are 600 or 660; with 644 the
# public key makes every /mcp and OAuth request fail with a 500.
for secret in /var/www/html/storage/oauth-private.key /var/www/html/storage/oauth-public.key /var/www/html/storage/.app_key; do
    if [ -f "$secret" ]; then
        chown www-data:www-data "$secret"
        chmod 600 "$secret"
    fi
done

# Wait for database based on connection type
case "$DB_CONNECTION" in
    sqlite)
        echo "📦 Using SQLite database - no connection wait needed"
        if [ -n "$DB_DATABASE" ] && [ ! -f "$DB_DATABASE" ]; then
            echo "📝 Creating SQLite database file..."
            touch "$DB_DATABASE"
            chown www-data:www-data "$DB_DATABASE"
        fi
        ;;
    mysql|mariadb)
        if [ -n "$DB_HOST" ]; then
            echo "⏳ Waiting for MariaDB/MySQL connection..."
            until nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
                echo "Database not ready - sleeping..."
                sleep 2
            done
            echo "✅ MariaDB/MySQL connection established"
        fi
        ;;
    pgsql)
        if [ -n "$DB_HOST" ]; then
            echo "⏳ Waiting for PostgreSQL connection..."
            until nc -z "$DB_HOST" "${DB_PORT:-5432}"; do
                echo "Database not ready - sleeping..."
                sleep 2
            done
            echo "✅ PostgreSQL connection established"
        fi
        ;;
    *)
        echo "⚠️ Unknown database connection type: $DB_CONNECTION"
        ;;
esac

# Create storage symlink for public files
echo "🔗 Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true



# Run database migrations
# RUN_MIGRATIONS=true  -> Run migrations only (safe for production)
# RUN_MIGRATIONS=seed  -> Run migrations with seeding (for initial setup)
# RUN_MIGRATIONS=false -> Skip migrations (default)
case "$RUN_MIGRATIONS" in
    true)
        echo "🗄️ Running database migrations..."
        php artisan migrate --force --no-interaction
        ;;
    seed)
        echo "🗄️ Running database migrations with seeding..."
        php artisan migrate --seed --force --no-interaction
        ;;
    *)
        echo "⏭️ Skipping database migrations (set RUN_MIGRATIONS=true to run)"
        ;;
esac

# A migration that re-creates the mcp views drops the AI reader's grants; re-apply them
# (idempotent) whenever free SQL is configured. A failure must not stop the app.
if [ -n "$DB_MCP_PASSWORD" ] && [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "🔐 Applying AI free-SQL reader grants..."
    php artisan cams:mcp-sql-setup --no-interaction || echo "⚠️ cams:mcp-sql-setup failed; AI free SQL may be unavailable"
fi

# Roles and permissions must exist; both steps only add what is missing. Permissions only:
# the policy files are committed, and regenerating them would overwrite custom methods.
# Default role permissions (RolePermissionsSeeder) are NOT re-synced on start, so changes
# made under Administration → Roles survive restarts; run it by hand after releases that
# add permissions (see docs/deployment.md).
echo "⚡ Seeding roles and generating permissions..."
php artisan db:seed --class=RolesSeeder --force --no-interaction
php artisan shield:generate --all --panel=dashboard --option=permissions --no-interaction

# API docs (/docs): generated, not committed.
echo "📚 Generating API docs..."
php artisan scribe:generate --force --no-interaction >/dev/null 2>&1 || echo "⚠️ API docs generation failed; /docs may be unstyled"

echo "⚡ Optimizing application..."
php artisan optimize:clear
php artisan livewire:publish --assets
php artisan optimize
php artisan filament:optimize

# Sync schedule monitor (tracks scheduled tasks)
echo "📊 Syncing schedule monitor..."
php artisan schedule-monitor:sync --no-interaction 2>/dev/null || true

echo "✅ Laravel initialization complete!"

# Ensure Supervisor log directory exists
echo "🎯 Ensuring supervisor log directory exists..."
mkdir -p /var/log/supervisor
chown -R www-data:www-data /var/log/supervisor

# Ensure Nginx log directory exists
echo "📁 Ensuring nginx log directory exists..."
mkdir -p /var/log/nginx
chown -R www-data:www-data /var/log/nginx

# Start supervisor to manage all processes
echo "🎯 Starting services with supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf