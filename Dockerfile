FROM php:8.2-fpm

# ติดตั้ง Nginx และเครื่องมือพื้นฐาน
RUN apt-get update && apt-get install -y nginx supervisor libpng-dev libjpeg-dev libfreetype6-dev zip git unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ตั้ง working dir
WORKDIR /var/www/html

# คัดลอก source code
COPY . .

# ติดตั้ง composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# คัดลอก config nginx ของคุณเข้าไป
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# ลบ config เดิมของ nginx ถ้ามี
RUN rm -f /etc/nginx/sites-enabled/default

# สร้าง supervisor config เพื่อรันทั้ง nginx และ php-fpm พร้อมกัน
RUN echo "[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=php-fpm\n\n[program:nginx]\ncommand=nginx -g 'daemon off;'\n" > /etc/supervisor/conf.d/supervisord.conf

# เปิด port 80 ให้เว็บเข้าได้
EXPOSE 80

# สั่งให้ supervisor รันทั้งคู่
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
