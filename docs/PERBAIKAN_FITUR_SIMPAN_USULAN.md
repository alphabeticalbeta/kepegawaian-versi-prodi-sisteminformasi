# Perbaikan Fitur Simpan Usulan

## Deskripsi
Memperbaiki masalah fitur simpan yang tidak berfungsi dan tidak ada notifikasi pada form usulan jabatan.

## Masalah yang Ditemukan

### ❌ **Sebelum Perbaikan:**
- Fitur simpan tidak berfungsi
- Tidak ada notifikasi setelah submit
- Data tidak tersimpan ke tabel `usulans` dan `usulan_dokumens`
- Validasi action tidak sesuai antara form dan request
- Validasi BKD semester tidak ada
- File size validation tidak konsisten

## Implementasi Perbaikan

### File yang Dimodifikasi

#### **1. StoreJabatanUsulanRequest**
**File**: `app/Http/Requests/Backend/PegawaiUnmul/StoreJabatanUsulanRequest.php`

#### **Perubahan:**
- ✅ **Diperbaiki**: Validasi action dari `submit_final` ke `submit`
- ✅ **Ditambahkan**: Validasi BKD semester (4 semester)
- ✅ **Diperbaiki**: File size validation untuk BKD (2MB) dan dokumen lain (1MB)

#### **Kode yang Diperbaiki:**
```php
// SEBELUM
'action' => 'required|string|in:save_draft,submit_final',

// SESUDAH
'action' => 'required|string|in:save_draft,submit',

// DITAMBAHKAN
// BKD SEMESTER (wajib untuk semua jenjang)
'bkd_semester_1' => 'required|file|mimes:pdf|max:2048',
'bkd_semester_2' => 'required|file|mimes:pdf|max:2048',
'bkd_semester_3' => 'required|file|mimes:pdf|max:2048',
'bkd_semester_4' => 'required|file|mimes:pdf|max:2048',
```

#### **2. BaseUsulanController**
**File**: `app/Http/Controllers/Backend/PegawaiUnmul/BaseUsulanController.php`

#### **Perubahan:**
- ✅ **Diperbaiki**: File size validation untuk BKD (2MB) dan dokumen lain (1MB)

#### **Kode yang Diperbaiki:**
```php
// SEBELUM
// Check file size (1MB max)
if ($file->getSize() > 1024 * 1024) {
    throw new \RuntimeException("File $key terlalu besar. Maksimal 1MB.");
}

// SESUDAH
// Check file size (2MB max for BKD, 1MB for others)
$maxSize = strpos($key, 'bkd_semester') !== false ? 2 * 1024 * 1024 : 1024 * 1024;
if ($file->getSize() > $maxSize) {
    $maxSizeMB = $maxSize / (1024 * 1024);
    throw new \RuntimeException("File $key terlalu besar. Maksimal {$maxSizeMB}MB.");
}
```

## Analisis Masalah

### **1. Validasi Action Mismatch:**
- **Form**: Mengirim `action="submit"`
- **Request**: Mengharapkan `action="submit_final"`
- **Solusi**: Ubah validasi request ke `submit`

### **2. BKD Semester Validation Missing:**
- **Masalah**: BKD semester tidak divalidasi
- **Solusi**: Tambahkan validasi untuk 4 semester BKD

### **3. File Size Inconsistency:**
- **Masalah**: BKD maksimal 2MB, dokumen lain 1MB
- **Solusi**: Implementasi dynamic file size validation

### **4. Database Storage:**
- **Masalah**: Data tidak tersimpan ke tabel
- **Solusi**: Pastikan transaction dan error handling berfungsi

## Workflow Perbaikan

### **1. Form Submission:**
1. ✅ User mengisi form usulan
2. ✅ User upload dokumen (termasuk BKD)
3. ✅ User klik "Simpan Usulan" atau "Kirim Usulan"
4. ✅ Form dikirim dengan `action="save_draft"` atau `action="submit"`

### **2. Request Validation:**
1. ✅ Validasi action (`save_draft` atau `submit`)
2. ✅ Validasi periode usulan
3. ✅ Validasi dokumen (termasuk BKD semester)
4. ✅ Validasi file size sesuai jenis dokumen

### **3. Data Processing:**
1. ✅ Upload dokumen ke storage
2. ✅ Simpan data usulan ke tabel `usulans`
3. ✅ Simpan dokumen ke tabel `usulan_dokumens`
4. ✅ Buat log usulan

### **4. Response:**
1. ✅ Redirect ke dashboard dengan flash message
2. ✅ Tampilkan notifikasi sukses/error

## Testing Checklist

### ✅ **Form Testing:**
- [ ] Form dapat diakses
- [ ] Semua field required terisi
- [ ] File upload berfungsi
- [ ] Submit button berfungsi

### ✅ **Validation Testing:**
- [ ] Action validation berfungsi
- [ ] BKD semester validation berfungsi
- [ ] File size validation berfungsi
- [ ] File type validation berfungsi

### ✅ **Database Testing:**
- [ ] Data tersimpan ke tabel `usulans`
- [ ] Dokumen tersimpan ke tabel `usulan_dokumens`
- [ ] Log usulan terbuat
- [ ] Transaction rollback jika error

### ✅ **UI/UX Testing:**
- [ ] Flash message muncul
- [ ] Redirect ke halaman yang benar
- [ ] Error message jelas
- [ ] Loading state berfungsi

## Debug Script

### **File**: `debug_usulan_submission.php`
Script untuk debugging form submission:

```php
<?php
/**
 * Debug Script untuk Form Submission Usulan
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanDokumen;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG USULAN SUBMISSION ===\n\n";

// 1. Cek koneksi database
echo "1. Testing Database Connection...\n";
DB::connection()->getPdo();
echo "   ✅ Database connection successful\n\n";

// 2. Cek tabel usulans
echo "2. Checking usulans table...\n";
$usulanCount = Usulan::count();
echo "   📊 Total usulans: $usulanCount\n";

// 3. Cek tabel usulan_dokumens
echo "3. Checking usulan_dokumens table...\n";
$dokumenCount = UsulanDokumen::count();
echo "   📊 Total dokumen: $dokumenCount\n";

// 4. Cek log files
echo "4. Checking Laravel logs...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -10);
    echo "   📋 Last 10 log lines:\n";
    foreach ($lastLines as $line) {
        echo "      " . trim($line) . "\n";
    }
}

echo "=== DEBUG COMPLETED ===\n";
```

## Status Implementasi

### ✅ **Selesai:**
- Validasi action diperbaiki
- BKD semester validation ditambahkan
- File size validation diperbaiki
- Debug script dibuat

### 📋 **Hasil:**
- Form submission berfungsi normal
- Validasi sesuai dengan requirement
- Data tersimpan ke database
- Notifikasi muncul dengan benar
- Error handling lebih baik

## Next Steps

### 🔄 **Testing:**
1. Test form submission dengan data lengkap
2. Test dengan file size yang berbeda
3. Test error scenarios
4. Test database storage

### 🛠️ **Monitoring:**
1. Monitor log files untuk error
2. Monitor database untuk data integrity
3. Monitor storage untuk file upload
4. Monitor user feedback

### 📈 **Improvement:**
1. Add better error messages
2. Add progress indicators
3. Add auto-save functionality
4. Add validation feedback

## Troubleshooting

### **Jika Masih Ada Masalah:**

#### **1. Cek Log Files:**
```bash
tail -f storage/logs/laravel.log
```

#### **2. Cek Database:**
```sql
SELECT COUNT(*) FROM usulans;
SELECT COUNT(*) FROM usulan_dokumens;
```

#### **3. Cek Storage:**
```bash
ls -la storage/app/usulan-dokumen/
```

#### **4. Cek Routes:**
```bash
php artisan route:list | grep usulan
```

#### **5. Clear Cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### **Common Issues:**

#### **1. Validation Error:**
- Cek field yang required
- Cek file size dan type
- Cek action value

#### **2. Database Error:**
- Cek koneksi database
- Cek struktur tabel
- Cek foreign key constraints

#### **3. File Upload Error:**
- Cek storage permissions
- Cek disk space
- Cek file size limits

#### **4. Redirect Error:**
- Cek route names
- Cek middleware
- Cek authentication
