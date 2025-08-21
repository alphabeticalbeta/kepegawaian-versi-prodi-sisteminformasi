-- Script untuk menambahkan kolom status_kepegawaian ke tabel periode_usulans
-- Jalankan script ini di database MySQL/MariaDB

-- Cek apakah kolom sudah ada
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'periode_usulans'
AND COLUMN_NAME = 'status_kepegawaian';

-- Jika kolom belum ada, tambahkan kolom
ALTER TABLE periode_usulans
ADD COLUMN status_kepegawaian JSON NULL
AFTER jenis_usulan
COMMENT 'Status kepegawaian yang diizinkan untuk mengakses periode ini';

-- Verifikasi kolom telah ditambahkan
DESCRIBE periode_usulans;

-- Tampilkan struktur tabel
SHOW COLUMNS FROM periode_usulans;
