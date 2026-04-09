# Production image for Render (Laravel 11 + MongoDB PHP extension)
FROM php:8.2-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libgd-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip intl exif fileinfo bcmath \
    && pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY backend/composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-ansi --optimize-autoloader --no-scripts --ignore-platform-req=all

COPY backend/ .

RUN composer dump-autoload --optimize

# إنشاء مستخدم Admin تلقائياً (إذا لم يكن موجوداً)
RUN php artisan tinker --execute="
\$user = App\Models\User::where('email', 'admin@xpay.com')->first();
if (!\$user) {
    App\Models\User::create([
        'email' => 'admin@xpay.com',
        'password' => bcrypt('password'),
        'name' => 'Admin',
        'role' => 'admin',
        'telegram_id' => null,
        'username' => 'admin',
        'balance' => ['USD' => 0, 'SYP' => 0],
        'is_banned' => false,
        'supabase_uid' => null
    ]);
    echo \"Admin user created.\\n\";
} else {
    echo \"Admin user already exists.\\n\";
}
"

ENV PORT=8080
ENV HOST=0.0.0.0

EXPOSE 8080

CMD sh -c "php artisan serve --host=${HOST} --port=${PORT}"
