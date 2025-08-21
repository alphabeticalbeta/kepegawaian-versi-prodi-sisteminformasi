# Troubleshooting Masalah Penyimpanan Usulan

## Deskripsi
Dokumentasi untuk mengatasi masalah data usulan yang tidak tersimpan ke database.

## Masalah yang Ditemukan

### ❌ **Gejala:**
- Form submission tidak menyimpan data
- Tidak ada error message yang jelas
- Tabel `usulans` dan `usulan_dokumens` kosong
- Tidak ada notifikasi setelah submit

## Analisis Masalah

### **1. Validasi Terlalu Ketat:**
- **Masalah**: BKD semester diwajibkan untuk semua jenjang
- **Solusi**: Ubah validasi BKD menjadi nullable untuk testing

### **2. File Validation Issues:**
- **Masalah**: Method `getFileValidation` mungkin terlalu kompleks
- **Solusi**: Gunakan validasi sederhana untuk testing

### **3. Database Transaction Issues:**
- **Masalah**: Transaction mungkin rollback tanpa error yang jelas
- **Solusi**: Tambahkan logging detail di setiap step

### **4. Foreign Key Constraints:**
- **Masalah**: Constraint mungkin gagal tanpa error yang jelas
- **Solusi**: Periksa data referensi (pegawai, periode)

## Implementasi Perbaikan

### **1. Validasi Disederhanakan**

#### **File**: `app/Http/Requests/Backend/PegawaiUnmul/StoreJabatanUsulanRequest.php`

#### **Perubahan:**
```php
// SEBELUM
'bkd_semester_1' => 'required|file|mimes:pdf|max:2048',
'bkd_semester_2' => 'required|file|mimes:pdf|max:2048',
'bkd_semester_3' => 'required|file|mimes:pdf|max:2048',
'bkd_semester_4' => 'required|file|mimes:pdf|max:2048',

// SESUDAH
'bkd_semester_1' => 'nullable|file|mimes:pdf|max:2048',
'bkd_semester_2' => 'nullable|file|mimes:pdf|max:2048',
'bkd_semester_3' => 'nullable|file|mimes:pdf|max:2048',
'bkd_semester_4' => 'nullable|file|mimes:pdf|max:2048',
```

#### **File Validation Disederhanakan:**
```php
// SEBELUM
'pakta_integritas' => $this->getFileValidation('pakta_integritas', true),

// SESUDAH
'pakta_integritas' => 'nullable|file|mimes:pdf|max:1024',
```

### **2. Logging Detail**

#### **File**: `app/Http/Controllers/Backend/PegawaiUnmul/UsulanJabatanController.php`

#### **Logging yang Ditambahkan:**
```php
Log::info('=== USULAN STORE START ===', [
    'user_id' => Auth::id(),
    'action' => $request->input('action'),
    'request_data' => $request->all()
]);

Log::info('=== VALIDATED DATA ===', [
    'validated_data' => $validatedData,
    'validation_passed' => true
]);

Log::info('=== TRANSACTION START ===', [
    'pegawai_id' => $pegawai->id,
    'periode_id' => $periodeUsulan->id
]);

Log::info('=== USULAN CREATED ===', [
    'usulan_id' => $usulan->id,
    'usulan_status' => $usulan->status_usulan
]);
```

## Debug Scripts

### **1. Debug Storage Script**
**File**: `debug_usulan_storage.php`

Script untuk memeriksa:
- Koneksi database
- Data tabel usulans dan usulan_dokumens
- Data pegawai dan periode
- Log files untuk error
- Storage directory
- Database transaction test

### **2. Test Submission Script**
**File**: `test_usulan_submission.php`

Script untuk testing:
- Data yang diperlukan (pegawai, periode)
- Create usulan sederhana
- Create dokumen sederhana
- Struktur tabel
- Foreign key constraints

## Testing Checklist

### ✅ **Database Testing:**
- [ ] Koneksi database berfungsi
- [ ] Tabel usulans ada dan dapat diakses
- [ ] Tabel usulan_dokumens ada dan dapat diakses
- [ ] Foreign key constraints tidak bermasalah
- [ ] Transaction dapat di-commit dan rollback

### ✅ **Data Testing:**
- [ ] Ada data pegawai yang valid
- [ ] Ada periode usulan yang aktif
- [ ] Data referensi lengkap
- [ ] Tidak ada constraint violation

### ✅ **Validation Testing:**
- [ ] Form validation tidak terlalu ketat
- [ ] File validation berfungsi
- [ ] Required fields terisi
- [ ] Optional fields dapat kosong

### ✅ **Controller Testing:**
- [ ] Method store dapat diakses
- [ ] Transaction berjalan normal
- [ ] Logging berfungsi
- [ ] Error handling berfungsi

## Troubleshooting Steps

### **Step 1: Cek Database**
```bash
# Cek koneksi database
php artisan tinker
DB::connection()->getPdo();

# Cek tabel
DB::table('usulans')->count();
DB::table('usulan_dokumens')->count();
```

### **Step 2: Cek Data Referensi**
```bash
# Cek pegawai
DB::table('pegawais')->first();

# Cek periode usulan
DB::table('periode_usulans')->where('status', 'Buka')->first();
```

### **Step 3: Cek Log Files**
```bash
# Cek log terbaru
tail -f storage/logs/laravel.log

# Cari error usulan
grep -i "usulan\|error\|exception" storage/logs/laravel.log
```

### **Step 4: Test Manual**
```bash
# Jalankan debug script
php debug_usulan_storage.php

# Jalankan test script
php test_usulan_submission.php
```

### **Step 5: Cek Storage**
```bash
# Cek storage directory
ls -la storage/app/usulan-dokumen/

# Cek permissions
chmod -R 755 storage/app/usulan-dokumen/
```

## Common Issues & Solutions

### **1. Validation Error**
**Gejala**: Form tidak submit, tidak ada error message
**Solusi**: 
- Periksa browser console untuk JavaScript error
- Periksa network tab untuk request/response
- Tambahkan logging di form submission

### **2. Database Error**
**Gejala**: Error di log, data tidak tersimpan
**Solusi**:
- Periksa foreign key constraints
- Periksa data referensi
- Periksa database permissions

### **3. File Upload Error**
**Gejala**: File tidak terupload, error storage
**Solusi**:
- Periksa storage permissions
- Periksa disk space
- Periksa file size limits

### **4. Transaction Rollback**
**Gejala**: Data tidak tersimpan, tidak ada error
**Solusi**:
- Tambahkan logging detail
- Periksa setiap step dalam transaction
- Periksa exception handling

## Monitoring & Prevention

### **1. Logging Strategy**
- Log setiap step penting
- Log error dengan detail
- Log performance metrics

### **2. Validation Strategy**
- Validasi bertahap (client-side, server-side)
- Error message yang jelas
- Fallback untuk data yang tidak valid

### **3. Database Strategy**
- Gunakan transaction untuk data integrity
- Periksa foreign key constraints
- Backup data secara regular

### **4. Testing Strategy**
- Unit test untuk setiap component
- Integration test untuk workflow
- Manual testing untuk edge cases

## Next Steps

### **1. Immediate Actions:**
1. Jalankan debug script untuk identifikasi masalah
2. Perbaiki validasi yang terlalu ketat
3. Tambahkan logging detail
4. Test dengan data minimal

### **2. Short Term:**
1. Implementasi error handling yang lebih baik
2. Tambahkan progress indicators
3. Implementasi auto-save
4. Tambahkan validation feedback

### **3. Long Term:**
1. Implementasi comprehensive testing
2. Monitoring dan alerting
3. Performance optimization
4. User experience improvement

## Status Implementasi

### ✅ **Selesai:**
- Validasi disederhanakan untuk testing
- Logging detail ditambahkan
- Debug script dibuat
- Test script dibuat

### 📋 **Hasil:**
- Identifikasi masalah lebih mudah
- Error tracking lebih detail
- Testing lebih sistematis
- Troubleshooting lebih efektif
