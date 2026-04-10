# Laravel Campaign & Notification System

Hệ thống quản lý **chiến dịch email** và **thông báo** được xây dựng trên nền tảng Laravel 8, chạy hoàn toàn trên Docker. Dự án hỗ trợ phân quyền Admin/User, lên lịch gửi email tự động, quản lý subscriber, và hệ thống notification real-time.

---

## Tính năng chính

| Tính năng                 | Mô tả                                                            |
| ------------------------- | ---------------------------------------------------------------- |
| **Xác thực & Phân quyền** | Đăng nhập/Đăng xuất với Laravel Breeze, phân quyền Admin và User |
| **Admin Dashboard**       | Tổng quan dữ liệu hệ thống, bảng thống kê real-time (AJAX)       |
| **Campaign Email**        | Tạo và quản lý chiến dịch gửi email hàng loạt                    |
| **Lên lịch Campaign**     | Hẹn giờ gửi campaign tự động với Task Scheduling                 |
| **Hệ thống Notification** | Thông báo cho user với đánh dấu đã đọc/chưa đọc, xoá             |
| **Đa ngôn ngữ**           | Hỗ trợ Tiếng Việt và Tiếng Anh                                   |

---

## Tech Stack

| Thành phần           | Công nghệ                                   |
| -------------------- | ------------------------------------------- |
| **Backend**          | PHP 7.4, Laravel 8.x                        |
| **Frontend**         | Blade Templates, Tailwind CSS, Bootstrap    |
| **Database**         | MySQL 8.0                                   |
| **Cache & Queue**    | Redis                                       |
| **Web Server**       | Nginx                                       |
| **Process Manager**  | Supervisor (Queue Worker + Schedule Runner) |
| **Containerization** | Docker, Docker Compose                      |
| **Authentication**   | Laravel Breeze                              |
| **Debugging**        | Laravel Telescope                           |
| **HTTP Client**      | Guzzle                                      |

---

## Cấu trúc dự án

```
dockerLaravel/
├── docker-compose.yml          # Cấu hình Docker services
├── Dockerfile                  # Build image PHP-FPM
├── supervisord.conf            # Cấu hình Supervisor (queue + schedule)
├── nginx/
│   └── default.conf            # Cấu hình Nginx
└── src/                        # Mã nguồn Laravel
    ├── app/
    │   ├── Console/
    │   │   └── Commands/       # Artisan commands (DispatchCampaignCommand)
    │   ├── Exceptions/         # Global exception handling
    │   ├── Http/
    │   │   ├── Controllers/    # Controllers (Admin, Campaign, Notification, ...)
    │   │   ├── Middleware/      # CheckRole, SetLocale, ...
    │   │   ├── Requests/       # Form Request validation
    │   │   ├── Responses/      # API Response Trait
    │   │   └── Services/       # Business logic layer
    │   ├── Jobs/               # Queue jobs (SendCampaignJob)
    │   ├── Mail/               # Mailable classes (CampaignMail, WelcomeMail)
    │   ├── Models/             # Eloquent models
    │   ├── Providers/          # Service providers
    │   └── Repositories/       # Repository pattern
    │       ├── Eloquent/       # Implementations
    │       └── Interfaces/     # Contracts
    ├── database/
    │   └── migrations/         # Database migrations
    │   └── factories/          # Database factories
    │   └── seeders/            # Database seeders
    ├── public/
    │   └── css/                # Public css
    │   └── js/                 # Public js
    ├── resources/
    │   ├── css/                # Resource css
    │   └── js/                 # Resource js
    │   ├── lang/               # Ngôn ngữ (en, vi)
    │   └── views/              # Blade templates
    │       ├── admin/          # Giao diện Admin
    │       ├── auth/           # Giao diện xác thực
    │       ├── emails/         # Email templates
    │       ├── user/           # Giao diện User
    │       └── layouts/        # Layout chung
    ├── routes/
    │   ├── web.php             # Web routes
    │   └── api.php             # API routes
    ├── composer.json
    └── .env.example
```

---

## Yêu cầu hệ thống

| Phần mềm                                                   | Phiên bản tối thiểu |
| ---------------------------------------------------------- | ------------------- |
| [Docker](https://docs.docker.com/get-docker/)              | 20.10+              |
| [Docker Compose](https://docs.docker.com/compose/install/) | 2.0+                |
| Git                                                        | 2.30+               |

> **Không cần** cài đặt PHP, Composer, MySQL hay Redis trên máy host — tất cả đã được đóng gói trong Docker.

---

## Phân quyền thư mục (Linux/Ubuntu)

Nếu bạn dùng Ubuntu/Linux và gặp lỗi kiểu `Permission denied` khi ghi log/cache/session, hãy cấp quyền cho các thư mục cần ghi của Laravel:

```bash
# Chạy tại thư mục root dự án
sudo chown -R $USER:$USER src/storage src/bootstrap/cache
sudo chmod -R 775 src/storage src/bootstrap/cache
```

Nếu vẫn lỗi do container ghi file bằng user `www-data`, chạy thêm trong container app:

```bash
docker exec -it laravel_app bash -lc "chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && chmod -R 775 /var/www/storage /var/www/bootstrap/cache"
```

> Sau khi cấp quyền, thử lại:
>
> -   `docker compose up -d`
> -   `docker exec -it laravel_app php artisan optimize:clear`

---

## Cài đặt & Khởi chạy

### 1. Clone dự án

```bash
git clone https://github.com/quangntq1210/dockerLaravel.git
cd dockerLaravel
```

### 2. Cấu hình môi trường

```bash
cp src/.env.example src/.env
```

Chỉnh sửa file `src/.env` theo cấu hình Docker (xem mục [Cấu hình môi trường](#-cấu-hình-môi-trường) bên dưới).

### 3. Khởi chạy Docker containers

```bash
docker compose up -d --build
```

Lệnh này sẽ khởi tạo **5 services**:

| Container        | Vai trò                  | Port            |
| ---------------- | ------------------------ | --------------- |
| `laravel_app`    | PHP-FPM (Application)    | 9000 (internal) |
| `laravel_nginx`  | Web Server               | **8088** → 80   |
| `laravel_worker` | Queue Worker + Scheduler | —               |
| `laravel_db`     | MySQL Database           | **3307** → 3306 |
| `laravel_redis`  | Redis Cache & Queue      | **6379**        |

### 4. Cài đặt dependencies

```bash
docker exec -it laravel_app composer install
cd src
npm install
npm run dev
```

### 5. Generate application key

```bash
docker exec -it laravel_app php artisan key:generate
```

### 6. Chạy database migration & seeder

```bash
docker exec -it laravel_app php artisan migrate
```

```bash
docker exec -it laravel_app php artisan db:seed --class=DatabaseSeeder
```

### 7. Truy cập ứng dụng

Mở trình duyệt và truy cập:

```
http://localhost:8088
```

---

## Cấu hình môi trường

Dưới đây là các biến môi trường quan trọng cần chỉnh sửa trong `src/.env` để phù hợp với Docker:

```env
APP_NAME="Campaign Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8088

# Database — khớp với docker-compose.yml
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=password

# Redis — khớp với docker-compose.yml
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue sử dụng Redis
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Mail (sử dụng Mailtrap cho development)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

> **Lưu ý quan trọng:** Trong Docker, `DB_HOST` phải là `db` (tên service) và `REDIS_HOST` phải là `redis` (tên service), **không phải** `127.0.0.1`.
