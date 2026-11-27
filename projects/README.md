# Garuda-Hub — Laravel MVC

Aplikasi lelang proyek IT hasil refactoring dari PHP Native ke Laravel 13.
Tersedia role `klien`, `developer`, dan `admin`.

## Fitur

- Klien membuat, melihat, mengubah, dan menghapus proyek sendiri sebelum ada pemenang.
- Developer membuat dan mengelola penawaran sendiri selama lelang terbuka.
- Klien pemilik proyek memilih satu pemenang; proyek otomatis ditutup.
- Admin melihat statistik, mengganti role pengguna, menghapus pengguna, dan menghapus proyek.
- Login, register, logout, validasi server, proteksi CSRF, Eloquent ORM, dan pagination.

## Persiapan lokal

Syarat: PHP 8.3+, Composer, MariaDB/MySQL, dan ekstensi PHP yang dibutuhkan Laravel.

Dari folder `projects`:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Buat database MariaDB kosong, misalnya `garudaHub`. Sesuaikan pengaturan `DB_*` di `.env` dengan akun database lokal.
Jangan commit `.env` atau password ke GitHub.

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

Buka `http://127.0.0.1:8000/`.
Seeder memuat akun, proyek, dan penawaran contoh. Password asli akun dummy tidak dicantumkan karena tersimpan sebagai hash.
Buat akun klien atau developer lewat Register atau atur password testing melalui Tinker hanya pada lingkungan lokal.

**Jangan gunakan `migrate:fresh` pada database berisi data penting**, karena perintah itu menghapus tabel.

## Menjalankan pengujian

Secara default `phpunit.xml` memakai SQLite in-memory untuk pengujian dan tidak menghapus database MariaDB lokal.
Pastikan ekstensi `pdo_sqlite` terpasang, lalu jalankan:

```bash
php artisan test
```

## Struktur MVC

- `app/Models/`: User, Project, Bid serta relasi Eloquent.
- `app/Http/Controllers/`: controller autentikasi, proyek, penawaran, pemenang, dashboard, dan admin.
- `resources/views/`: halaman Blade.
- `routes/web.php`: daftar route dan middleware autentikasi.
- `database/migrations/`: struktur tabel relasional.
- `database/seeders/`: data dummy.
- `tests/Feature/`: pengujian fitur dan otorisasi.

## Catatan keamanan

Jalankan di localhost untuk tugas kuliah. Sebelum deployment, audit konfigurasi produksi, hak akses, pengamanan session, CSRF, XSS, serta lakukan pemindaian OWASP ZAP sesuai tugas keamanan berikutnya.
**Tugas 2 (The Phantom Vulnerability):** `GET /login` tidak lagi mengakhiri session. Logout hanya lewat `POST /logout` dengan CSRF. Output halaman menggunakan Blade `{{ }}` untuk HTML escaping, login menggunakan `Auth::attempt`, password disimpan sebagai hash, dan respons mendapatkan header keamanan tambahan. Panduan pengujian dan bukti OWASP ZAP ada di `../docs/tugas-2-keamanan.md`.
