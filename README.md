# SIPADU

Implementasi awal sistem peminjaman auditorium UPN Veteran Jakarta berbasis web menggunakan Laravel 12, Blade, dan MySQL.

## Persyaratan

Sebelum menjalankan aplikasi, pastikan perangkat telah memiliki:

- PHP 8.2 atau versi yang lebih baru
- Composer 2
- MySQL 8 atau MariaDB

Ekstensi PHP yang diperlukan:

- `pdo_mysql`
- `mbstring`
- `openssl`
- `tokenizer`
- `xml`
- `ctype`
- `fileinfo`

## Instalasi

### 1. Buka Project

Clone repository atau ekstrak project, kemudian buka folder project menggunakan VS Code.

```bash
cd sipadu
```

### 2. Install Dependencies

Jalankan perintah berikut:

```bash
composer install
```

### 3. Buat File Environment

Buat file `.env` berdasarkan file `.env.example`.

**Windows PowerShell:**

```powershell
Copy-Item .env.example .env
```

Kemudian generate application key:

```bash
php artisan key:generate
```

### 4. Buat Database

Buat database MySQL dengan nama:

```text
sipadu
```

Kemudian sesuaikan konfigurasi database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipadu
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL pada perangkat yang digunakan.

### 5. Jalankan Migration dan Seeder

Jalankan perintah berikut:

```bash
php artisan migrate --seed
```

Perintah ini akan membuat seluruh tabel database sekaligus mengisi data awal aplikasi.

### 6. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

## Akun Admin Awal

Gunakan akun berikut untuk login sebagai admin:

- **Email:** `admin@sipadu.test`
- **Password:** `Password123!`

> **Catatan:** Password admin sebaiknya segera diganti sebelum aplikasi digunakan atau dipublikasikan.

Peminjam dapat membuat akun baru melalui menu **Daftar**.

## Fitur yang Tersedia

### Landing Page

- Hero slideshow
- Informasi alur peminjaman
- SOP
- FAQ
- Contact person

### Autentikasi dan Otorisasi

- Registrasi
- Login
- Logout
- Pembatasan akses berdasarkan role

### Dashboard

- Dashboard peminjam
- Dashboard admin
- Informasi dalam bentuk scorecard

### Peminjaman Auditorium

- Pengajuan peminjaman auditorium
- Pemeriksaan jadwal yang bentrok dengan peminjaman berstatus `Disetujui`
- Pembatalan pengajuan selama status masih `Pending`
- Riwayat pengajuan peminjaman

### Pengelolaan Pengajuan

Admin dapat:

- Menyetujui pengajuan
- Menolak pengajuan
- Melihat riwayat keputusan

### Kalender

Kalender menampilkan jadwal peminjaman auditorium dengan status **Disetujui**.

### Pengelolaan Data

Admin dapat mengelola:

- Informasi alur
- SOP
- FAQ
- Contact person
- Data auditorium
- Data pengguna

### Profil

- Profil admin
- Profil peminjam

## Catatan Implementasi

- Tampilan menggunakan CSS lokal pada:

  ```text
  public/css/sipadu.css
  ```

- Aplikasi tidak menggunakan proses build Vite agar lebih mudah dijalankan sebagai implementasi awal atau MVP.

- Foto pada bagian hero menggunakan gambar ilustrasi. Untuk penggunaan produksi, gambar perlu diganti dengan foto resmi yang telah mendapatkan izin penggunaan.

- Informasi mengenai kapasitas auditorium, fasilitas, daftar sesi, contact person, dan aturan operasional perlu dikonfirmasi kepada pihak pengelola sebelum digunakan dalam lingkungan produksi.

- Kalender saat ini menampilkan peminjaman yang telah disetujui berdasarkan bulan.

- Kebijakan sesi, seperti sesi pagi, siang, dan malam, belum diimplementasikan karena daftar sesi resmi belum tersedia.

- Menu pengelolaan pengguna saat ini masih berupa daftar pengguna.

## Pengembangan Selanjutnya

Beberapa fitur yang dapat dikembangkan pada iterasi berikutnya:

- CRUD pengguna
- Ekspor laporan
- Verifikasi email
- Reset password
- Audit log
- Rate limiting
- HTTPS
- Backup database
- Kebijakan otorisasi yang lebih rinci
- Pengelolaan sesi peminjaman
- Notifikasi terkait status pengajuan

## SIPADU Capstone

SIPADU merupakan implementasi awal sistem informasi peminjaman auditorium berbasis web yang dikembangkan sebagai bagian dari proyek **Capstone**. Sistem ini dirancang untuk membantu proses pengajuan, pengelolaan, dan pemantauan peminjaman auditorium secara lebih terstruktur.
