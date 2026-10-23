# ==========================================
# Naivas Marketing Command Center - Dockerfile
# Production-ready for Render Deployment
# ==========================================

FROM php:8.2-cli-alpine

# Install system dependencies and PostgreSQL client libraries
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm \
    bash \
    icu-dev

# Install PHP extensions required for Laravel & PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql bcmath gd zip intl opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set environment for production build
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install NPM dependencies and build Vite production assets
RUN npm ci || npm install
RUN npm run build

# Configure permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint startup script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose Render PORT (default: 8080 or assigned by Render)
EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
