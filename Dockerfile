# Menggunakan image PHP + Nginx yang sudah siap pakai
FROM serversideup/php:8.3-fpm-nginx

# Set working directory aplikasi
WORKDIR /var/www/html

# Ganti user ke root sementara untuk install dependencies sistem jika butuh
USER root

# Install tools tambahan yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Pindah kembali ke user bawaan agar aman
USER www-data

# Copy seluruh source code project Laravel ke dalam container
COPY --chown=www-data:www-data . .

# Install dependencies Composer (tanpa dev tools untuk production)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Catatan: Jika lu punya asset frontend (CSS/JS) yang perlu di-build (Vite/Mix), 
# Render akan otomatis menjalankan web server Nginx di port bawaan container (8080/80)
# Dan mengeksposnya ke publik secara otomatis.