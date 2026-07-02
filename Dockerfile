FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpq-dev \
    ca-certificates \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN sed -i 's/80/10000/g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's/80/10000/g' /etc/apache2/ports.conf

EXPOSE 10000

CMD ["sh", "-c", "php artisan migrate:fresh --force && apache2-foreground"]