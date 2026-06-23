# syntax=docker/dockerfile:1.7

# ---------- Stage 1: composer dependencies ----------
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ---------- Stage 2: PHP-FPM runtime ----------
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache \
        bash \
        git \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        mysql-client \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        intl \
        opcache \
        zip \
    && rm -rf /var/cache/apk/*

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini

WORKDIR /var/www/html

COPY --from=vendor /app/vendor ./vendor
COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
