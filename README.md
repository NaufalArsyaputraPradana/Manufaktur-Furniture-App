# 🪑 Sistem Informasi Manajemen Manufaktur Furnitur

> **UD Bisa Furniture** — Aplikasi web berbasis Laravel untuk manajemen produksi dan penjualan furnitur secara terintegrasi.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap)](https://getbootstrap.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat&logo=mysql)](https://mysql.com)

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Struktur Database](#-struktur-database)
- [Alur Kerja Sistem](#-alur-kerja-sistem)
- [Peran Pengguna](#-peran-pengguna)
- [Menu & Navigasi](#-menu--navigasi)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Panduan Instalasi](#-panduan-instalasi)
- [Konfigurasi API Eksternal](#-konfigurasi-api-eksternal)
- [Struktur Proyek](#-struktur-proyek)
- [Keunggulan Sistem](#-keunggulan-sistem)

---

## �� Tentang Proyek

Sistem Informasi Manajemen Manufaktur Furnitur adalah aplikasi web yang dirancang untuk membantu usaha pembuatan furnitur (seperti UD Bisa Furniture) dalam mengelola seluruh proses bisnis secara digital — mulai dari pemesanan produk oleh pelanggan, proses produksi di lantai pabrik, hingga manajemen pembayaran dan pelaporan keuangan.

Sistem ini dikembangkan menggunakan **Laravel 11** dengan pola arsitektur **MVC (Model-View-Controller)** yang diperkuat dengan Repository Pattern, Service Layer, Observer Pattern, dan Policy-based Authorization, sehingga kode bersih, mudah diuji, dan mudah dikembangkan lebih lanjut.

### Latar Belakang

Banyak usaha manufaktur furnitur skala kecil-menengah masih mengelola pesanan, produksi, dan keuangan secara manual (kertas/spreadsheet), yang menyebabkan:
- Kesulitan memantau status pesanan secara real-time
- Risiko kesalahan perhitungan harga produk custom
- Tidak ada histori produksi yang terstruktur
- Pelaporan keuangan yang membutuhkan waktu lama

Sistem ini hadir sebagai solusi digital yang terintegrasikan untuk mengatasi semua permasalahan tersebut.

---

## ✨ Fitur Utama

### 🛒 Manajemen Produk & Katalog
- Katalog produk dengan gambar, kategori, deskripsi, dan dimensi
- Kategori produk bertingkat (parent-child)
- Sistem estimasi harga berdasarkan dimensi kustom (berbasis rasio volume)
- Filter produk berdasarkan kategori, nama, dan rentang harga
- Pagination dengan pencarian real-time

### 📦 Manajemen Pesanan
- Keranjang belanja berbasis sesi (session-based cart)
- Checkout dengan pilihan metode pembayaran (transfer, tunai, kartu kredit)
- Pembuatan pesanan custom (BOM — Bill of Materials calculator)
- Kalkulasi harga produk custom berbasis kubikasi kayu (Grade A/B/C)
- Pelacakan status pesanan real-time oleh pelanggan
- Pembatalan pesanan dengan validasi status

### 💳 Manajemen Pembayaran
- Integrasi webhook **Midtrans** untuk pembayaran otomatis
- Verifikasi pembayaran manual oleh admin
- Penolakan pembayaran dengan alasan
- Riwayat pembayaran per pesanan

### 🔧 Manajemen Produksi
- Dashboard produksi untuk staf lantai pabrik
- Pemantauan tahap produksi: Pending → Pemotongan → Perakitan → Penghalusan → Finishing → Quality Control → Selesai
- Pencatatan log produksi (production log)
- Manajemen jadwal produksi dengan tampilan kalender (FullCalendar compatible)
- Manajemen to-do list tugas produksi harian
- Export jadwal ke format `.ics` (Google Calendar / iCalendar)

### 📊 Laporan & Analitik
- Laporan keuangan bulanan dengan grafik
- Laporan penjualan per periode
- Laporan produksi (efisiensi, status proses)
- Laporan inventori (produk terlaris)
- Laporan profitabilitas
- Export laporan ke format **CSV**
- Arsip laporan tersimpan di database

### 👤 Manajemen Pengguna
- Registrasi & login dengan throttle (anti-brute force)
- Tiga peran pengguna: Admin, Pelanggan, Staf Produksi
- Manajemen pengguna oleh admin (CRUD lengkap)
- Profil pengguna dengan update password dan hapus akun
- Validasi agar akun dengan pesanan aktif tidak bisa dihapus

### 🌍 Fitur Internasional
- **Konversi Mata Uang** via ExchangeRate API (IDR ↔ USD)
- **Terjemahan** via Google Translate API
- Format tanggal dalam Bahasa Indonesia

### ⚙️ Pengaturan Sistem
- Konfigurasi nama & kontak website
- Pengaturan mata uang default (IDR/USD)
- Pengaturan zona waktu
- Mode pemeliharaan (maintenance mode)
- Notifikasi email (on/off)

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                        LAPISAN PRESENTASI                    │
│              Blade Views + Bootstrap 5.3 + Bootstrap Icons   │
└─────────────────────┬───────────────────────────────────────┘
                      │ HTTP Request
┌─────────────────────▼───────────────────────────────────────┐
│                    LAPISAN ROUTING (routes/web.php)           │
│  Middleware: auth, role:admin|customer|production_staff      │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                  LAPISAN CONTROLLER                           │
│  Admin/ | Customer/ | Production/ | Auth/                    │
└──────────┬────────────────┬────────────────┬────────────────┘
           │                │                │
┌──────────▼──────┐ ┌───────▼──────┐ ┌──────▼──────────────┐
│  SERVICE LAYER  │ │  REPOSITORY  │ │  FORM REQUESTS      │
│  PaymentService │ │  OrderRepo   │ │  Validasi Input     │
│  ProductionSvc  │ │  ProductRepo │ └─────────────────────┘
│  CurrencyConv.  │ └──────────────┘
│  GoogleTranslate│
│  QueryOptimize  │
└──────────┬──────┘
           │
┌──────────▼───────────────────────────────────────────────┐
│                    LAPISAN MODEL (Eloquent ORM)            │
│  User, Role, Order, OrderDetail, Payment, Product,        │
│  Category, ProductionProcess, ProductionLog,              │
│  ProductionTodo, ProductionSchedule, Report, Setting      │
│                                                           │
│  Observer: OrderObserver, ProductObserver,                │
│            ProductionProcessObserver                      │
│  Policy:   ProductionTodoPolicy,                          │
│            ProductionSchedulePolicy                       │
└──────────┬───────────────────────────────────────────────┘
           │
┌──────────▼───────────────────────────────────────────────┐
│                    DATABASE (MySQL)                        │
│  Cache Driver: database | Queue Driver: database          │
└──────────────────────────────────────────────────────────┘
```

### Pola Desain yang Digunakan

| Pola | Implementasi |
|------|-------------|
| **MVC** | Controller → Service/Repository → Model → View |
| **Repository Pattern** | `OrderRepository`, `ProductRepository` — abstraksi akses data |
| **Service Layer** | `PaymentService`, `ProductionService`, `CurrencyConversionService`, dll |
| **Observer Pattern** | `OrderObserver` — otomatis invalidasi cache saat data order berubah |
| **Policy Authorization** | `ProductionTodoPolicy`, `ProductionSchedulePolicy` — otorisasi berbasis kepemilikan |
| **Form Request** | Validasi input terpisah dari logika controller |

---

## 🗄️ Struktur Database

### Diagram Relasi Antar Tabel

```
roles ─────────────┐
                   │ 1:N
users ─────────────┴───────────────────┐
  │                                    │
  │ 1:N (orders.user_id)               │ 1:N (reports.generated_by)
  ▼                                    ▼
orders ──────────────────────────── reports
  │
  ├─── 1:N ──► order_details ──► products ──► categories
  │                  │
  │                  └── (product_id, product_name, quantity,
  │                        unit_price, subtotal, is_custom,
  │                        custom_specifications)
  │
  ├─── 1:1 ──► payments
  │
  └─── 1:1 ──► production_processes
                    │
                    └─── 1:N ──► production_logs

users ──── 1:N ──► production_todos    (per staf, user_id)
users ──── 1:N ──► production_schedules (per staf, user_id)
```

### Tabel & Kolom Utama

#### `roles`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| name | varchar(50) | `admin`, `customer`, `production_staff` |
| display_name | varchar(100) | Nama tampilan |
| description | text nullable | |

#### `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| role_id | FK → roles | |
| name | varchar(255) | |
| email | varchar(255) unique | |
| password | varchar(255) | Bcrypt hash |
| phone | varchar(20) nullable | |
| address | text nullable | |
| is_active | boolean | Default: true |

#### `categories`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| parent_id | FK → categories nullable | Kategori bertingkat |
| name | varchar(100) unique | |
| slug | varchar(100) unique | |
| description | text nullable | |
| image | varchar(255) nullable | Path file |
| is_active | boolean | Default: true |

#### `products`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| category_id | FK → categories | |
| name | varchar(255) | |
| slug | varchar(255) unique | |
| description | text nullable | |
| base_price | decimal(15,2) | Harga dasar |
| sku | varchar(50) unique nullable | Stock Keeping Unit |
| dimensions | varchar(100) nullable | Format: `WxHxD` cm |
| images | JSON nullable | Array path gambar |
| is_active | boolean | Default: true |

#### `orders`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | FK → users | |
| order_number | varchar(50) unique | Format: `ORD-YYYYMMDD-XXXXX` |
| status | enum | `pending`, `confirmed`, `in_production`, `completed`, `cancelled` |
| subtotal | decimal(15,2) | |
| total | decimal(15,2) | |
| shipping_address | text | |
| customer_notes | text nullable | |
| expected_completion_date | date nullable | |

#### `order_details`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| order_id | FK → orders | |
| product_id | FK → products nullable | Null jika produk dihapus |
| product_name | varchar(255) | Snapshot nama produk saat order |
| quantity | int | Min: 1 |
| unit_price | decimal(15,2) | |
| subtotal | decimal(15,2) | |
| is_custom | boolean | Default: false |
| custom_specifications | JSON nullable | Data spesifikasi & BOM custom |

#### `payments`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| order_id | FK → orders unique | Satu order satu pembayaran |
| payment_method | varchar(50) | `transfer`, `cash`, `midtrans`, dll |
| payment_status | enum | `pending`, `paid`, `failed`, `expired` |
| amount | decimal(15,2) | |
| transaction_id | varchar(100) nullable | ID dari Midtrans |
| payment_proof | varchar(255) nullable | Path bukti transfer |
| midtrans_response | JSON nullable | Raw response Midtrans |
| verified_by | FK → users nullable | Admin yang memverifikasi |
| verified_at | timestamp nullable | |
| notes | text nullable | |

#### `production_processes`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| order_id | FK → orders unique | |
| status | enum | `pending`, `cutting`, `assembly`, `sanding`, `finishing`, `quality_control`, `completed` |
| started_at | timestamp nullable | |
| completed_at | timestamp nullable | |
| notes | text nullable | |
| assigned_to | FK → users nullable | Staf yang ditugaskan |

#### `production_logs`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| production_process_id | FK → production_processes | |
| action | varchar(100) | Aksi yang dicatat |
| description | text nullable | |
| user_id | FK → users | Yang mencatat |
| metadata | JSON nullable | Data tambahan |

#### `production_todos`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | FK → users | Pemilik tugas |
| title | varchar(191) | |
| description | text nullable | |
| status | enum | `pending`, `in_progress`, `done`, `cancelled` |
| deadline | date nullable | |
| deleted_at | timestamp nullable | Soft delete |

#### `production_schedules`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | FK → users | Pemilik jadwal |
| title | varchar(191) | |
| description | text nullable | |
| start_datetime | datetime | |
| end_datetime | datetime | |
| location | varchar(255) nullable | |
| deleted_at | timestamp nullable | Soft delete |

#### `settings`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| key | varchar(100) unique | |
| value | text nullable | |

#### `reports`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| report_type | varchar(50) | `sales`, `production`, `inventory`, `profitability` |
| title | varchar(255) | |
| start_date | date | |
| end_date | date | |
| data | JSON | Data hasil komputasi laporan |
| generated_by | FK → users | Admin yang membuat |

---

## 🔄 Alur Kerja Sistem

### 1. Alur Pemesanan Produk Standar

```
Pelanggan                  Sistem                     Admin
    │                         │                          │
    ├──[Browse Katalog]───────►│                          │
    ├──[Tambah ke Keranjang]──►│                          │
    ├──[Checkout]─────────────►│                          │
    │                         ├──[Buat Order (pending)]──►│
    │                         │                    ├──[Konfirmasi Order]
    │                         │◄──[Status: confirmed]──────┤
    │◄──[Notifikasi]──────────┤                          │
    ├──[Upload Bukti Bayar]───►│                          │
    │                         │                    ├──[Verifikasi Pembayaran]
    │                         │◄──[Status: paid]───────────┤
    │                         ├──[Buat Proses Produksi]──►Staf Produksi
    │                         │                    ├──[Update Tahap Produksi]
    │                         │◄──[Status: completed]──────┤
    │◄──[Pesanan Selesai]──────┤                          │
```

### 2. Alur Pemesanan Produk Custom

```
Pelanggan                  Sistem                     Admin
    │                         │                          │
    ├──[Form Custom Order]────►│                          │
    │   (nama, dimensi, qty)  ├──[Buat OrderDetail       │
    │                         │   is_custom=true,        │
    │                         │   unit_price=0]──────────►│
    │                         │                    ├──[Hitung BOM]
    │                         │                    │   (kubikasi kayu,
    │                         │                    │    grade, biaya)
    │                         │◄──[Simpan Harga]──────────┤
    │◄──[Harga Ditetapkan]────┤                          │
    ├──[Bayar]────────────────►│ ... (alur normal) ...    │
```

### 3. Alur Produksi

```
[Order Confirmed]
      │
      ▼
[Proses Produksi Dibuat: pending]
      │
      ├──►[Pemotongan (cutting)]
      │         │
      ├──►[Perakitan (assembly)]
      │         │
      ├──►[Penghalusan (sanding)]
      │         │
      ├──►[Finishing]
      │         │
      ├──►[Quality Control]
      │         │
      └──►[Selesai (completed)]
                │
                ▼
      [Order Status → completed]
```

---

## 👥 Peran Pengguna

### 🔑 Admin
Akses penuh ke seluruh sistem melalui panel `/admin`.

**Hak Akses:**
- Kelola semua pengguna (buat, edit, nonaktifkan, hapus)
- Kelola kategori dan produk (CRUD + upload gambar)
- Konfirmasi dan kelola semua pesanan
- Hitung harga produk custom (BOM calculator)
- Verifikasi / tolak pembayaran manual
- Pantau dan kelola proses produksi
- Akses semua laporan dan export CSV
- Konfigurasi pengaturan sistem

### 🛍️ Pelanggan (Customer)
Akses melalui panel `/customer`.

**Hak Akses:**
- Browse katalog produk dan melihat detail
- Kelola keranjang belanja
- Checkout dan buat pesanan
- Buat pesanan custom (spesifikasi dimensi bebas)
- Lihat dan lacak status pesanan sendiri
- Upload bukti pembayaran transfer
- Kelola profil dan ganti password
- Batalkan pesanan yang masih `pending`

### 🔧 Staf Produksi (Production Staff)
Akses melalui panel `/production`.

**Hak Akses:**
- Dashboard produksi (monitoring semua proses)
- Pantau dan update tahap produksi
- Akses detail pesanan yang sedang diproduksi
- Kelola to-do list tugas harian (milik sendiri)
- Kelola jadwal produksi (milik sendiri)
- Export jadwal ke file `.ics`

---

## 🗂️ Menu & Navigasi

### Admin Panel (`/admin`)

| Menu | Sub-menu | Keterangan |
|------|----------|------------|
| **Dashboard** | — | Statistik ringkas, grafik, order terbaru |
| **Pengguna** | Daftar, Tambah, Edit, Detail | CRUD manajemen akun |
| **Produk** | Daftar, Tambah, Edit | CRUD produk dengan upload gambar |
| **Kategori** | Daftar, Tambah, Edit | CRUD kategori bertingkat |
| **Pesanan** | Daftar, Detail, Update Status | Manajemen semua pesanan |
| **Pesanan Custom** | Daftar, Kalkulator BOM | Penetapan harga produk custom |
| **Pembayaran** | Menunggu Verifikasi, Detail | Verifikasi/tolak pembayaran |
| **Produksi** | Dashboard, Detail | Monitoring proses produksi |
| **Laporan** | Keuangan, Penjualan, Produksi, Inventori, Profitabilitas | Laporan + export CSV |
| **Pengaturan** | — | Konfigurasi sistem |
| **Profil** | — | Edit profil & ganti password |

### Customer Panel (`/customer`)

| Menu | Keterangan |
|------|------------|
| **Beranda** | Produk terbaru, kategori unggulan |
| **Katalog Produk** | Browse, filter, cari produk |
| **Keranjang** | Kelola item, ubah jumlah, hapus |
| **Checkout** | Form pesanan, pilih metode bayar |
| **Pesanan Saya** | Daftar semua pesanan |
| **Lacak Pesanan** | Status real-time per pesanan |
| **Pesanan Custom** | Form pemesanan dimensi bebas |
| **Profil** | Edit data diri, ganti password |

### Production Staff Panel (`/production`)

| Menu | Keterangan |
|------|------------|
| **Dashboard** | Statistik produksi, proses aktif |
| **Monitoring** | Daftar order yang sedang diproduksi |
| **Proses Produksi** | Detail dan update tahap per proses |
| **To-Do List** | Tugas harian staf (milik sendiri) |
| **Jadwal Produksi** | Kalender jadwal (milik sendiri) |

---

## 💻 Persyaratan Sistem

| Komponen | Versi Minimum |
|----------|---------------|
| PHP | 8.2 atau lebih baru |
| Laravel | 11.x |
| MySQL | 8.0 atau lebih baru |
| Composer | 2.x |
| Node.js | 18.x (untuk build aset) |
| NPM | 9.x |

### Ekstensi PHP yang Diperlukan
- `ext-pdo`
- `ext-mbstring`
- `ext-openssl`
- `ext-tokenizer`
- `ext-xml`
- `ext-ctype`
- `ext-json`
- `ext-bcmath`
- `ext-fileinfo`
- `ext-curl`

---

## 🚀 Panduan Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/furniture-manufacturing-system.git
cd furniture-manufacturing-system
```

### 2. Instal Dependensi PHP

```bash
composer install
```

### 3. Instal Dependensi JavaScript

```bash
npm install
```

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Kemudian buka file `.env` dan sesuaikan konfigurasi berikut:

```env
# Konfigurasi Aplikasi
APP_NAME="UD Bisa Furniture"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Konfigurasi Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=furniture_manufacturing
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi Driver Cache & Queue
CACHE_DRIVER=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# Konfigurasi Email (opsional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# API Konversi Mata Uang (dari https://app.exchangerate-api.com)
EXCHANGERATE_API_KEY=your_exchangerate_api_key

# API Google Translate (dari https://console.cloud.google.com)
GOOGLE_TRANSLATE_API_KEY=your_google_translate_api_key

# Midtrans Payment Gateway
MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
MIDTRANS_IS_PRODUCTION=false
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Buat Database

Buat database MySQL baru dengan nama `furniture_manufacturing`:

```sql
CREATE DATABASE furniture_manufacturing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Jalankan Migrasi Database

```bash
php artisan migrate
```

### 8. Jalankan Seeder (Data Awal)

```bash
php artisan db:seed
```

Seeder akan membuat:
- 3 peran (roles): Admin, Customer, Production Staff
- 1 akun admin default

> **Kredensial Admin Default:**
> - Email: `admin@furniture.com`
> - Password: `password`
>
> ⚠️ **Segera ganti password setelah login pertama!**

### 9. Buat Symbolic Link Storage

```bash
php artisan storage:link
```

### 10. Build Aset Frontend

Untuk development:
```bash
npm run dev
```

Untuk production:
```bash
npm run build
```

### 11. Jalankan Server Development

```bash
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

### 12. (Opsional) Jalankan Queue Worker

Jika menggunakan fitur email atau antrian:

```bash
php artisan queue:work
```

---

## 🔑 Konfigurasi API Eksternal

### ExchangeRate API (Konversi Mata Uang)

Sistem menggunakan [ExchangeRate-API](https://www.exchangerate-api.com/) untuk konversi nilai mata uang IDR ↔ USD.

1. Daftar akun gratis di https://app.exchangerate-api.com/
2. Salin API Key dari dashboard
3. Isi pada `.env`:
   ```env
   EXCHANGERATE_API_KEY=your_api_key_here
   ```

Implementasi ada di: `app/Services/CurrencyConversionService.php`

### Google Translate API (Terjemahan)

Sistem menggunakan [Google Cloud Translation API](https://cloud.google.com/translate) untuk terjemahan konten.

1. Buat project di [Google Cloud Console](https://console.cloud.google.com/)
2. Aktifkan **Cloud Translation API**
3. Buat API Key di menu Credentials
4. Isi pada `.env`:
   ```env
   GOOGLE_TRANSLATE_API_KEY=your_api_key_here
   ```

Implementasi ada di: `app/Services/GoogleTranslateService.php`

### Midtrans (Payment Gateway)

Sistem mendukung webhook Midtrans untuk notifikasi pembayaran otomatis.

1. Daftar akun di [Midtrans Dashboard](https://dashboard.midtrans.com/)
2. Ambil Server Key dan Client Key dari menu Settings → Access Keys
3. Isi pada `.env`:
   ```env
   MIDTRANS_SERVER_KEY=your_server_key
   MIDTRANS_CLIENT_KEY=your_client_key
   MIDTRANS_IS_PRODUCTION=false  # Ubah ke true untuk production
   ```
4. Daftarkan URL webhook di Midtrans Dashboard:
   ```
   https://your-domain.com/payment/midtrans/notification
   ```

---

## 📁 Struktur Proyek

```
furniture-manufacturing-system/
│
├── app/
│   ├── Helpers/
│   │   └── helpers.php              # Helper global (formatRupiah, statusBadge, dll)
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/               # Controller panel admin
│   │   │   ├── Auth/                # Autentikasi (login, register, logout)
│   │   │   ├── Customer/            # Controller panel pelanggan
│   │   │   └── Production/          # Controller panel staf produksi
│   │   │
│   │   ├── Middleware/
│   │   │   └── CheckRole.php        # Middleware cek peran pengguna
│   │   │
│   │   └── Requests/
│   │       ├── Admin/               # Form request validasi admin
│   │       └── Production/          # Form request validasi produksi
│   │
│   ├── Models/                      # Eloquent Models
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Order.php
│   │   ├── OrderDetail.php
│   │   ├── Payment.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── ProductionProcess.php
│   │   ├── ProductionLog.php
│   │   ├── ProductionTodo.php
│   │   ├── ProductionSchedule.php
│   │   ├── Report.php
│   │   └── Setting.php
│   │
│   ├── Observers/                   # Auto-handle event model
│   │   ├── OrderObserver.php        # Invalidasi cache saat order berubah
│   │   ├── ProductObserver.php      # Handle event produk
│   │   └── ProductionProcessObserver.php
│   │
│   ├── Policies/                    # Otorisasi berbasis kepemilikan data
│   │   ├── ProductionTodoPolicy.php
│   │   └── ProductionSchedulePolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php   # Registrasi observer, timezone
│   │   ├── AuthServiceProvider.php  # Registrasi policy
│   │   └── RepositoryServiceProvider.php  # Bind repository
│   │
│   ├── Repositories/                # Repository Pattern
│   │   ├── BaseRepository.php
│   │   ├── Contracts/               # Interface repository
│   │   └── Eloquent/                # Implementasi Eloquent
│   │
│   └── Services/                    # Business Logic Layer
│       ├── CurrencyConversionService.php
│       ├── GoogleTranslateService.php
│       ├── PaymentService.php
│       ├── ProductionService.php
│       └── QueryOptimizationService.php
│
├── database/
│   ├── migrations/                  # Skema database (22+ file)
│   ├── factories/                   # Data factory untuk testing
│   └── seeders/                     # Data awal (roles, admin user)
│
├── resources/
│   ├── css/                         # Custom CSS
│   ├── js/                          # JavaScript
│   └── views/
│       ├── admin/                   # Blade view panel admin
│       ├── customer/                # Blade view panel pelanggan
│       ├── production/              # Blade view panel staf produksi
│       ├── auth/                    # View login & register
│       ├── layouts/                 # Layout utama (admin, customer, production)
│       └── components/              # Komponen blade reusable
│
├── routes/
│   └── web.php                      # Definisi semua rute web
│
├── config/                          # File konfigurasi Laravel
├── public/                          # Aset publik & entry point
├── storage/                         # File upload & log
└── tests/                           # Unit & Feature tests
```

---

## 🏆 Keunggulan Sistem

### Dibanding Pengelolaan Manual

| Aspek | Manual (Kertas/Excel) | Sistem Digital Ini |
|-------|----------------------|-------------------|
| **Pencatatan Pesanan** | Rawan hilang/rusak, lambat | Real-time, tersimpan aman di database |
| **Status Pesanan** | Perlu konfirmasi manual via telepon | Pelanggan bisa lacak sendiri kapan saja |
| **Harga Produk Custom** | Perhitungan manual, rawan error | Kalkulasi BOM otomatis berbasis kubikasi |
| **Monitoring Produksi** | Tidak ada histori terstruktur | Log produksi lengkap per tahap |
| **Laporan Keuangan** | Butuh waktu panjang rekap data | Otomatis, real-time, bisa export CSV |
| **Keamanan Data** | Mudah hilang/dicuri | Password terenkripsi, akses berbasis peran |

### Fitur Teknis Unggulan

- ✅ **Cache cerdas** — Dashboard statistik di-cache, otomatis dibersihkan oleh Observer saat data berubah
- ✅ **Query optimasi** — Eager loading konsisten untuk menghindari masalah N+1 query
- ✅ **Validasi berlapis** — Form Request + Policy Authorization + Middleware
- ✅ **Transaksi database** — Semua operasi kritis dibungkus `DB::transaction()` untuk konsistensi data
- ✅ **Soft delete** — Data `ProductionTodo` dan `ProductionSchedule` tidak benar-benar dihapus
- ✅ **Snapshot data** — `order_details.product_name` menyimpan snapshot nama produk saat order dibuat, aman jika produk diedit/dihapus
- ✅ **Throttle login** — Proteksi anti-brute force (5 percobaan/menit untuk login, 3 untuk register)
- ✅ **Webhook idempoten** — Handler Midtrans aman diproses ulang tanpa duplikasi

---

## 🧪 Testing

Jalankan semua test:

```bash
php artisan test
```

Jalankan unit test saja:

```bash
php artisan test --testsuite=Unit
```

Jalankan feature test saja:

```bash
php artisan test --testsuite=Feature
```

---

## 🤝 Kontribusi

1. Fork repository ini
2. Buat branch fitur baru: `git checkout -b feature/nama-fitur`
3. Commit perubahan: `git commit -m 'feat: tambah fitur X'`
4. Push ke branch: `git push origin feature/nama-fitur`
5. Buat Pull Request

---

## 📜 Lisensi

Proyek ini dikembangkan untuk keperluan **Skripsi / Tugas Akhir**. Seluruh hak cipta dilindungi oleh pengembang.

---

## 📞 Kontak

Untuk pertanyaan atau dukungan teknis, silakan hubungi tim pengembang melalui:
- WhatsApp: +62 852-9050-5442
- Email: admin@furniture.com

---

<p align="center">
  Dibuat dengan ❤️ menggunakan <strong>Laravel 11</strong> + <strong>Bootstrap 5</strong>
</p># Manufaktur-Furniture-App
