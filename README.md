# 🚀 Base App - Laravel 11 Application

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Aplikasi dasar Laravel dengan fitur autentikasi, manajemen user, dan sistem role & permission yang lengkap.</strong>
</p>

---

## 📋 Tentang Project

**Base App** adalah aplikasi web berbasis Laravel 11 yang dirancang sebagai template dasar untuk memulai project baru. Aplikasi ini sudah dilengkapi dengan fitur-fitur esensial seperti sistem autentikasi, manajemen user, dan role-based access control menggunakan Spatie Laravel Permission.

Project ini menggunakan **Repository Pattern** untuk memudahkan maintainability dan testing, serta sudah dikonfigurasikan dengan PostgreSQL sebagai database utama.

---

## 🛠 Tech Stack

- **Framework**: Laravel 11.x
- **PHP Version**: 8.2+
- **Database**: PostgreSQL (default), support MySQL/SQLite
- **Frontend**: Blade Templates (raw HTML)
- **Authentication**: Laravel built-in Auth
- **Authorization**: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Session Driver**: Database
- **Cache Driver**: Database
- **Queue Connection**: Database

---

## ✨ Fitur Utama

### 🔐 Sistem Autentikasi
- **Login** dengan validasi email & password
- **Forgot Password** dengan token reset
- **Logout** dengan session invalidation
- CSRF Protection & Password Hashing
- Session regeneration untuk keamanan

### 👥 Manajemen User
- CRUD User lengkap (Create, Read, Update, Delete)
- Validasi password dengan aturan:
  - Minimal 8 karakter
  - Harus mengandung minimal 1 angka
- Soft Delete untuk data user
- Profile management untuk user yang sedang login

### 🔑 Role & Permission
- Role-based access control menggunakan Spatie Laravel Permission
- Permission per module (Read, Create, Update, Delete)
- Seeder untuk role & permission default

---

## 📁 Struktur Direktori

```
base-app/
├── app/
│   ├── Helpers/                    # Helper functions
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/               # Authentication controllers
│   │       │   └── AuthController.php
│   │       └── Cms/                # CMS controllers
│   │           ├── DashboardController.php
│   │           └── UserController.php
│   ├── Mail/                       # Mail classes
│   ├── Models/                     # Eloquent models
│   │   └── User.php
│   ├── Providers/
│   │   └── RepositoryServiceProvider.php
│   └── Repositories/
│       ├── Interfaces/             # Repository interfaces
│       │   ├── AuthRepositoryInterface.php
│       │   └── UserRepositoryInterface.php
│       └── Implementations/        # Repository implementations
│           ├── AuthRepository.php
│           └── UserRepository.php
├── database/
│   ├── migrations/                 # Database migrations
│   │   ├── create_users_table.php
│   │   ├── create_cache_table.php
│   │   ├── create_jobs_table.php
│   │   └── create_permission_tables.php
│   └── seeders/                    # Database seeders
│       ├── AdminUserSeeder.php     # Admin user default
│       ├── PermissionSeeder.php    # Permissions & roles
│       └── UserSeeder.php          # Sample users
├── resources/
│   └── views/
│       ├── auth/                   # Authentication views
│       │   ├── login.blade.php
│       │   └── forgot.blade.php
│       └── cms/                    # CMS views
│           ├── layouts/
│           ├── users/
│           └── profile/
└── routes/
    └── web.php                     # Web routes
```

---

## 🚀 Instalasi

### Prasyarat
Pastikan sistem Anda sudah terinstall:
- PHP >= 8.2
- Composer
- PostgreSQL
- Node.js & NPM (opsional, untuk frontend assets)

### Langkah Instalasi

1. **Clone atau download project ini**
   ```bash
   cd c:\laragon\www\base-app
   ```

2. **Install dependencies PHP**
   ```bash
   composer install
   ```

3. **Copy file environment**
   ```bash
   copy .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Konfigurasi database di `.env`**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=base-app
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

6. **Buat database PostgreSQL**
   ```sql
   CREATE DATABASE "base-app";
   ```

7. **Jalankan migration**
   ```bash
   php artisan migrate
   ```

8. **Jalankan seeder (untuk generate data default)**
   ```bash
   php artisan db:seed
   ```
   
   Jalankan seeder ini untuk seed data user:
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

9. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```

10. **Akses aplikasi**
    - URL: `http://localhost:8000`
    - Email: `admin@email.com`
    - Password: `12345678`

---

## 👤 Default Users (Setelah Seeding)

Setelah menjalankan seeder, Anda dapat login dengan kredensial berikut:

### Admin User
```
Email: admin@email.com
Password: password
Role: Admin
```

---

## 🔑 Default Roles & Permissions

### Roles
- **Admin**: Full access ke semua fitur

### Permission Modules
- **users**: users.read, users.create, users.update, users.delete
- **roles**: roles.read, roles.create, roles.update, roles.delete
- Dapat ditambahkan sesuai kebutuhan di `PermissionSeeder.php`

---

## 📝 Catatan Pengembangan

### Repository Pattern
Project ini menggunakan Repository Pattern dengan struktur:
- **Interface**: Mendefinisikan kontrak (di `app/Repositories/Interfaces/`)
- **Implementation**: Implementasi konkret (di `app/Repositories/Implementations/`)
- **Service Provider**: Binding interface ke implementation (di `RepositoryServiceProvider.php`)

### Validasi Password
Password harus memenuhi kriteria:
- Minimal 8 karakter
- Mengandung minimal 1 angka
- Validasi berlaku di create dan update user

### Raw HTML Views
Semua view menggunakan raw HTML tanpa styling framework. Anda bebas menambahkan Bootstrap, Tailwind CSS, atau framework CSS lainnya sesuai kebutuhan project.

### Session & Cache
- Session disimpan di database untuk skalabilitas
- Cache menggunakan database driver
- Queue connection menggunakan database

---

## 🤝 Contributing

Silakan buat pull request atau issue untuk improvement atau bug fixes.

---

## 📄 License

Project ini menggunakan framework Laravel yang berlisensi [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support

Jika ada pertanyaan atau kendala, silakan buat issue di repository ini.

---

**Happy Coding! 🎉**
