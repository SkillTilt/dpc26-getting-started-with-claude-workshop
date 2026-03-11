FROM php:8.4-cli

# Install minimal system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libicu-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
RUN composer run-script post-autoload-dump 2>/dev/null || true

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

RUN cp .env.example .env && php artisan key:generate
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
