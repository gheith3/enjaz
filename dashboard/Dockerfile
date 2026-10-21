# ==========================================
# STAGE 1: Base PHP Environment
# ==========================================
FROM php:8.4-fpm-alpine AS base

# Runtime packages and the shared libraries the PHP extensions link against.
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    gzip \
    openssl \
    postgresql-client \
    libpq \
    sqlite \
    sqlite-libs \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs \
    oniguruma \
    netcat-openbsd

# PHP extensions. Build tools and -dev headers are only needed to compile them, so they
# are removed again in the same layer to keep the image small.
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        postgresql-dev \
        sqlite-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pdo_sqlite \
        gd \
        zip \
        intl \
        mbstring \
        opcache \
        bcmath \
        pcntl \
        posix \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear

# Composer 2 (pinned major, not :latest, so a new Composer major can't break releases).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html


# ==========================================
# STAGE 2: Composer Dependencies
# ==========================================
FROM base AS vendor

COPY composer.json composer.lock ./
# Production dependencies only; scripts and autoloader wait until the code is copied.
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

COPY . .

# Laravel's cache directories must exist before package:discover runs.
RUN mkdir -p bootstrap/cache storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && composer dump-autoload --optimize --no-dev --no-interaction


# ==========================================
# STAGE 3: Frontend Builder (Node.js)
# ==========================================
FROM node:24-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
# Tailwind scans Filament's views in vendor/ for the theme classes.
COPY --from=vendor /var/www/html/vendor ./vendor

RUN npm run build


# ==========================================
# STAGE 4: Final Production Image
# ==========================================
FROM base AS final

ARG APP_VERSION=dev
ARG GIT_COMMIT=unknown
ENV APP_VERSION=${APP_VERSION} \
    GIT_COMMIT=${GIT_COMMIT}
LABEL org.opencontainers.image.title="Injazat" \
      org.opencontainers.image.description="Omani Youth Achievements platform" \
      org.opencontainers.image.version="${APP_VERSION}" \
      org.opencontainers.image.revision="${GIT_COMMIT}"

COPY --from=vendor /var/www/html /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
        storage/app/public bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN mkdir -p /var/log/nginx /var/log/supervisor /var/log/php-fpm /run/nginx \
    && touch /var/log/nginx/access.log /var/log/nginx/error.log \
    && chown -R www-data:www-data /var/log/nginx /run/nginx \
    && chmod +x /entrypoint.sh

EXPOSE 80

# Laravel's /up checks that the app boots (not just that nginx answers).
HEALTHCHECK --interval=30s --timeout=10s --start-period=120s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]
