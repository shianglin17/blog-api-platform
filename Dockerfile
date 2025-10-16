FROM dunglas/frankenphp:latest

RUN install-php-extensions \
    pcntl \
    pdo_sqlite \
    zip \
    gd \
    intl \
    opcache

# FrankenPHP Official Known Issues（Composer scripts，https://frankenphp.dev/docs/known-issues/#composer-scripts） \
# 官方提供 Composer 使用的 PHP 包裝腳本，避開 FrankenPHP CLI 尚未支援的 -d 參數
COPY docker/php-wrapper.sh /usr/local/bin/php
RUN chmod +x /usr/local/bin/php

# 指定 wrapper 作為 PHP_BINARY 並關閉 Composer 的 root 警告
ENV PHP_BINARY=/usr/local/bin/php \
    COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --optimize-autoloader --no-interaction --no-scripts

COPY . .

RUN php artisan package:discover --ansi

RUN mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 8000

ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000", "--workers=4", "--max-requests=500"]
