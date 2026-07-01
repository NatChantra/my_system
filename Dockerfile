FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

CMD ["sh", "-c", "php artisan migrate --force && apache2-foreground"]