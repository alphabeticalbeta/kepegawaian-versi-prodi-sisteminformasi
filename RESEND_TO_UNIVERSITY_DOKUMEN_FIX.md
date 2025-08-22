# 🔧 PERBAIKAN MASALAH DOKUMEN HILANG PADA RESEND_TO_UNIVERSITY

## 📋 DESKRIPSI MASALAH

**Kondisi:**
- ✅ Kondisi pertama: Semua dokumen ada dan tampil dengan baik
- ❌ Setelah perbaikan dan kirim kembali ke admin univ usulan: Dokumen hilang

## 🎯 ROOT CAUSE

**Masalah utama:** Kondisi `if (!empty($validatedData['dokumen_pendukung']))` pada action `resend_to_university`

**Alur masalah:**
1. Admin Fakultas upload dokumen → `forward_to_university` → Dokumen tersimpan ✅
2. Admin Univ Usulan mengembalikan ke Admin Fakultas
3. Admin Fakultas melakukan perbaikan (tanpa upload file baru)
4. Admin Fakultas klik 'Kirim ke Universitas' → `resend_to_university`
5. Karena tidak ada file baru, `$validatedData['dokumen_pendukung']` kosong
6. Kondisi `if (!empty($validatedData['dokumen_pendukung']))` **TIDAK TERPENUHI**
7. Dokumen pendukung **TIDAK DI-UPDATE**
8. Dokumen yang sudah ada **HILANG** dari `validasi_data`
9. Admin Universitas dan Penilai Universitas melihat "Dokumen tidak tersedia" ❌

## 🔧 SOLUSI YANG DIIMPLEMENTASIKAN

### File: `app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php`
### Method: `saveComplexValidation()`
### Case: `'resend_to_university'`

### **SEBELUM (BERMASALAH):**
```php
// Update dokumen pendukung menggunakan FileStorageService
if (!empty($validatedData['dokumen_pendukung'])) {  // ← MASALAH DI SINI!
    $currentValidasi = $usulan->validasi_data;
    $currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];
    
    // Update text fields dan file uploads...
    $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
    $usulan->validasi_data = $currentValidasi;
}
```

### **SESUDAH (DIPERBAIKI):**
```php
// Update dokumen pendukung menggunakan FileStorageService
// SELALU update dokumen pendukung, tidak peduli apakah ada file baru atau tidak
$currentValidasi = $usulan->validasi_data;
$currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

// Update text fields jika ada
if (isset($validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {
    $currentDokumenPendukung['nomor_surat_usulan'] = $validatedData['dokumen_pendukung']['nomor_surat_usulan'];
}
if (isset($validatedData['dokumen_pendukung']['nomor_berita_senat'])) {
    $currentDokumenPendukung['nomor_berita_senat'] = $validatedData['dokumen_pendukung']['nomor_berita_senat'];
}

// Handle file uploads menggunakan FileStorageService
// FileStorageService akan mengembalikan file yang sudah ada jika tidak ada file baru
$currentDokumenPendukung['file_surat_usulan_path'] = $this->fileStorage->handleDokumenPendukung(
    $request,
    $usulan,
    'file_surat_usulan',
    'dokumen-fakultas/surat-usulan'
);

$currentDokumenPendukung['file_berita_senat_path'] = $this->fileStorage->handleDokumenPendukung(
    $request,
    $usulan,
    'file_berita_senat',
    'dokumen-fakultas/berita-senat'
);

$currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
$usulan->validasi_data = $currentValidasi;
```

## 🔄 PERUBAHAN YANG DILAKUKAN

### **1. Menghapus Kondisi Bermasalah:**
- ❌ Hapus: `if (!empty($validatedData['dokumen_pendukung']))`
- ✅ Selalu update dokumen pendukung

### **2. Menggunakan Logic yang Sama dengan forward_to_university:**
- ✅ Selalu update `validasi_data['admin_fakultas']['dokumen_pendukung']`
- ✅ FileStorageService akan mengembalikan file lama jika tidak ada file baru

### **3. Mempertahankan Fleksibilitas:**
- ✅ Update text fields hanya jika ada data baru
- ✅ File uploads selalu diproses (mengembalikan file lama jika tidak ada file baru)

## 🧪 TESTING SCENARIOS

### **Scenario 1: Admin Fakultas Upload Dokumen Baru**
- **Kondisi:** Admin Fakultas upload file baru
- **Hasil:** ✅ Dokumen baru tersimpan dan menggantikan dokumen lama

### **Scenario 2: Admin Fakultas Tidak Upload File Baru**
- **Kondisi:** Admin Fakultas hanya update nomor surat/berita tanpa upload file
- **Hasil:** ✅ Dokumen lama tetap ada, hanya text fields yang diupdate

### **Scenario 3: Admin Fakultas Update Nomor Surat Saja**
- **Kondisi:** Admin Fakultas hanya update nomor surat usulan
- **Hasil:** ✅ Dokumen file tetap ada, nomor surat terupdate

### **Scenario 4: Admin Fakultas Update Nomor Berita Saja**
- **Kondisi:** Admin Fakultas hanya update nomor berita senat
- **Hasil:** ✅ Dokumen file tetap ada, nomor berita terupdate

### **Scenario 5: Admin Fakultas Tidak Update Apapun**
- **Kondisi:** Admin Fakultas tidak mengubah apapun, hanya klik 'Kirim ke Universitas'
- **Hasil:** ✅ Dokumen lama tetap ada dan tidak hilang

## 📊 HASIL YANG DIHARAPKAN

### **Sebelum Perbaikan:**
- ❌ Admin Universitas: "Dokumen tidak tersedia"
- ❌ Penilai Universitas: "Dokumen tidak tersedia"

### **Sesudah Perbaikan:**
- ✅ Admin Universitas: Dapat melihat link dokumen File Surat Usulan dan File Berita Senat
- ✅ Penilai Universitas: Dapat melihat link dokumen File Surat Usulan dan File Berita Senat
- ✅ Tim Penilai: Tetap menggunakan secure route (tidak berubah)
- ✅ Admin Fakultas: Tetap dapat upload dan manage dokumen (tidak berubah)

## 🔐 KEAMANAN DAN KONSISTENSI

### **Keamanan:**
- ✅ Tidak mengubah permission model yang ada
- ✅ Tim Penilai tetap menggunakan secure route
- ✅ Admin Universitas & Penilai Universitas menggunakan direct asset URL
- ✅ Tidak ada perubahan pada authorization logic

### **Konsistensi:**
- ✅ Logic `resend_to_university` sekarang sama dengan `forward_to_university`
- ✅ FileStorageService behavior konsisten
- ✅ Data structure `validasi_data` tetap sama
- ✅ Tidak ada duplikasi data atau tabel baru

## 📝 LOGGING DAN DEBUGGING

### **Logging yang Ditambahkan:**
```php
Log::info('Dokumen pendukung - using existing file', [
    'usulan_id' => $usulan->id,
    'field_name' => $fieldName,
    'existing_file_path' => $usulan->getDocumentPath($fieldName),
    'debug_info' => $debugInfo
]);
```

### **Debugging Info:**
- File detection (dot notation vs bracket notation)
- Existing file path check
- Upload success/failure status
- File details (size, type, etc.)

## ✅ STATUS IMPLEMENTASI

**Status:** ✅ **BERHASIL DIIMPLEMENTASI**

**File yang Diperbaiki:**
- `app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php`

**Perubahan:**
- Menghapus kondisi `if (!empty($validatedData['dokumen_pendukung']))` pada `resend_to_university`
- Selalu update dokumen pendukung menggunakan FileStorageService
- Mempertahankan fleksibilitas untuk text fields

**Target:** Menyelesaikan masalah "Dokumen tidak tersedia" untuk Admin Universitas dan Penilai Universitas

**Solusi:** Multi-location document path lookup dengan fallback logic dan perbaikan logic `resend_to_university`

