# Neakers Backend & Web Admin (Laravel 11)

Repositori ini berisi backend web server yang dibangun menggunakan **Laravel 11** dan **Supabase PostgreSQL Cloud**. Server ini bertugas melayani RESTful JSON API bagi klien mobile (Flutter) serta menyediakan antarmuka **Web Admin Dashboard** untuk pengelolaan data.

---

## 🛠️ Tech Stack & Database

*   **Backend Framework:** Laravel 11 (PHP 8.2+)
*   **Database:** Supabase Cloud PostgreSQL (Relational Database)
*   **Templating Engine:** Blade Templates (Responsive UI dengan Google Fonts & FontAwesome)
*   **API Architecture:** RESTful JSON API (Multipart form-data untuk upload file)

---

## 🌟 Fitur Utama

1.  **Dashboard Overview:** Menampilkan statistik penjualan total, jumlah pesanan, total produk sepatu, jumlah order pending, dan daftar transaksi terbaru.
2.  **Manajemen Sepatu (CRUD):** Tambah, edit, hapus, dan lihat data produk sepatu (termasuk kelola stok, multi-size checkbox, dan unggah foto produk).
3.  **Manajemen Transaksi (Order Validation):**
    *   Melihat resi bukti transfer pembayaran dari pelanggan (dilengkapi fitur klik untuk perbesar gambar).
    *   Menampilkan koordinat peta **GPS (Latitude, Longitude)** lokasi pengiriman pelanggan secara visual.
    *   Mengubah status pesanan menjadi **Approved** (Stok otomatis berkurang) atau **Rejected** (Stok kembali).
4.  **RESTful JSON API:** Menyediakan endpoint aman untuk registrasi, login, katalog sepatu, dan pengiriman order transaksi dari HP Android/browser.
5.  **CORS & Fallback Safe:** Dilengkapi konfigurasi CORS untuk memperbolehkan koneksi dari browser Chrome Flutter Web dan fallback data produk lokal (.json) jika koneksi database cloud mati.

---

## 📂 Struktur Folder Utama

```text
app/
├── Http/Controllers/   # Logic Controller (AdminController, ApiController)
├── Models/             # Eloquent Model (User, Shoe, Transaction, TransactionItem)
config/
├── database.php        # Konfigurasi pgsql untuk koneksi Supabase
├── cors.php            # Konfigurasi Cross-Origin Resource Sharing (CORS)
database/migrations/    # File skema tabel database (users, shoes, transactions)
public/
├── uploads/            # Direktori penyimpanan file lokal (shoes, receipts)
├── sneakers_dummy.json # File fallback produk lokal
routes/
├── web.php             # Rute halaman Admin Dashboard & CRUD
└── api.php             # Rute RESTful JSON API untuk HP Flutter
```

---

## 🚀 Panduan Instalasi & Menjalankan Server

### 1. Prasyarat
*   PHP Versi **8.2** atau lebih baru.
*   Composer (PHP Package Manager).
*   Driver database PostgreSQL (`pgsql` dan `pdo_pgsql`) sudah aktif di file `php.ini` Anda.

### 2. Mengambil Dependensi
Masuk ke folder `sepatu_backend` melalui terminal, lalu jalankan:
```bash
composer install --no-security-blocking
```

### 3. Konfigurasi Lingkungan (.env)
Buat atau edit berkas **`.env`** di root folder dan masukkan koneksi Supabase PostgreSQL Anda:
```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.xairnmebbevxxtdeaegd
DB_PASSWORD=sneakerjogja123
DB_SCHEMA=public
```

### 4. Menyiapkan Folder Cache & Storage
Laravel memerlukan folder-folder berikut ada dan memiliki hak akses tulis. Jika belum ada, buat foldernya secara manual:
*   `bootstrap/cache/`
*   `storage/app/public/`
*   `storage/framework/cache/data/`
*   `storage/framework/sessions/`
*   `storage/framework/views/`
*   `storage/logs/`

### 5. Jalankan Migrasi & Seed Database
Jalankan perintah berikut untuk mengeksekusi migrasi tabel dan mengisi data katalog produk awal ke database Supabase:
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
*Web Admin Anda sekarang aktif di alamat **`http://localhost:8000/admin`** dengan data masuk:*
*   **Email:** `admin@solesteps.com`
*   **Password:** `admin123`
