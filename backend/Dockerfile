# Production image for Render (Laravel 11 + MongoDB PHP extension)
FROM php:8.2-cli-bookworm

# Install system dependencies and MongoDB extension
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first (better caching)
COPY composer.json composer.lock* ./

# Install dependencies (no dev, no scripts yet)
RUN composer install --no-dev --prefer-dist --no-interaction --no-ansi --optimize-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Run dump-autoload and any post-install commands
RUN composer dump-autoload --optimize

# Set environment variables for Laravel and Render
ENV PORT=8080
ENV HOST=0.0.0.0

# Expose the port
EXPOSE 8080

# Start Laravel's built-in server (suitable for Render)
CMD sh -c "php artisan serve --host=${HOST} --port=${PORT}"
