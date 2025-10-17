FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libicu-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install pdo pdo_mysql zip intl \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd \
 && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html
