# Perbaikan Halaman Periode Usulan & Form Periode

## 🔍 **Masalah yang Ditemukan:**

### **1. Periode Tidak Muncul di Dashboard:**
- Periode yang sudah dibuka tidak muncul pada daftar (index/dashboard) sesuai jenis usulan yang dipilih di sidebar
- Dashboard hanya menampilkan periode dengan exact match, tidak termasuk sub-jenis

### **2. Judul Form Statis:**
- Judul & label form membuat periode selalu menampilkan "Buat Usulan Jabatan"
- Tidak dinamis mengikuti jenis usulan yang dipilih

## 🔧 **Solusi yang Diterapkan:**

### **1. Perbaikan DashboardPeriodeController:**

#### **A. Query Periode yang Lebih Fleksibel:**
```php
// SEBELUM: Hanya exact match
$periodes = PeriodeUsulan::where('jenis_usulan', $namaUsulan)
    ->withCount([...])
    ->orderBy('created_at', 'desc')
    ->get();

// SETELAH: Include sub-jenis untuk jabatan
$periodes = PeriodeUsulan::where(function($query) use ($namaUsulan, $jenisUsulan) {
        // Exact match untuk jenis usulan utama
        $query->where('jenis_usulan', $namaUsulan);
        
        // Jika jenis usulan adalah jabatan, juga ambil sub-jenis
        if ($jenisUsulan === 'jabatan') {
            $query->orWhereIn('jenis_usulan', ['usulan-jabatan-dosen', 'usulan-jabatan-tendik']);
        }
    })
    ->withCount([...])
    ->orderBy('created_at', 'desc')
    ->get();
```

**Perubahan:**
- ✅ Menambahkan logic untuk include sub-jenis jabatan
- ✅ Menggunakan closure untuk complex query
- ✅ Mempertahankan exact match untuk jenis usulan lain

### **2. Perbaikan PeriodeUsulanController:**

#### **A. Method Create - Pass Data Dinamis:**
```php
// SEBELUM: Hanya jenis_usulan_otomatis
return view($view, [
    'jenis_usulan_otomatis' => $jenisUsulanOtomatis
]);

// SETELAH: Tambah data untuk judul dinamis
return view($view, [
    'jenis_usulan_otomatis' => $jenisUsulanOtomatis,
    'jenis_usulan_key' => $jenisUsulan,
    'nama_usulan' => $jenisUsulanOtomatis
]);
```

#### **B. Method Edit - Pass Nama Usulan:**
```php
// SEBELUM: Hanya periode
return view('backend.layouts.views.periode-usulan.form', [
    'periode' => $periodeUsulan
]);

// SETELAH: Tambah nama_usulan untuk judul
return view('backend.layouts.views.periode-usulan.form', [
    'periode' => $periodeUsulan,
    'nama_usulan' => $periodeUsulan->jenis_usulan
]);
```

#### **C. Method Store/Update/Destroy - Redirect yang Benar:**
```php
// Mapping untuk redirect yang konsisten
$jenisMapping = [
    'Usulan Jabatan' => 'jabatan',
    'usulan-jabatan-dosen' => 'jabatan',
    'usulan-jabatan-tendik' => 'jabatan',
    'Usulan NUPTK' => 'nuptk',
    'Usulan Laporan LKD' => 'laporan-lkd',
    'Usulan Presensi' => 'presensi',
    'Usulan Penyesuaian Masa Kerja' => 'penyesuaian-masa-kerja',
    'Usulan Ujian Dinas & Ijazah' => 'ujian-dinas-ijazah',
    'Usulan Laporan Serdos' => 'laporan-serdos',
    'Usulan Pensiun' => 'pensiun',
    'Usulan Kepangkatan' => 'kepangkatan',
    'Usulan Pencantuman Gelar' => 'pencantuman-gelar',
    'Usulan ID SINTA ke SISTER' => 'id-sinta-sister',
    'Usulan Satyalancana' => 'satyalancana',
    'Usulan Tugas Belajar' => 'tugas-belajar',
    'Usulan Pengaktifan Kembali' => 'pengaktifan-kembali'
];

$jenisKey = $jenisMapping[$validated['jenis_usulan']] ?? 'jabatan';

return redirect()->route('backend.admin-univ-usulan.dashboard-periode.index', ['jenis' => $jenisKey])
    ->with('success', 'Periode usulan berhasil dibuat!');
```

### **3. Perbaikan Form View:**

#### **A. Judul Dinamis:**
```php
// SEBELUM: Judul statis
<h3 class="text-lg font-semibold text-white">
    {{ isset($periode) ? 'Edit Periode Usulan Jabatan' : 'Tambah Periode Usulan Jabatan' }}
</h3>

// SETELAH: Judul dinamis
<h3 class="text-lg font-semibold text-white">
    {{ isset($periode) ? 'Edit Periode ' . ($nama_usulan ?? 'Usulan Jabatan') : 'Tambah Periode ' . ($nama_usulan ?? 'Usulan Jabatan') }}
</h3>
```

#### **B. Deskripsi Dinamis:**
```php
// SEBELUM: Deskripsi statis
<p class="text-indigo-100 text-sm mt-1">
    Kelola periode untuk usulan jabatan dosen dan tenaga kependidikan
</p>

// SETELAH: Deskripsi dinamis
<p class="text-indigo-100 text-sm mt-1">
    Kelola periode untuk {{ $nama_usulan ?? 'usulan jabatan dosen dan tenaga kependidikan' }}
</p>
```

#### **C. Dropdown Jenis Usulan Lengkap:**
```php
<select name="jenis_usulan" id="jenis_usulan" required>
    <option value="">Pilih Jenis Usulan</option>
    <option value="Usulan Jabatan">Usulan Jabatan</option>
    <option value="usulan-jabatan-dosen">Usulan Jabatan Dosen</option>
    <option value="usulan-jabatan-tendik">Usulan Jabatan Tenaga Kependidikan</option>
    <option value="Usulan NUPTK">Usulan NUPTK</option>
    <option value="Usulan Laporan LKD">Usulan Laporan LKD</option>
    <option value="Usulan Presensi">Usulan Presensi</option>
    <option value="Usulan Penyesuaian Masa Kerja">Usulan Penyesuaian Masa Kerja</option>
    <option value="Usulan Ujian Dinas & Ijazah">Usulan Ujian Dinas & Ijazah</option>
    <option value="Usulan Laporan Serdos">Usulan Laporan Serdos</option>
    <option value="Usulan Pensiun">Usulan Pensiun</option>
    <option value="Usulan Kepangkatan">Usulan Kepangkatan</option>
    <option value="Usulan Pencantuman Gelar">Usulan Pencantuman Gelar</option>
    <option value="Usulan ID SINTA ke SISTER">Usulan ID SINTA ke SISTER</option>
    <option value="Usulan Satyalancana">Usulan Satyalancana</option>
    <option value="Usulan Tugas Belajar">Usulan Tugas Belajar</option>
    <option value="Usulan Pengaktifan Kembali">Usulan Pengaktifan Kembali</option>
</select>
```

## 📊 **Data Flow yang Diperbaiki:**

### **1. Dashboard Periode Flow:**
```
Sidebar Click → DashboardPeriodeController@index → 
Query Periode (include sub-jenis) → View dengan data lengkap
```

### **2. Form Periode Flow:**
```
Create Button → PeriodeUsulanController@create → 
Pass nama_usulan → View dengan judul dinamis
```

### **3. CRUD Operations Flow:**
```
Form Submit → Controller Method → 
Mapping jenis_usulan → Redirect ke dashboard yang tepat
```

## 🎯 **Testing Steps:**

### **1. Test Dashboard Periode:**
1. Buka sidebar dan pilih jenis usulan tertentu
2. **Expected:** Dashboard menampilkan periode sesuai jenis usulan
3. **Expected:** Untuk jabatan, tampilkan semua sub-jenis (dosen & tendik)
4. **Expected:** Statistik terupdate sesuai data yang ditampilkan

### **2. Test Form Create:**
1. Klik "Buat Periode Usulan" dari dashboard
2. **Expected:** Form terbuka dengan judul dinamis sesuai jenis usulan
3. **Expected:** Dropdown jenis usulan terisi dengan semua opsi
4. **Expected:** Jenis usulan otomatis terpilih sesuai dari dashboard

### **3. Test Form Edit:**
1. Klik tombol edit pada periode tertentu
2. **Expected:** Form terbuka dengan judul "Edit Periode [Nama Usulan]"
3. **Expected:** Data periode terisi dengan benar
4. **Expected:** Jenis usulan terpilih sesuai data

### **4. Test CRUD Operations:**
1. **Create:** Submit form create
   - **Expected:** Redirect ke dashboard dengan jenis usulan yang sesuai
   - **Expected:** Notifikasi sukses muncul
   - **Expected:** Data periode muncul di daftar

2. **Update:** Submit form edit
   - **Expected:** Redirect ke dashboard dengan jenis usulan yang sesuai
   - **Expected:** Notifikasi sukses muncul
   - **Expected:** Data periode terupdate

3. **Delete:** Klik tombol delete
   - **Expected:** Redirect ke dashboard dengan jenis usulan yang sesuai
   - **Expected:** Notifikasi sukses muncul
   - **Expected:** Data periode hilang dari daftar

## ✅ **Expected Outcome:**

Setelah perbaikan, sistem seharusnya:
- ✅ **Dashboard:** Menampilkan periode sesuai jenis usulan yang dipilih
- ✅ **Form Create:** Judul dinamis sesuai jenis usulan
- ✅ **Form Edit:** Judul dinamis sesuai jenis usulan
- ✅ **Dropdown:** Semua jenis usulan tersedia
- ✅ **Redirect:** Kembali ke dashboard yang tepat setelah CRUD
- ✅ **Data Consistency:** Periode muncul di dashboard yang sesuai

## 🔄 **Mapping Jenis Usulan:**

### **1. Sidebar to Controller Mapping:**
```php
'jabatan' => 'Usulan Jabatan'
'nuptk' => 'Usulan NUPTK'
'laporan-lkd' => 'Usulan Laporan LKD'
'presensi' => 'Usulan Presensi'
'penyesuaian-masa-kerja' => 'Usulan Penyesuaian Masa Kerja'
'ujian-dinas-ijazah' => 'Usulan Ujian Dinas & Ijazah'
'laporan-serdos' => 'Usulan Laporan Serdos'
'pensiun' => 'Usulan Pensiun'
'kepangkatan' => 'Usulan Kepangkatan'
'pencantuman-gelar' => 'Usulan Pencantuman Gelar'
'id-sinta-sister' => 'Usulan ID SINTA ke SISTER'
'satyalancana' => 'Usulan Satyalancana'
'tugas-belajar' => 'Usulan Tugas Belajar'
'pengaktifan-kembali' => 'Usulan Pengaktifan Kembali'
```

### **2. Sub-jenis untuk Jabatan:**
```php
'usulan-jabatan-dosen' => 'Usulan Jabatan Dosen'
'usulan-jabatan-tendik' => 'Usulan Jabatan Tenaga Kependidikan'
```

## 🚀 **Additional Improvements:**

### **1. Error Handling:**
```php
// Tambahkan fallback untuk mapping yang tidak ditemukan
$jenisKey = $jenisMapping[$jenisUsulan] ?? 'jabatan';
```

### **2. Logging untuk Debug:**
```php
\Log::info('DashboardPeriodeController - Mapping Debug', [
    'jenisUsulan' => $jenisUsulan,
    'namaUsulan' => $namaUsulan,
    'totalPeriodes' => $periodes->count()
]);
```

### **3. Validation:**
```php
// Pastikan jenis usulan valid
$allowedJenisUsulan = array_keys($jenisMapping);
if (!in_array($jenisUsulan, $allowedJenisUsulan)) {
    abort(404, 'Jenis usulan tidak valid');
}
```

---

**🔧 Fixes Applied - Ready for Testing!**

**Next Steps:**
1. **Test Dashboard** - Pastikan periode muncul sesuai jenis usulan
2. **Test Form Create** - Pastikan judul dan dropdown dinamis
3. **Test Form Edit** - Pastikan judul dan data terisi benar
4. **Test CRUD Operations** - Pastikan redirect dan notifikasi benar
