# Stage 1: build frontend assets (Vite)
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP app
FROM serversideup/php:8.5.11

ENV PHP_OPCACHE_ENABLE=1
ENV AUTORUN_ENABLED=true
ENV AUTORUN_LARAVEL_MIGRATION=true

COPY --chown=www-data:www-data . /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Bring in the compiled CSS/JS from stage 1
COPY --chown=www-data:www-data --from=assets /app/public/build /var/www/html/public/build
