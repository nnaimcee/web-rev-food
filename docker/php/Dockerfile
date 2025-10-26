FROM php:8.2-fpm

# ติดตั้ง system deps
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libicu-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && rm -rf /var/lib/apt/lists/*

# ติดตั้ง Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ✅ คัดลอกโค้ดโปรเจกต์มาก่อน (สำคัญ!)
COPY . .

# ✅ สร้างโฟลเดอร์ให้แน่ใจว่ามี
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# (ตัวเลือก) ตั้งค่า PHP เพิ่มเติมได้ที่นี่
# RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

CMD ["php-fpm"