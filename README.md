# Integrated Project Management System

IPMS adalah aplikasi web berbasis Laravel untuk mengelola proyek, periode pelaporan, progress, aktivitas, isu, dokumentasi, procurement, tracking, dan export laporan.

## Teknologi

- PHP 8.2+
- Laravel 12
- MySQL
- Vite dan Tailwind CSS
- Dompdf dan PHPWord untuk export laporan

## Instalasi

```bash
git clone <repository-url>
cd IPMS
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Atur koneksi database pada `.env`, lalu jalankan:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan storage:link
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Akun demo

Semua akun demo menggunakan password `password`.

| Username | Email | Role | Status |
|---|---|---|---|
| `admin` | `admin@ipms.test` | Admin | Approved |
| `project.manager` | `project.manager@ipms.test` | User | Approved |
| `project.coordinator` | `project.coordinator@ipms.test` | User | Approved |
| `pending.user` | `pending.user@ipms.test` | User | Pending |

## Seeder

Seeder utama membuat data contoh untuk seluruh tabel aplikasi: user, project, period, activity, issue, documentation, EPC, engineering, construction, project progress, tracking, procurement material, dan procurement service.

Tabel default Laravel seperti cache, jobs, failed jobs, password reset tokens, dan sessions tidak diisi dengan data contoh.

Untuk mengulang database lokal:

```bash
php artisan migrate:fresh --seed
```

## Perintah pengembangan

```bash
php artisan serve
npm run dev
php artisan view:cache
vendor/bin/phpunit
```

## Struktur penting

- `app/Models` — model domain aplikasi
- `app/Http/Controllers` — controller dan alur request
- `database/migrations` — struktur database
- `database/seeders` — data contoh
- `resources/views` — halaman Blade
- `public/images` — logo dan background IPMS

## Lisensi

Gunakan dan sesuaikan sesuai kebijakan repository atau organisasi Anda.
