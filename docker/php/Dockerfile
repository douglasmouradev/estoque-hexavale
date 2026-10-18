FROM php:8.5-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl libzip-dev libicu-dev libpng-dev libonig-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql intl zip bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.10 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-hexavale.conf

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

USER www-data

CMD ["php-fpm"]
