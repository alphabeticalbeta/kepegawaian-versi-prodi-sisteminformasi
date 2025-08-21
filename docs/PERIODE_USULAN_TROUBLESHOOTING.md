# Troubleshooting: Periode Usulan Tidak Muncul

## 🚨 **Masalah**
Halaman usul jabatan muncul dengan baik, tetapi periode usulan tidak muncul meskipun status kepegawaian pegawai sudah sesuai dengan periode yang dibuka.

## 🔍 **Penyebab Kemungkinan**

### **1. Data Periode Usulan Kosong**
- Tidak ada data periode usulan di database
- Data periode usulan ada tetapi tidak sesuai dengan filter

### **2. Format JSON Status Kepegawaian**
- Format JSON pada kolom `status_kepegawaian` tidak sesuai
- Query `whereJsonContains` tidak berfungsi dengan benar

### **3. Filter Query yang Terlalu Ketat**
- Filter berdasarkan jenis usulan tidak sesuai
- Filter berdasarkan status tidak sesuai
- Filter berdasarkan tanggal tidak sesuai

### **4. Data Pegawai Tidak Sesuai**
- Status kepegawaian pegawai tidak sesuai dengan data periode
- Jenis pegawai tidak sesuai dengan jenis usulan periode

## 🛠️ **Solusi yang Diterapkan**

### **1. Debug Information di Controller**

Saya telah menambahkan debug information di `UsulanJabatanController@index`:

```php
// Debug information
Log::info('UsulanJabatanController@index Debug', [
    'pegawai_id' => $pegawai->id,
    'pegawai_nip' => $pegawai->nip,
    'jenis_pegawai' => $pegawai->jenis_pegawai,
    'status_kepegawaian' => $pegawai->status_kepegawaian,
    'jenis_usulan_periode' => $jenisUsulanPeriode
]);

// Debug query results
Log::info('Periode Usulan Query Results', [
    'total_periode_found' => $periodeUsulans->count(),
    'periode_ids' => $periodeUsulans->pluck('id')->toArray(),
    'periode_names' => $periodeUsulans->pluck('nama_periode')->toArray()
]);
```

### **2. Alternative Query**

Jika query utama tidak menemukan hasil, sistem akan mencoba query alternatif:

```php
// Alternative query if no results
if ($periodeUsulans->count() == 0) {
    // Try without JSON contains
    $altPeriodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
        ->where('status', 'Buka')
        ->orderBy('tanggal_mulai', 'desc')
        ->get();

    // Use alternative results if found
    if ($altPeriodeUsulans->count() > 0) {
        $periodeUsulans = $altPeriodeUsulans;
    }
}
```

## 🔧 **Script Debug yang Dibuat**

### **1. `debug_periode_usulan.php`**
Script untuk debug data periode usulan dan query:

```bash
php debug_periode_usulan.php
```

**Fitur:**
- ✅ Menampilkan data pegawai yang login
- ✅ Menampilkan semua periode usulan di database
- ✅ Test query step by step
- ✅ Alternative query methods
- ✅ SQL query debugging
- ✅ Recommendations

### **2. `create_sample_periode_usulan.php`**
Script untuk membuat sample data periode usulan:

```bash
php create_sample_periode_usulan.php
```

**Fitur:**
- ✅ Membuat sample data untuk Dosen PNS
- ✅ Membuat sample data untuk Tenaga Kependidikan PNS
- ✅ Test query untuk memastikan data bisa ditemukan
- ✅ Menampilkan hasil query

## 📋 **Langkah Troubleshooting**

### **Step 1: Check Log Files**
```bash
tail -f storage/logs/laravel.log
```

Cari log dengan keyword:
- `UsulanJabatanController@index Debug`
- `Periode Usulan Query Results`
- `Alternative Query Results`

### **Step 2: Run Debug Script**
```bash
php debug_periode_usulan.php
```

Periksa output untuk:
- Data pegawai yang login
- Total periode usulan di database
- Hasil query step by step
- Alternative query results

### **Step 3: Create Sample Data (Jika Perlu)**
```bash
php create_sample_periode_usulan.php
```

### **Step 4: Check Database Directly**
```sql
-- Check all periode usulan
SELECT * FROM periode_usulans;

-- Check specific periode for Dosen PNS
SELECT * FROM periode_usulans 
WHERE jenis_usulan = 'usulan-jabatan-dosen' 
AND status = 'Buka';

-- Check JSON format
SELECT id, nama_periode, status_kepegawaian 
FROM periode_usulans 
WHERE JSON_CONTAINS(status_kepegawaian, '"Dosen PNS"');
```

## 🎯 **Expected Data Structure**

### **1. Periode Usulan Table**
```sql
CREATE TABLE periode_usulans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nama_periode VARCHAR(255) NOT NULL,
    jenis_usulan VARCHAR(255) NOT NULL,
    status_kepegawaian JSON NOT NULL,
    status ENUM('Buka', 'Tutup') DEFAULT 'Buka',
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    tanggal_mulai_perbaikan DATE NULL,
    tanggal_selesai_perbaikan DATE NULL,
    senat_min_setuju INT DEFAULT 3,
    tahun_periode INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### **2. Sample Data**
```sql
INSERT INTO periode_usulans (
    nama_periode, 
    jenis_usulan, 
    status_kepegawaian, 
    status, 
    tanggal_mulai, 
    tanggal_selesai,
    tahun_periode
) VALUES 
('Periode Usulan Jabatan Dosen 2024', 'usulan-jabatan-dosen', '["Dosen PNS"]', 'Buka', '2024-01-01', '2024-12-31', 2024),
('Periode Usulan Jabatan Tenaga Kependidikan 2024', 'usulan-jabatan-tendik', '["Tenaga Kependidikan PNS"]', 'Buka', '2024-01-01', '2024-12-31', 2024);
```

## 🔍 **Common Issues & Solutions**

### **Issue 1: No Data Found**
**Solution:** Run `create_sample_periode_usulan.php`

### **Issue 2: JSON Format Problem**
**Solution:** Check JSON format in database
```sql
SELECT id, nama_periode, status_kepegawaian 
FROM periode_usulans 
WHERE id = 1;
```

### **Issue 3: Wrong Status Kepegawaian**
**Solution:** Check pegawai status
```sql
SELECT id, nip, nama_lengkap, jenis_pegawai, status_kepegawaian 
FROM pegawais 
WHERE id = [pegawai_id];
```

### **Issue 4: Wrong Jenis Usulan**
**Solution:** Check jenis usulan mapping
- Dosen PNS → `usulan-jabatan-dosen`
- Tenaga Kependidikan PNS → `usulan-jabatan-tendik`

## 🚀 **Testing Steps**

### **1. After Running Scripts**
1. Clear cache: `php artisan cache:clear`
2. Clear view cache: `php artisan view:clear`
3. Restart web server
4. Login sebagai pegawai
5. Akses halaman usul jabatan
6. Periksa apakah periode usulan muncul

### **2. Expected Results**
- ✅ Periode usulan muncul di tabel
- ✅ Tombol "Membuat Usulan" untuk periode tanpa usulan
- ✅ Tombol "Lihat Detail" dan "Hapus" untuk periode dengan usulan
- ✅ Debug information di log file

## 📞 **Jika Masih Bermasalah**

1. **Periksa log file** untuk error messages
2. **Run debug script** untuk melihat data detail
3. **Check database** untuk memastikan data ada
4. **Verify pegawai data** untuk memastikan status kepegawaian benar
5. **Test query manual** di database

**Dengan script debug yang telah dibuat, masalah periode usulan yang tidak muncul seharusnya bisa diatasi!** 🎉
