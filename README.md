# Neakers Backend - Laravel Admin Dashboard & RESTful API

Repositori ini berisi backend berbasis **Laravel 11** yang bertindak sebagai pusat pengelolaan data (*Server-Side*) untuk aplikasi toko sepatu online **Neakers**. Backend ini mengelola cloud database, enkripsi keamanan, RESTful API, serta menyediakan dasbor admin untuk memantau transaksi pembelian.

---

## 🚀 Fitur Utama
1.  **Dashboard Admin (CRUD Sepatu):** Kelola data produk sepatu (tambah, edit, hapus, detail, stok, ukuran/sizes, deskripsi, dan upload gambar).
2.  **Manajemen Transaksi Pelanggan:** Dasbor khusus untuk memantau pesanan yang masuk dari aplikasi mobile (status *pending*, *approved*, *rejected*).
3.  **Integrasi Peta Visual (LeafletJS & OpenStreetMap):** Menampilkan peta lokasi pengiriman rumah pelanggan secara presisi pada dasbor admin berdasarkan koordinat GPS yang dikirim dari HP pembeli.
4.  **Bypass Validasi Fileinfo:** Sistem aman untuk penanganan unggahan file foto yang kompatibel di berbagai sistem XAMPP tanpa bergantung pada ekstensi PHP `fileinfo`.
5.  **RESTful API Services:** Menyediakan endpoint untuk registrasi, login, katalog produk, dan pembuatan transaksi yang dikonsumsi oleh aplikasi mobile Flutter.

---

## 🛠️ Spesifikasi Teknologi
*   **Framework Utama:** Laravel 11.x (PHP 8.2+)
*   **Database Cloud:** PostgreSQL (Hosted on Supabase Cloud)
*   **Mapping Library:** LeafletJS & OpenStreetMap (OSM)
*   **Keamanan Sandi:** Hash Enkripsi Bcrypt (Laravel Hash)

---

## 📥 Panduan Instalasi Lokal

1.  **Clone Repositori & Masuk Ke Direktori:**
    ```bash
    git clone https://github.com/USERNAME/sepatu_backend.git
    cd Resource_Backend
    ```

2.  **Instal Dependensi PHP (Composer):**
    ```bash
    composer install
    ```

3.  **Konfigurasi File Environment (`.env`):**
    Salin file `.env.example` menjadi `.env` lalu sesuaikan kredensial koneksi database PostgreSQL Supabase Anda:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
    DB_PORT=5432
    DB_DATABASE=postgres
    DB_USERNAME=postgres.YOUR_SUPABASE_PROJECT_ID
    DB_PASSWORD=YOUR_SUPABASE_PASSWORD
    ```

4.  **Migrasi Database & Seeding Data Awal:**
    Jalankan perintah berikut untuk membuat tabel-tabel di Supabase dan memasukkan akun admin serta produk sepatu default:
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Nyalakan Server Lokal:**
    Buka jaringan agar bisa diakses oleh HP (klien) dengan melakukan binding IP ke host `0.0.0.0`:
    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```
    *Server kini aktif di port `8000` dan siap menerima koneksi dari aplikasi mobile.*
