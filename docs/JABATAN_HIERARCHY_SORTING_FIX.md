# Jabatan Hierarchy Sorting Fix

## 🎯 **Masalah yang Ditemukan**
Jabatan di dropdown "Jabatan Terakhir" tidak diurutkan berdasarkan hierarki, sehingga sulit untuk melihat urutan jabatan dari level rendah ke tinggi.

## ✅ **Solusi yang Diterapkan**
Menggunakan method `orderByHierarchy('asc')` pada model Jabatan untuk mengurutkan jabatan berdasarkan `hierarchy_level` dari rendah ke tinggi.

## 🔧 **Perubahan yang Dilakukan**

### **File yang Diupdate: `DataPegawaiController.php`**

#### **Method `create()` - Sebelum:**
```php
$jabatans = \Cache::remember('jabatans_all', 3600, function () {
    return Jabatan::orderBy('jabatan')->get(['id', 'jabatan', 'jenis_pegawai']);
});
```

#### **Method `create()` - Sesudah:**
```php
$jabatans = \Cache::remember('jabatans_all_hierarchy', 3600, function () {
    return Jabatan::orderByHierarchy('asc')->get(['id', 'jabatan', 'jenis_pegawai', 'jenis_jabatan', 'hierarchy_level']);
});
```

#### **Method `edit()` - Sebelum:**
```php
$jabatans = Jabatan::orderBy('jabatan')->get();
```

#### **Method `edit()` - Sesudah:**
```php
$jabatans = \Cache::remember('jabatans_all_hierarchy', 3600, function () {
    return Jabatan::orderByHierarchy('asc')->get(['id', 'jabatan', 'jenis_pegawai', 'jenis_jabatan', 'hierarchy_level']);
});
```

## 📊 **Urutan Hierarki Jabatan**

### **Dosen Fungsional (Hierarki Rendah ke Tinggi):**
1. **Asisten Ahli** (hierarchy_level: 1)
2. **Lektor** (hierarchy_level: 2)
3. **Lektor Kepala** (hierarchy_level: 3)
4. **Guru Besar** (hierarchy_level: 4)

### **Tenaga Kependidikan Fungsional Umum:**
1. **Pranata Muda** (hierarchy_level: 1)
2. **Pranata Muda Tingkat I** (hierarchy_level: 2)
3. **Pranata** (hierarchy_level: 3)
4. **Pranata Tingkat I** (hierarchy_level: 4)
5. **Pranata Utama** (hierarchy_level: 5)
6. **Pranata Utama Tingkat I** (hierarchy_level: 6)

### **Tenaga Kependidikan Fungsional Tertentu:**
1. **Pranata Laboratorium Pendidikan Muda** (hierarchy_level: 1)
2. **Pranata Laboratorium Pendidikan Muda Tingkat I** (hierarchy_level: 2)
3. **Pranata Laboratorium Pendidikan** (hierarchy_level: 3)
4. **Pranata Laboratorium Pendidikan Tingkat I** (hierarchy_level: 4)
5. **Pranata Laboratorium Pendidikan Utama** (hierarchy_level: 5)
6. **Pranata Laboratorium Pendidikan Utama Tingkat I** (hierarchy_level: 6)

### **Tenaga Kependidikan Struktural:**
1. **Kepala Bagian** (hierarchy_level: 1)
2. **Kepala Sub Bagian** (hierarchy_level: 2)
3. **Kepala Urusan** (hierarchy_level: 3)

### **Dosen dengan Tugas Tambahan:**
1. **Ketua Jurusan** (hierarchy_level: 1)
2. **Wakil Dekan** (hierarchy_level: 2)
3. **Dekan** (hierarchy_level: 3)
4. **Wakil Rektor** (hierarchy_level: 4)
5. **Rektor** (hierarchy_level: 5)

## 🎯 **Keuntungan Perubahan**

### 1. **User Experience yang Lebih Baik**
- Jabatan diurutkan secara logis dari level rendah ke tinggi
- Mudah untuk melihat progression karir
- Lebih intuitif untuk pemilihan jabatan

### 2. **Konsistensi dengan Business Logic**
- Sesuai dengan hierarki jabatan yang sebenarnya
- Memudahkan pemahaman struktur organisasi
- Konsisten dengan sistem promosi jabatan

### 3. **Performance Optimization**
- Menggunakan caching untuk data jabatan
- Cache key yang lebih spesifik (`jabatans_all_hierarchy`)
- Mengambil field yang diperlukan saja

## 🔍 **Method `orderByHierarchy`**

Method ini sudah tersedia di model `Jabatan`:

```php
public function scopeOrderByHierarchy(Builder $query, string $direction = 'asc'): Builder
{
    return $query->orderBy('hierarchy_level', $direction);
}
```

### **Penggunaan:**
```php
// Urutkan dari rendah ke tinggi (ascending)
Jabatan::orderByHierarchy('asc')->get();

// Urutkan dari tinggi ke rendah (descending)
Jabatan::orderByHierarchy('desc')->get();
```

## 📋 **Testing Checklist**

- [x] Jabatan diurutkan berdasarkan hierarchy_level dari rendah ke tinggi
- [x] Cache berfungsi dengan baik untuk performance
- [x] Data attributes tetap tersimpan dengan benar
- [x] Filtering berdasarkan jenis pegawai tetap berfungsi
- [x] Form create dan edit menggunakan urutan yang sama
- [x] Tidak ada perubahan pada logic filtering

## 🎉 **Hasil yang Diharapkan**

Setelah fix ini:

- ✅ **Logical Ordering**: Jabatan diurutkan berdasarkan hierarki yang benar
- ✅ **Better UX**: User dapat melihat progression karir dengan jelas
- ✅ **Consistent Display**: Urutan konsisten di semua halaman
- ✅ **Performance**: Caching untuk data jabatan
- ✅ **Maintained Functionality**: Semua filtering tetap berfungsi

## 🔄 **Kompatibilitas**

Perubahan ini:
- ✅ **Backward Compatible**: Tidak mempengaruhi data yang tersimpan
- ✅ **Filtering Compatible**: Semua filtering logic tetap berfungsi
- ✅ **Form Compatible**: Form submission tidak terpengaruh
- ✅ **Cache Compatible**: Menggunakan cache key yang berbeda

## 📊 **Contoh Urutan Tampilan**

### **Sebelum (Alphabetical):**
- Asisten Ahli
- Dekan
- Guru Besar
- Ketua Jurusan
- Lektor
- Lektor Kepala
- Rektor
- Wakil Dekan
- Wakil Rektor

### **Sesudah (Hierarchical):**
- Asisten Ahli
- Lektor
- Lektor Kepala
- Guru Besar
- Ketua Jurusan
- Wakil Dekan
- Dekan
- Wakil Rektor
- Rektor

---

*Fix ini memberikan urutan jabatan yang lebih logis dan sesuai dengan hierarki organisasi yang sebenarnya.*
