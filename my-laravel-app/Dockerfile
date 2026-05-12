FROM dunglas/frankenphp:php8.4-bookworm

WORKDIR /app

RUN apt-get update && apt-get install -y git unzip && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions pdo_mysql mbstring bcmath intl opcache zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE ${PORT:-8000}

CMD php artisan config:clear && php artisan config:cache && php artisan migrate --force --seed && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
