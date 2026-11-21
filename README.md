# SIKAP DPD RI

**Sistem Informasi Keluhan Administrasi Presensi**  
Sekretariat Jenderal Dewan Perwakilan Daerah Republik Indonesia

---

## Deskripsi Aplikasi

SIKAP DPD RI adalah sistem manajemen pengaduan kehadiran (presensi) elektronik yang dirancang khusus untuk pegawai di lingkungan Sekretariat Jenderal DPD RI. Aplikasi ini memungkinkan pegawai untuk mengajukan, melacak, dan menyelesaikan keluhan terkait ketidaksesuaian data presensi mereka.

## Navigasi Utama (3 Menu)

1. **Dashboard** — Ringkasan statistik dan informasi terkini sesuai peran pengguna
2. **Pengaduan Presensi** — Pengajuan, pengelolaan, dan pelacakan pengaduan presensi
3. **Profil** — Informasi pengguna dari SSO dan pengaturan akun (manajemen pengguna untuk Admin)

## Peran Pengguna

| Peran | Akses |
|-------|-------|
| **Pegawai** | Mengajukan pengaduan, melacak status, melihat riwayat |
| **Administrator** | Verifikasi manual, mengelola pengaduan, respons & perubahan status |
| **Manajemen** | Monitoring, laporan, statistik pengaduan |

---

## Persyaratan Sistem

- PHP >= 8.1
- Composer >= 2.x
- MySQL >= 5.7 / MariaDB >= 10.3
- Laravel 10.x
- Node.js >= 16 (untuk asset build, opsional)

---

## Instalasi

### 1. Clone / Ekstrak Proyek

```bash
cd /path/ke/server
# Ekstrak atau clone repositori ke folder ini
```

### 2. Install Dependensi PHP

```bash
composer install --optimize-autoloader
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` sesuai konfigurasi lokal:

```env
APP_NAME="SIKAP DPD RI"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikap_dpd
DB_USERNAME=root
DB_PASSWORD=your_password

# Mode autentikasi: 'dev' untuk pengembangan, 'production' untuk SSO institusional
SSO_MODE=dev
```

### 4. Buat Database

```sql
CREATE DATABASE sikap_dpd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate --seed
```

### 6. Buat Symlink Storage

```bash
php artisan storage:link
```

### 7. Akses Aplikasi

Ada **tiga cara** untuk menjalankan aplikasi, pilih sesuai setup server Anda:

---

#### Cara A — XAMPP / Laragon / WAMP (Paling Umum)

Letakkan folder `E-Presensi` di dalam folder htdocs (XAMPP) atau www (Laragon):

```
C:\xampp\htdocs\E-Presensi\   ← XAMPP
C:\laragon\www\E-Presensi\    ← Laragon
C:\wamp64\www\E-Presensi\     ← WAMP
```

Lalu akses di browser:

```
http://localhost/E-Presensi
```

> File `index.php` di root folder dan `.htaccess` sudah disiapkan untuk meneruskan
> semua request ke Laravel secara otomatis.
>
> **Pastikan `mod_rewrite` aktif di Apache.**
> Di XAMPP: buka `httpd.conf`, pastikan baris `LoadModule rewrite_module ...` tidak dikomentari.

---

#### Cara B — PHP Built-in Server (Tanpa Web Server)

```bash
php -S localhost:8000 -t public
```

Akses di: **http://localhost:8000**

---

#### Cara C — `php artisan serve`

```bash
php artisan serve
```

Akses di: **http://localhost:8000**

---

> **Catatan APP_URL:** Sesuaikan `APP_URL` di file `.env` dengan cara akses yang dipilih:
> - Cara A (XAMPP): `APP_URL=http://localhost/E-Presensi`
> - Cara B/C (built-in): `APP_URL=http://localhost:8000`

---

## Akun Demo (Mode Pengembangan)

Saat `SSO_MODE=dev`, gunakan akun berikut:

| Peran | NIP | Kata Sandi |
|-------|-----|-----------|
| Pegawai | 198001012005011001 | `pegawai123` |
| Pegawai | 197505152000031002 | `pegawai123` |
| Administrator | 196803251995121001 | `admin123` |
| Manajemen | 197012101994031003 | `manajemen123` |

> **Catatan:** Akun-akun ini tersedia hanya untuk pengujian di lingkungan pengembangan. Pada lingkungan produksi, autentikasi menggunakan SSO institusional DPD RI.

---

## Autentikasi SSO

Aplikasi ini dirancang untuk menggunakan SSO institusional DPD RI pada lingkungan produksi. Saat ini berjalan dalam **mode pengembangan** (`SSO_MODE=dev`) menggunakan adapter lokal.

Untuk mengaktifkan SSO produksi:

1. Set `SSO_MODE=production` di `.env`
2. Isi `SSO_URL`, `SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, dan `SSO_REDIRECT_URI`
3. Implementasikan `App\Services\SsoAuthService` sesuai dokumentasi SSO DPD RI

---

## Status Pengaduan

| Status | Keterangan |
|--------|------------|
| Diajukan | Pengaduan baru diterima sistem |
| Menunggu Verifikasi | Diteruskan ke verifikator |
| Telah Diverifikasi | Verifikasi manual selesai |
| Sedang Diproses | Admin sedang menindaklanjuti |
| Menunggu Informasi Tambahan | Memerlukan data/dokumen tambahan dari pegawai |
| Diselesaikan | Pengaduan telah diselesaikan |
| Ditutup | Pengaduan ditutup |
| Ditolak | Pengaduan tidak valid/ditolak |

---

## Prinsip Desain

- ✅ Navigasi minimal: **hanya 3 menu utama**
- ✅ Role-based access control: konten berbeda per peran
- ✅ Verifikasi manual: tidak ada integrasi langsung ke database e-Presensi
- ✅ Tidak ada modifikasi otomatis data presensi resmi
- ✅ Semua pengaduan masuk antrian administrator pusat
- ✅ Responsif untuk desktop dan mobile
- ✅ Bahasa Indonesia

---

## Teknologi

- **Backend:** Laravel 10, PHP 8.1+
- **Frontend:** Bootstrap 5.3, Bootstrap Icons
- **Database:** MySQL/MariaDB
- **Autentikasi:** Laravel Session Auth + Dev SSO Adapter

---

&copy; 2024 Sekretariat Jenderal DPD RI — SIKAP v1.0
