<?php

echo "=== ANALISIS MASALAH DOKUMEN HILANG ===\n\n";

echo "1. DESKRIPSI MASALAH:\n";
echo "• Kondisi pertama: Semua dokumen ada dan tampil dengan baik\n";
echo "• Setelah perbaikan dan kirim kembali ke admin univ usulan: Dokumen hilang\n\n";

echo "2. ROOT CAUSE ANALYSIS:\n\n";

echo "2.1 Perbedaan Logic antara forward_to_university dan resend_to_university:\n\n";

echo "FORWARD_TO_UNIVERSITY (Baris 530-550):\n";
echo "```php\n";
echo "// Simpan dokumen pendukung fakultas\n";
echo "\$currentValidasi = \$usulan->validasi_data;\n";
echo "\$currentDokumenPendukung = \$currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];\n";
echo "\n";
echo "// Update text fields (handle null values)\n";
echo "if (isset(\$validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {\n";
echo "    \$currentDokumenPendukung['nomor_surat_usulan'] = \$validatedData['dokumen_pendukung']['nomor_surat_usulan'];\n";
echo "}\n";
echo "if (isset(\$validatedData['dokumen_pendukung']['nomor_berita_senat'])) {\n";
echo "    \$currentDokumenPendukung['nomor_berita_senat'] = \$validatedData['dokumen_pendukung']['nomor_berita_senat'];\n";
echo "}\n";
echo "\n";
echo "// Handle file uploads menggunakan FileStorageService\n";
echo "\$currentDokumenPendukung['file_surat_usulan_path'] = \$this->fileStorage->handleDokumenPendukung(...);\n";
echo "\$currentDokumenPendukung['file_berita_senat_path'] = \$this->fileStorage->handleDokumenPendukung(...);\n";
echo "\n";
echo "\$currentValidasi['admin_fakultas']['dokumen_pendukung'] = \$currentDokumenPendukung;\n";
echo "\$usulan->validasi_data = \$currentValidasi;\n";
echo "```\n\n";

echo "RESEND_TO_UNIVERSITY (Baris 640-680):\n";
echo "```php\n";
echo "// Update dokumen pendukung menggunakan FileStorageService\n";
echo "if (!empty(\$validatedData['dokumen_pendukung'])) {  // ← MASALAH DI SINI!\n";
echo "    \$currentValidasi = \$usulan->validasi_data;\n";
echo "    \$currentDokumenPendukung = \$currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];\n";
echo "    \n";
echo "    // Update text fields\n";
echo "    if (isset(\$validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {\n";
echo "        \$currentDokumenPendukung['nomor_surat_usulan'] = \$validatedData['dokumen_pendukung']['nomor_surat_usulan'];\n";
echo "    }\n";
echo "    if (isset(\$validatedData['dokumen_pendukung']['nomor_berita_senat'])) {\n";
echo "        \$currentDokumenPendukung['nomor_berita_senat'] = \$validatedData['dokumen_pendukung']['nomor_berita_senat'];\n";
echo "    }\n";
echo "    \n";
echo "    // Handle file uploads menggunakan FileStorageService\n";
echo "    \$currentDokumenPendukung['file_surat_usulan_path'] = \$this->fileStorage->handleDokumenPendukung(...);\n";
echo "    \$currentDokumenPendukung['file_berita_senat_path'] = \$this->fileStorage->handleDokumenPendukung(...);\n";
echo "    \n";
echo "    \$currentValidasi['admin_fakultas']['dokumen_pendukung'] = \$currentDokumenPendukung;\n";
echo "    \$usulan->validasi_data = \$currentValidasi;\n";
echo "}\n";
echo "```\n\n";

echo "2.2 MASALAH UTAMA:\n";
echo "• Kondisi `if (!empty(\$validatedData['dokumen_pendukung']))` pada resend_to_university\n";
echo "• Jika tidak ada file baru yang di-upload, \$validatedData['dokumen_pendukung'] akan kosong\n";
echo "• Akibatnya, dokumen pendukung TIDAK di-update sama sekali\n";
echo "• Dokumen yang sudah ada sebelumnya HILANG karena tidak disimpan kembali\n\n";

echo "3. ALUR MASALAH:\n\n";
echo "3.1 Kondisi Pertama (Berhasil):\n";
echo "• Admin Fakultas upload dokumen → forward_to_university\n";
echo "• Dokumen disimpan di validasi_data['admin_fakultas']['dokumen_pendukung']\n";
echo "• Admin Universitas dan Penilai Universitas dapat melihat dokumen\n\n";

echo "3.2 Setelah Perbaikan (Masalah):\n";
echo "• Admin Univ Usulan mengembalikan ke Admin Fakultas\n";
echo "• Admin Fakultas melakukan perbaikan (tanpa upload file baru)\n";
echo "• Admin Fakultas klik 'Kirim ke Universitas' → resend_to_university\n";
echo "• Karena tidak ada file baru, \$validatedData['dokumen_pendukung'] kosong\n";
echo "• Kondisi `if (!empty(\$validatedData['dokumen_pendukung']))` tidak terpenuhi\n";
echo "• Dokumen pendukung TIDAK di-update\n";
echo "• Dokumen yang sudah ada HILANG dari validasi_data\n";
echo "• Admin Universitas dan Penilai Universitas melihat 'Dokumen tidak tersedia'\n\n";

echo "4. SOLUSI YANG DIPERLUKAN:\n\n";
echo "4.1 Hapus kondisi `if (!empty(\$validatedData['dokumen_pendukung']))` pada resend_to_university\n";
echo "4.2 Selalu update dokumen pendukung, tidak peduli apakah ada file baru atau tidak\n";
echo "4.3 Gunakan logic yang sama dengan forward_to_university\n\n";

echo "4.2 Kode yang Benar untuk resend_to_university:\n";
echo "```php\n";
echo "// Update dokumen pendukung menggunakan FileStorageService\n";
echo "// HAPUS kondisi if (!empty(\$validatedData['dokumen_pendukung']))\n";
echo "\$currentValidasi = \$usulan->validasi_data;\n";
echo "\$currentDokumenPendukung = \$currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];\n";
echo "\n";
echo "// Update text fields\n";
echo "if (isset(\$validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {\n";
echo "    \$currentDokumenPendukung['nomor_surat_usulan'] = \$validatedData['dokumen_pendukung']['nomor_surat_usulan'];\n";
echo "}\n";
echo "if (isset(\$validatedData['dokumen_pendukung']['nomor_berita_senat'])) {\n";
echo "    \$currentDokumenPendukung['nomor_berita_senat'] = \$validatedData['dokumen_pendukung']['nomor_berita_senat'];\n";
echo "}\n";
echo "\n";
echo "// Handle file uploads menggunakan FileStorageService\n";
echo "// FileStorageService akan mengembalikan file yang sudah ada jika tidak ada file baru\n";
echo "\$currentDokumenPendukung['file_surat_usulan_path'] = \$this->fileStorage->handleDokumenPendukung(\n";
echo "    \$request, \$usulan, 'file_surat_usulan', 'dokumen-fakultas/surat-usulan'\n";
echo ");\n";
echo "\n";
echo "\$currentDokumenPendukung['file_berita_senat_path'] = \$this->fileStorage->handleDokumenPendukung(\n";
echo "    \$request, \$usulan, 'file_berita_senat', 'dokumen-fakultas/berita-senat'\n";
echo ");\n";
echo "\n";
echo "\$currentValidasi['admin_fakultas']['dokumen_pendukung'] = \$currentDokumenPendukung;\n";
echo "\$usulan->validasi_data = \$currentValidasi;\n";
echo "```\n\n";

echo "5. FILE YANG PERLU DIPERBAIKI:\n";
echo "• File: app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php\n";
echo "• Method: saveComplexValidation()\n";
echo "• Case: 'resend_to_university'\n";
echo "• Baris: ~640-680\n\n";

echo "6. TESTING SCENARIOS SETELAH PERBAIKAN:\n";
echo "• Scenario 1: Admin Fakultas upload dokumen baru → Dokumen tersimpan\n";
echo "• Scenario 2: Admin Fakultas tidak upload file baru → Dokumen lama tetap ada\n";
echo "• Scenario 3: Admin Fakultas update nomor surat saja → Dokumen tetap ada\n";
echo "• Scenario 4: Admin Fakultas update nomor berita saja → Dokumen tetap ada\n\n";

echo "=== ANALISIS SELESAI ===\n";
echo "Status: ✅ Masalah diidentifikasi\n";
echo "Root Cause: Kondisi if (!empty(\$validatedData['dokumen_pendukung'])) pada resend_to_university\n";
echo "Solution: Hapus kondisi tersebut dan selalu update dokumen pendukung\n";

