FROM serversideup/php:8.3-fpm-nginx

ENV PHP_OPCACHE_ENABLE=1
ENV AUTORUN_ENABLED=true
ENV AUTORUN_LARAVEL_MIGRATION=true

COPY --chown=www-data:www-data . /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction
