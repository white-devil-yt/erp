FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev git unzip curl \
    && docker-php-ext-install pdo_pgsql pdo_mysql \
    && a2enmod rewrite headers \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "php artisan deploy && php artisan serve --host 0.0.0.0 --port ${PORT:-8080}"]