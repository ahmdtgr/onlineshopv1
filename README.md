# 🛍️ OnlineShop - Toko Online Lengkap dengan Payment Gateway & Ekspedisi Otomatis

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel" alt="Laravel 10">
  <img src="https://img.shields.io/badge/Filament-3.x-orange?style=for-the-badge&logo=filament" alt="Filament 3">
  <img src="https://img.shields.io/badge/Livewire-3.x-pink?style=for-the-badge&logo=livewire" alt="Livewire 3">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-blue?style=for-the-badge&logo=tailwindcss" alt="TailwindCSS">
</p>

## 📖 Tentang Project

**OnlineShop 2.4.0** adalah sistem toko online modern yang dibangun dengan Laravel 10, Filament 3, dan Livewire 3. Project ini dilengkapi dengan fitur-fitur lengkap untuk menjalankan bisnis e-commerce, termasuk integrasi payment gateway (Midtrans) dan sistem ekspedisi otomatis (Biteship/Rajaongkir).

### ✨ Fitur Utama

#### 🛒 Customer Features
- **Product Catalog** dengan kategori dan varian produk (ukuran, warna, dll)
- **Shopping Cart** dengan sistem keranjang belanja real-time
- **Checkout Process** yang simpel dan user-friendly
- **Multiple Payment Methods** (Transfer Bank & Midtrans Payment Gateway)
- **Automatic Shipping Integration** dengan Biteship
- **Real-time Order Tracking** dengan status pengiriman otomatis
- **Order History** dengan detail lengkap
- **Payment Confirmation** dengan upload bukti transfer
- **Mobile-First Design** yang responsive

#### 🎛️ Admin Panel (Filament 3)
- **Dashboard** dengan statistik penjualan dan revenue chart
- **Product Management** dengan varian produk kompleks
- **Order Management** dengan status tracking otomatis
- **User Management** dengan role & permission (Spatie)
- **Store Settings** termasuk konfigurasi pengiriman
- **Payment Method Management**
- **Category & Variant Management**
- **Invoice Generator** (PDF dengan DomPDF)

#### 🚀 Advanced Features
- **Midtrans Payment Gateway** - QRIS, Virtual Account, E-Wallet, dll
- **Biteship Integration** - Tracking & shipping otomatis
- **Auto Status Update** - Order status ter-update otomatis berdasarkan payment & shipping
- **Email Notifications** - Otomatis kirim email untuk update status order
- **SMTP Email Configuration** - Setting email melalui admin panel
- **Voucher & Discount System** - Kelola voucher diskon (fixed/percentage) dengan batas penggunaan
- **Responsive Mobile Design** dengan bottom navigation
- **Role & Permission** menggunakan Spatie Laravel Permission
- **File Upload to Public Storage** untuk production ready

### 🛠️ Tech Stack

- **Backend**: Laravel 10.x
- **Admin Panel**: Filament 3.x
- **Frontend Framework**: Livewire 3.x
- **Styling**: TailwindCSS 3.x + Alpine.js
- **Database**: MySQL/PostgreSQL
- **Payment Gateway**: Midtrans
- **Shipping**: Biteship API
- **PDF Generator**: DomPDF
- **File Storage**: Laravel Storage (Public Disk)

---

## 📋 Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Web Server (Apache/Nginx)

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/filament-onlineshop.git
cd filament-onlineshop
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=onlineshop
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi dan seeder:

```bash
# Run migrations
php artisan migrate

# Run seeders (creates demo data: products, categories, users, store)
php artisan db:seed
```

### 5. Storage Setup

```bash
# Create symbolic link for storage
php artisan storage:link
```

### 6. Payment Gateway Configuration (Midtrans)

Tambahkan credential Midtrans di `.env`:

```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

### 7. Shipping API Configuration (Biteship)

Tambahkan API key Biteship di Store Settings (via Admin Panel) atau langsung ke database.

### 8. Email Configuration (SMTP)

Konfigurasi email SMTP di `.env` untuk notifikasi otomatis:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Atau konfigurasi langsung melalui **Admin Panel > System Settings > Email Settings**.

**Fitur Email Notifications:**
- Order Created - Email konfirmasi pesanan baru
- Payment Confirmed - Notifikasi pembayaran diterima
- Order Shipped - Email dengan nomor resi pengiriman
- Order Delivered - Konfirmasi pesanan sudah sampai
- Order Cancelled - Notifikasi pembatalan pesanan

### 9. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 9. Run Application

```bash
# Local development server
php artisan serve
```

Aplikasi akan berjalan di: `http://localhost:8000`

---

## 👤 Default Users

Setelah menjalankan seeder, gunakan akun berikut:

### Admin Account
- **Email**: admin@dewakoding.com
- **Password**: password

### Customer Account
- **Email**: user@dewakoding.com
- **Password**: password

---

## 📂 Project Structure

```
filament-onlineshop/
├── app/
│   ├── Filament/           # Filament Admin Resources
│   │   ├── Pages/          # Custom admin pages
│   │   ├── Resources/      # CRUD resources
│   │   └── Widgets/        # Dashboard widgets
│   ├── Livewire/           # Livewire Components (Frontend)
│   ├── Models/             # Eloquent Models
│   ├── Services/           # Business Logic Services
│   │   ├── BiteshipService.php
│   │   ├── MidtransService.php
│   │   ├── OrderStatusService.php
│   │   └── ShippingStatusService.php
│   └── Providers/
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
│       └── images/         # Seeder images
├── resources/
│   ├── views/
│   │   ├── livewire/       # Livewire views
│   │   ├── components/     # Blade components
│   │   └── filament/       # Filament custom views
│   ├── css/
│   └── js/
└── public/
    └── storage/            # Public storage (symlink)
```

---

## 🔧 Configuration

### Store Settings

Konfigurasi toko dapat diatur melalui Admin Panel:
1. Login sebagai admin
2. Buka menu **Settings > Stores**
3. Isi informasi toko:
   - Nama toko
   - Deskripsi
   - Logo
   - Warna tema (Primary & Secondary)
   - Alamat pickup (untuk shipping)
   - API Keys (Biteship/Rajaongkir)

### Payment Methods

Tambahkan metode pembayaran via Admin Panel:
1. Buka menu **Settings > Payment Methods**
2. Tambah metode baru dengan:
   - Nama (contoh: BCA, Mandiri)
   - Logo bank
   - Nomor rekening
   - Nama penerima

---

## 🎨 Customization

### Theme Colors

Edit warna tema di `config/filament.php` atau melalui Store Settings.

### Email Notifications

Konfigurasi SMTP di `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourstore.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 📝 API Documentation

### Midtrans Integration

Project ini menggunakan Midtrans Snap untuk payment gateway. Flow:
1. Customer checkout → order created
2. System creates Midtrans transaction
3. Customer redirected to Midtrans payment page
4. After payment → webhook updates order status

### Biteship Integration

Shipping integration workflow:
1. Admin approves order → creates shipping order via Biteship
2. System gets tracking number & courier info
3. Auto-update shipping status (picked up, on delivery, delivered)
4. Customer can track real-time via order detail page

---

## 🚢 Deployment

### Production Checklist

```bash
# Set environment to production
APP_ENV=production
APP_DEBUG=false

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

### Server Configuration

Lihat file `DEPLOYMENT_FIX.md` untuk panduan deployment ke production server.

**Key Points:**
- Set proper file permissions
- Configure web server (Apache/Nginx)
- Setup SSL certificate
- Configure storage for public disk
- Enable OPcache for better performance

---

## 🐛 Troubleshooting

### Issue: File upload 404 in production

**Solution**: Update `config/livewire.php`:
```php
'temporary_file_upload' => [
    'disk' => 'public',
],
```

### Issue: Shipping status not updating

**Solution**: Check Store API key and run:
```bash
php artisan queue:work
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Developer

Developed by **Dewa Koding**
- Website: [https://dewakoding.com](https://dewakoding.com)
- Tutorial: [Source Code Toko Online Lengkap](https://dewakoding.com/tutorial/source-code-toko-online-lengkap-payment-ekspedisi-otomatis)

---

## 🤝 Support

Jika ada pertanyaan atau issue, silakan:
1. Buka [GitHub Issues](https://github.com/yourusername/filament-onlineshop/issues)
2. Email: support@dewakoding.com
3. Join komunitas Dewa Koding

---

## 📸 Screenshots

### Customer View (Mobile)
- Home page dengan product catalog
- Product detail dengan variant selection
- Shopping cart & checkout dengan voucher discount
- Order tracking dengan shipping status

### Admin Panel
- Dashboard dengan revenue chart
- Product management dengan varian kompleks
- Order management dengan auto status update
- Voucher management dengan usage tracking
- Email settings & SMTP configuration
- Store settings & configuration

---

## 🎟️ Voucher System

Sistem voucher diskon lengkap dengan fitur:

- **Tipe Diskon**: Fixed Amount atau Percentage
- **Periode Berlaku**: Set tanggal mulai dan berakhir
- **Batas Minimal**: Minimum pembelian untuk gunakan voucher
- **Batas Maksimal**: Maximum diskon untuk percentage type
- **Usage Tracking**: Monitor berapa kali voucher sudah digunakan
- **Unlimited Usage**: Option untuk voucher tanpa batas penggunaan
- **Auto Apply**: Customer bisa input kode voucher saat checkout
- **Real-time Validation**: Validasi otomatis untuk voucher expired/habis

---

**Made by Dewakoding with ❤️ using Laravel, Filament, and Livewire**
