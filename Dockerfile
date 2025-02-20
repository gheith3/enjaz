# Use PHP Apache base image
FROM php:8.2.12-apache

# Update and install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    mariadb-client \
    curl \
    git \
    supervisor \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip intl gd exif pcntl bcmath opcache \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set the document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && echo "ServerName localhost" >> /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

# Set the working directory
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && echo 'COMPOSER_ALLOW_SUPERUSER=1' >> /usr/local/etc/php/conf.d/docker-vars.ini

# Install Composer dependencies first to optimize Docker layer caching
COPY composer.json composer.lock /var/www/html/
RUN composer install --no-interaction --no-plugins --no-scripts

# Copy the application code
COPY . /var/www/html

# Install npm dependencies and build assets
RUN npm install && npm run build

# Ensure storage and bootstrap/cache directories exist and are writable
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data . \
    && chmod -R 775 storage bootstrap/cache

# Copy Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Use custom entrypoint script to start Apache, Supervisor, and queue workers
ENTRYPOINT ["docker-entrypoint.sh"]
