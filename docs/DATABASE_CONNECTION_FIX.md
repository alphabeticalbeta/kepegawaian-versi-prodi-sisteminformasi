# Panduan Mengatasi Error Database Connection Refused

## Error yang Dialami
```
SQLSTATE[HY000] [2002] Connection refused (Connection: mysql, SQL: select * from `pegawais` where `nip` = 199405242024061001 limit 1)
```

## Analisis Masalah
Error ini terjadi karena aplikasi Laravel tidak dapat terhubung ke database MySQL. Berdasarkan konfigurasi di file `.env`, aplikasi mencoba terhubung ke:
- Host: 127.0.0.1
- Port: 3307
- Database: db_kepegunmul
- Username: root
- Password: root

## Langkah-langkah Penyelesaian

### 1. Periksa Status MySQL Service

**Jika menggunakan Laragon:**
1. Buka Laragon
2. Pastikan MySQL berjalan (indikator hijau)
3. Jika tidak, klik "Start All"

**Jika menggunakan XAMPP:**
1. Buka XAMPP Control Panel
2. Start MySQL dan Apache
3. Pastikan status MySQL "Running"

**Jika menggunakan MySQL standalone:**
```bash
# Windows
net start mysql

# Atau cek service
sc query mysql
```

### 2. Periksa Port MySQL

Berdasarkan konfigurasi, MySQL seharusnya berjalan di port 3307. Untuk memastikan:

```bash
# Cek port yang digunakan
netstat -an | findstr :3307
netstat -an | findstr :3306
```

### 3. Jalankan Script Diagnostik

Saya telah membuat script untuk mendiagnosis masalah:

```bash
# Jalankan script diagnostik
php fix_database_connection.php

# Jalankan script perbaikan konfigurasi
php fix_database_config.php
```

### 4. Perbaiki Konfigurasi Database

**Jika port salah:**
Edit file `.env`:
```env
DB_PORT=3306  # atau 3307, sesuaikan dengan port MySQL Anda
```

**Jika database tidak ada:**
1. Buat database secara manual:
```sql
CREATE DATABASE db_kepegunmul CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Atau jalankan migrasi:
```bash
php artisan migrate
```

### 5. Jalankan Migrasi dan Seeder

```bash
# Jalankan migrasi
php artisan migrate

# Jalankan seeder
php artisan db:seed

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### 6. Periksa Tabel dan Data

Pastikan tabel `pegawais` ada dan berisi data:

```sql
-- Cek tabel
SHOW TABLES LIKE 'pegawais';

-- Cek data
SELECT * FROM pegawais WHERE nip = '199405242024061001';
```

## Solusi Cepat

### Untuk Laragon:
1. Buka Laragon
2. Klik "Start All"
3. Buka Terminal di Laragon
4. Navigasi ke direktori project:
   ```bash
   cd /d/kepegawaian-unmul-v2
   ```
5. Jalankan perintah:
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan config:clear
   ```

### Untuk XAMPP:
1. Buka XAMPP Control Panel
2. Start MySQL dan Apache
3. Buka Command Prompt
4. Navigasi ke direktori project:
   ```bash
   cd D:\kepegawaian-unmul-v2
   ```
5. Jalankan perintah:
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan config:clear
   ```

## Troubleshooting Lanjutan

### Jika MySQL tidak berjalan:
1. Cek apakah MySQL terinstall
2. Cek service MySQL di Windows Services
3. Restart service MySQL

### Jika port berbeda:
1. Cek konfigurasi MySQL di my.ini
2. Sesuaikan port di file .env
3. Restart MySQL service

### Jika database tidak ada:
1. Buat database secara manual
2. Jalankan migrasi
3. Jalankan seeder

### Jika user tidak ada:
1. Cek apakah user dengan NIP tersebut ada di database
2. Tambahkan user ke database
3. Atau gunakan NIP yang benar

## Verifikasi Perbaikan

Setelah melakukan perbaikan, coba login kembali. Jika masih error, periksa:

1. Log Laravel di `storage/logs/laravel.log`
2. Status MySQL service
3. Konfigurasi database di `.env`
4. Koneksi database dengan script diagnostik

## File-file yang Dibuat

1. `fix_database_connection.php` - Script diagnostik koneksi database
2. `fix_database_config.php` - Script perbaikan konfigurasi database
3. `test_db_connection.php` - Script pengujian koneksi database

## Kontak Support

Jika masalah masih berlanjut, siapkan informasi berikut:
1. Output dari script diagnostik
2. Log error Laravel
3. Konfigurasi database
4. Versi MySQL dan Laravel
