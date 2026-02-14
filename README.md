# 🎫 Ticketing Apps - Laravel 11 Application

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Aplikasi manajemen ticket dengan role-based access control yang lengkap dan fleksibel.</strong>
</p>

---

## 📋 Tentang Project

**Ticketing Apps** adalah aplikasi manajemen ticket berbasis Laravel 11 dengan sistem role dan permission yang fleksibel menggunakan Spatie Laravel Permission. Aplikasi ini memungkinkan organisasi untuk mengelola ticket support dengan workflow yang terstruktur dan permission yang dapat dikustomisasi per role.

Aplikasi ini menggunakan **Repository Pattern** untuk maintainability yang lebih baik dan sudah dikonfigurasikan dengan PostgreSQL sebagai database.

---

## 🛠 Tech Stack

- **Framework**: Laravel 11.x
- **PHP Version**: 8.2+
- **Database**: PostgreSQL
- **Frontend**: Blade Templates + Bootstrap
- **Authentication**: Laravel built-in Auth
- **Authorization**: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Pattern**: Repository Pattern

---

## ✨ Fitur Utama

### 🔐 Sistem Autentikasi
- Login dengan email & password
- Forgot Password dengan email reset
- Session management dengan CSRF protection

### 🎫 Manajemen Ticket
- CRUD Ticket lengkap (Create, Read, Update, Delete)
- Status workflow: Open → In Progress → Resolved → Closed
- Priority levels: Low, Medium, High, Urgent
- Assignment ticket ke user tertentu
- Due date tracking
- Take ticket functionality untuk Admin/Support
- Ticket confirmation workflow (Reopen/Close)

### 👥 Manajemen User
- CRUD User dengan soft delete
- Password validation (min 8 karakter + minimal 1 angka)
- Profile management

### 🔑 Role & Permission System
- **Admin Role**: Full access ke semua fitur
- **Support Role**: Lihat semua ticket, take ticket, update status
- **User Role**: Lihat & edit ticket sendiri, konfirmasi ticket resolved
- Permission-based access control (bukan hardcoded)
- Flexible permission configuration per role

---

## 🎭 Role-Based Access Control

### Admin
- ✅ Full access ke semua ticket
- ✅ Create, edit, delete ticket
- ✅ Assign ticket ke user lain
- ✅ Take ticket yang belum assigned
- ✅ Update semua field ticket
- ✅ View semua ticket termasuk yang closed

### Support
- ✅ View semua ticket
- ✅ Take ticket yang belum assigned
- ✅ Update status ticket
- ✅ Permission delete/edit **configurable** (bisa diatur via role permission)
- ✅ View ticket closed

### User
- ✅ View hanya ticket yang dibuat sendiri
- ✅ Create ticket baru
- ✅ Edit ticket sendiri (title, description, priority, due date) **hanya saat status open**
- ✅ Reopen ticket jika status resolved (jika belum puas dengan solusi)
- ✅ Close ticket jika status resolved (konfirmasi masalah selesai)
- ❌ Tidak bisa edit ticket saat status in_progress, resolved, atau closed
- ❌ Tidak bisa melihat ticket user lain

---

## 📊 Ticket Workflow

```
📝 User Create Ticket
    ↓
[OPEN] - Ticket baru dibuat
    ↓
🔧 Admin/Support Take Ticket
    ↓
[IN PROGRESS] - Sedang dikerjakan
    ↓
✅ Admin/Support Resolved
    ↓
[RESOLVED] - Menunggu konfirmasi user
    ↓
┌─────────────┬─────────────┐
│             │             │
🔄 User        ✅ User
Reopen        Close
│             │
↓             ↓
[IN PROGRESS] [CLOSED]
              (Read-only)
```

---

## 🚀 Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- PostgreSQL
- Laravel 11.x

### Langkah Instalasi

1. **Clone project**
   ```bash
   git clone <repository-url>
   cd ticketing-apps
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database di `.env`**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=ticketing_apps
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

5. **Buat database**
   ```sql
   CREATE DATABASE ticketing_apps;
   ```

6. **Jalankan migration & seeder**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```

8. **Akses aplikasi**
   - URL: `http://localhost:8000`
   - Login dengan kredensial default

---

## 👤 Default Users

Setelah menjalankan seeder (`RoleAndPermissionSeeder`), tersedia 3 user default:

### Admin
```
Email: admin@email.com
Password: password
Role: Admin
Access: Full access
```

### Support Staff
```
Email: support@email.com
Password: password
Role: Support
Access: View all tickets, take tickets, update status
```

### Regular User
```
Email: user@email.com
Password: password
Role: User
Access: View/edit own tickets only
```

---

## 🔑 Default Permissions

### Modules
- **users**: users-read, users-create, users-update, users-delete
- **roles**: roles-read, roles-create, roles-update, roles-delete
- **tickets**: tickets-read, tickets-create, tickets-update, tickets-delete

### Role Permission Matrix

| Permission | Admin | Support | User |
|-----------|-------|---------|------|
| tickets-read | ✅ | ✅ | ✅ |
| tickets-create | ✅ | ✅ | ✅ |
| tickets-update | ✅ | ✅ | ✅ |
| tickets-delete | ✅ | ❌ | ❌ |
| users-* | ✅ | ❌ | ❌ |
| roles-* | ✅ | ❌ | ❌ |

**Note**: Permission Support dan User bisa diubah via halaman Role Management!

---

## 📁 Struktur Direktori

```
ticketing-apps/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   │   └── AuthController.php
│   │   └── Cms/
│   │       ├── DashboardController.php
│   │       ├── UserController.php
│   │       ├── RoleController.php
│   │       └── TicketController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Ticket.php
│   └── Repositories/
│       ├── Interfaces/
│       │   ├── UserRepositoryInterface.php
│       │   ├── RoleRepositoryInterface.php
│       │   └── TicketRepositoryInterface.php
│       └── Implementations/
│           ├── UserRepository.php
│           ├── RoleRepository.php
│           └── TicketRepository.php
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_tickets_table.php
│   │   └── create_permission_tables.php
│   └── seeders/
│       ├── AdminUserSeeder.php
│       └── RoleAndPermissionSeeder.php
└── resources/views/
    ├── auth/
    └── cms/
        ├── users/
        ├── roles/
        └── tickets/
```

---

## 🎯 Fitur Unggulan

### 1. Flexible Permission System
Permission tidak hardcoded! Admin bisa mengatur permission per role via UI:
- Support bisa diberi permission `tickets-delete` jika diperlukan
- User bisa diberi lebih banyak akses sesuai kebutuhan
- Semua via konfigurasi di halaman Role & Permission

### 2. Ticket Confirmation Workflow
- Saat Support/Admin set status → **Resolved**
- User menerima notifikasi untuk konfirmasi
- User bisa:
  - **Reopen** jika masalah belum fix → balik ke In Progress
  - **Close** jika masalah sudah selesai → status final (read-only)

### 3. Read-only Closed Tickets
- Ticket yang sudah **Closed** tidak bisa diedit siapapun
- Tersedia tombol **"View Ticket"** untuk melihat detail
- Creator dan Admin/Support bisa view ticket closed

### 4. Take Ticket Feature
- Admin/Support bisa "claim" ticket yang belum assigned
- Button "Take Ticket" akan:
  - Assign ticket ke user yang klik
  - Ubah status ke "In Progress"

---

## 📝 Cara Pakai

### Sebagai User (Creator Ticket)

1. **Create Ticket**
   - Klik "Create Ticket"
   - Isi title, description, priority, due date
   - Submit

2. **Edit Ticket**
   - Hanya bisa edit saat status = **Open**
   - Bisa edit: title, description, priority, due date
   - Tidak bisa edit: status, assigned to

3. **Konfirmasi Ticket Resolved**
   - Saat Support/Admin resolve ticket
   - Klik **"Reopen"** jika masalah belum selesai
   - Klik **"Close"** jika masalah sudah selesai

### Sebagai Support

1. **Take Ticket**
   - Lihat list ticket
   - Klik "Take Ticket" untuk assign ke diri sendiri
   - Ticket otomatis status → In Progress

2. **Update Status**
   - Edit ticket
   - Update status: Open → In Progress → Resolved
   - Save

### Sebagai Admin

1. **Full Control**
   - Create, edit, delete ticket
   - Assign ticket ke user tertentu
   - Set semua field termasuk status
   - View/manage semua ticket

2. **Manage Permissions**
   - Buka menu Roles
   - Edit role (Support/User)
   - Checklist permission sesuai kebutuhan
   - Save

---

## 🔧 Kustomisasi

### Menambah Permission ke Role

1. Login sebagai Admin
2. Menu **Roles** → Edit role yang ingin diubah
3. Checklist permission yang diinginkan
4. Save

**Contoh**: Memberi Support akses delete ticket
- Edit role "Support"
- Checklist "Delete" di module Tickets
- Save
- Support sekarang bisa delete ticket!

### Menambah Custom Role

1. Menu **Roles** → Create New
2. Isi nama role (contoh: "Manager")
3. Pilih permission yang diinginkan
4. Save
5. Assign role ke user via User Management

---

## 🐛 Troubleshooting

### Permission tidak update
```bash
php artisan cache:clear
php artisan config:clear
```

### Migration error
```bash
php artisan migrate:fresh --seed
```

### Route 404 saat create ticket
Pastikan route order sudah benar (route spesifik di atas route dengan parameter)

---

## 📄 License

Project ini menggunakan framework Laravel yang berlisensi [MIT license](https://opensource.org/licenses/MIT).

---

**Happy Ticketing! �✨**
