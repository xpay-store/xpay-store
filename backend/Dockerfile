# Production image for Render (Laravel 11 + MongoDB PHP extension)
FROM php:8.2-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-ansi --optimize-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize

ENV PORT=8080
ENV HOST=0.0.0.0

EXPOSE 8080

CMD sh -c "php artisan serve --host=${HOST} --port=${PORT}"
