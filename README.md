# FoodReview (Laravel 12 + Docker)

เว็บรีวิวอาหารแบบง่ายที่มีระบบสมาชิก กดถูกใจ คอมเมนต์ Hashtag และหน้าแดชบอร์ดผู้ดูแลระบบ สร้างบน Laravel 12 และรันด้วย Docker Compose

## คุณสมบัติเด่น
- หน้าแรกแสดงการ์ดรีวิว พร้อม Modal แสดงรายละเอียด และระบบค้นหา/กรองตามร้าน/แท็ก
- ระบบกดถูกใจ/ยกเลิก สำหรับผู้ล็อกอิน (Ajax)
- ระบบคอมเมนต์แบบเธรด สำหรับผู้ล็อกอิน (Ajax)
- Hashtag ต่อรีวิว และลิสต์รีวิวตามแท็ก
- แดชบอร์ดผู้ดูแลระบบ แสดงสถิติรวม ร้านยอดนิยม รีวิวล่าสุด
- จัดการสิทธิ์ด้วย Spatie Permission (`user`, `admin`)

## เทคโนโลยี
- PHP 8.2, Laravel 12
- MySQL 8, phpMyAdmin
- Nginx + PHP-FPM (Docker)
- Bootstrap 5 (CDN)

## โครงสร้างโดยย่อ
- โค้ด Laravel อยู่ที่ `src/`
- ไฟล์ Docker อยู่ที่ `docker/` และ `docker-compose.yml`
- ตัวอย่างฐานข้อมูล: `database/food_review_app.sql`

## เริ่มต้นอย่างรวดเร็ว (Docker)
ข้อกำหนด: ติดตั้ง Docker Desktop และ Docker Compose

1) สตาร์ทคอนเทนเนอร์

```bash
docker compose up -d
```

- Web: http://localhost:8080
- phpMyAdmin: http://localhost:8081 (host: `db`, user: `root`, pass: `root`)

2) ติดตั้ง dependencies และตั้งค่าแอป (ทำในคอนเทนเนอร์ `app`)

```bash
docker compose exec app bash -lc "cd /var/www/html \
  && composer install \
  && cp -n .env.example .env \
  && php artisan key:generate"
```

3) ตั้งค่าฐานข้อมูลในไฟล์ `.env`

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

4) รันไมเกรต (และข้อมูลตั้งต้นหากต้องการ)

```bash
docker compose exec app bash -lc "cd /var/www/html && php artisan migrate"
```

ทางเลือก: ต้องการข้อมูลตัวอย่างอย่างรวดเร็ว ให้ Import ไฟล์ `database/food_review_app.sql` ผ่าน phpMyAdmin (เลือกฐานข้อมูล `laravel` ก่อน)

## การเข้าสู่ระบบและสิทธิ์
- สมัครสมาชิกได้ที่ `/register` (ระบบจะกำหนดบทบาท `user` อัตโนมัติ)
- หน้าหลังบ้านผู้ดูแล: `/admin/dashboard` (ต้องมีบทบาท `admin`)

การกำหนดบทบาทแอดมินอย่างรวดเร็ว (รันในคอนเทนเนอร์ `app`):
```bash
docker compose exec app bash
php artisan tinker
>>> $u = \App\Models\User::first();
>>> $u->assignRole('admin');
>>> exit
```

## คำสั่งที่มีประโยชน์
- เข้า shell แอป: `docker compose exec app bash`
- เคลียร์แคชคอนฟิก: `php artisan config:clear`
- รันทดสอบ: `php artisan test`

## License
โค้ดภายในโปรเจกต์นี้ใช้เพื่อการเรียนรู้

