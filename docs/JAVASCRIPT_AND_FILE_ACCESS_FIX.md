# Perbaikan Masalah JavaScript dan File Access

## 🔍 **Masalah yang Ditemukan:**

### **1. JavaScript Error:**
```
admin-univ-usulan.js:127 Uncaught ReferenceError: $ is not defined
    at AdminUnivUsulan.initializeDataTables (admin-univ-usulan.js:127:9)
```

### **2. File Access Error:**
```
GET http://localhost/storage/pegawai-files/foto/oaKDrDeFDudjLDTRzRi9vPG3nnIvLPSVrcvWlV3t.jpg 403 (Forbidden)
```

## 🔧 **Solusi yang Diterapkan:**

### **1. Perbaikan JavaScript jQuery Error:**

#### **A. Sebelum Perbaikan:**
```javascript
// DataTables initialization
initializeDataTables() {
    // Initialize DataTables if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            }
        });
    }
}
```

#### **B. Setelah Perbaikan:**
```javascript
// DataTables initialization
initializeDataTables() {
    // Initialize DataTables if available
    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.DataTable !== 'undefined') {
        window.jQuery('.datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            }
        });
    } else {
        console.log('DataTables or jQuery not available');
    }
}
```

**Perubahan:**
- ✅ Menggunakan `window.jQuery` instead of `$`
- ✅ Menambahkan fallback logging
- ✅ Memastikan jQuery tersedia sebelum menggunakan DataTables

### **2. Perbaikan File Access Error:**

#### **A. Masalah Root Cause:**
- File foto disimpan di disk `public` tapi diakses melalui disk `local`
- Method `showDocument` menggunakan disk `local` untuk semua file
- Foto seharusnya diakses melalui disk `public`

#### **B. Perbaikan di DataPegawaiController:**

**1. Update Method showDocument:**
```php
// SEBELUM: Menggunakan disk local untuk semua file
$filePath = $pegawai->$field;
if (!$filePath || !Storage::disk('local')->exists($filePath)) {
    abort(404, 'File tidak ditemukan');
}

// SETELAH: Menggunakan disk yang sesuai
$filePath = $pegawai->$field;
if (!$filePath) {
    abort(404, 'File tidak ditemukan');
}

// Determine correct disk based on field type
$disk = $this->getFileDisk($field);
if (!Storage::disk($disk)->exists($filePath)) {
    abort(404, 'File tidak ditemukan');
}
```

**2. Tambah Method getFileDisk:**
```php
/**
 * Get the appropriate disk for a given field
 */
private function getFileDisk($field): string
{
    $sensitiveFiles = [
        'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
        'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
        'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
    ];

    return in_array($field, $sensitiveFiles) ? 'local' : 'public';
}
```

**3. Update File Path Access:**
```php
// SEBELUM: Menggunakan disk local
$fullPath = Storage::disk('local')->path($filePath);

// SETELAH: Menggunakan disk yang sesuai
$fullPath = Storage::disk($disk)->path($filePath);
```

## 📊 **Disk Configuration:**

### **1. File Storage Strategy:**
```php
// Sensitive Files (local disk - protected)
$sensitiveFiles = [
    'sk_pangkat_terakhir',
    'sk_jabatan_terakhir', 
    'ijazah_terakhir',
    'transkrip_nilai_terakhir',
    'sk_penyetaraan_ijazah',
    'disertasi_thesis_terakhir',
    'pak_konversi',
    'skp_tahun_pertama',
    'skp_tahun_kedua',
    'sk_cpns',
    'sk_pns'
];

// Public Files (public disk - accessible)
$publicFiles = [
    'foto'  // Profile photo
];
```

### **2. Filesystem Configuration:**
```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
        'serve' => true,
        'throw' => false,
        'report' => false,
    ],

    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
        'report' => false,
    ],
]
```

## 🎯 **Testing Steps:**

### **1. Test JavaScript Fix:**
1. Buka halaman admin universitas usulan
2. **Expected:** Tidak ada error jQuery di console
3. **Expected:** DataTables berfungsi jika jQuery tersedia

### **2. Test File Access Fix:**
1. Buka halaman yang menampilkan foto pegawai
2. **Expected:** Foto tampil dengan benar
3. **Expected:** Tidak ada error 403 Forbidden
4. **Expected:** Dokumen sensitif tetap terlindungi

### **3. Test Document Access:**
1. Akses dokumen pegawai melalui route
2. **Expected:** Foto diakses melalui disk public
3. **Expected:** Dokumen sensitif diakses melalui disk local
4. **Expected:** Access control tetap berfungsi

## 🔄 **Expected Data Flow:**

### **1. File Access Flow:**
```
Request → Route → Controller → getFileDisk() → 
Storage::disk($disk) → File Serve → Response
```

### **2. JavaScript Flow:**
```
Page Load → AdminUnivUsulan.init() → 
initializeDataTables() → jQuery Check → 
DataTables Init (if available)
```

## ✅ **Expected Outcome:**

Setelah perbaikan, sistem seharusnya:
- ✅ **JavaScript:** Tidak ada error jQuery undefined
- ✅ **File Access:** Foto pegawai dapat diakses tanpa error 403
- ✅ **Security:** Dokumen sensitif tetap terlindungi
- ✅ **Performance:** File access menggunakan disk yang tepat
- ✅ **Fallback:** Logging untuk debugging jika ada masalah

## 🚀 **Additional Recommendations:**

### **1. jQuery Loading:**
```html
<!-- Pastikan jQuery dimuat sebelum script custom -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/js/admin-universitas/admin-univ-usulan.js"></script>
```

### **2. File Access Monitoring:**
```php
// Tambahkan logging untuk monitoring
\Log::info('File accessed', [
    'field' => $field,
    'disk' => $disk,
    'path' => $filePath,
    'user_id' => Auth::id()
]);
```

### **3. Error Handling:**
```javascript
// Tambahkan error handling untuk DataTables
try {
    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.DataTable !== 'undefined') {
        window.jQuery('.datatable').DataTable({
            // ... configuration
        });
    }
} catch (error) {
    console.error('DataTables initialization failed:', error);
}
```

---

**🔧 Fixes Applied - Ready for Testing!**

**Next Steps:**
1. **Test JavaScript** - Pastikan tidak ada error jQuery
2. **Test File Access** - Pastikan foto dapat diakses
3. **Test Security** - Pastikan dokumen sensitif terlindungi
4. **Monitor Logs** - Periksa log untuk debugging
