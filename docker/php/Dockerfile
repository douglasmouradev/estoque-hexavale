FROM php:8.5-fpm-bookworm

COPY --from=mlocati/php-extension-installer:2.9 /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl \
    && install-php-extensions pdo_mysql intl zip bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.10 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-hexavale.conf

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

USER www-data

CMD ["php-fpm"]
