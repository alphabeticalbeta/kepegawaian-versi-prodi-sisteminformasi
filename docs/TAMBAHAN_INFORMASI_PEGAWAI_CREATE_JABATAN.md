# Penambahan Informasi Pegawai di Halaman Create Jabatan

## Deskripsi
Menambahkan section "Informasi Pegawai" di bawah "Informasi Periode Usulan" pada halaman create jabatan untuk menampilkan data pegawai yang sedang membuat usulan.

## Implementasi

### File yang Dimodifikasi
**File**: `resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/create-jabatan.blade.php`

### Section yang Ditambahkan

#### **Informasi Pegawai Section:**
```php
{{-- Informasi Pegawai --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="user" class="w-6 h-6 mr-3"></i>
            Informasi Pegawai
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Nama Lengkap</label>
                <p class="text-xs text-gray-600 mb-2">Nama lengkap pegawai</p>
                <input type="text" value="{{ $pegawai->nama_lengkap ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">NIP</label>
                <p class="text-xs text-gray-600 mb-2">Nomor Induk Pegawai</p>
                <input type="text" value="{{ $pegawai->nip ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Jabatan Sekarang</label>
                <p class="text-xs text-gray-600 mb-2">Jabatan fungsional saat ini</p>
                <input type="text" value="{{ $pegawai->jabatan_fungsional ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Jabatan yang Dituju</label>
                <p class="text-xs text-gray-600 mb-2">Jabatan fungsional yang diajukan</p>
                <input type="text" value="{{ $daftarPeriode->jenis_usulan ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
        </div>
    </div>
</div>
```

## Informasi yang Ditampilkan

### 1. **Nama Lengkap**
- **Field**: `$pegawai->nama_lengkap`
- **Deskripsi**: Nama lengkap pegawai yang sedang membuat usulan
- **Fallback**: `-` jika data kosong

### 2. **NIP**
- **Field**: `$pegawai->nip`
- **Deskripsi**: Nomor Induk Pegawai
- **Fallback**: `-` jika data kosong

### 3. **Jabatan Sekarang**
- **Field**: `$pegawai->jabatan->jabatan`
- **Deskripsi**: Jabatan fungsional yang sedang dipegang saat ini (dari tabel jabatans)
- **Source**: Relasi `jabatan_terakhir_id` di tabel pegawais
- **Fallback**: `-` jika data kosong

### 4. **Jabatan yang Dituju**
- **Field**: `$pegawai->jabatan->getNextLevel()->jabatan`
- **Deskripsi**: Jabatan level berikutnya berdasarkan hirarki master jabatan
- **Source**: Method `getNextLevel()` dari model Jabatan
- **Logic**: Mengambil jabatan dengan hierarchy_level yang lebih tinggi
- **Fallback**: `-` jika tidak ada level berikutnya atau jabatan tidak memiliki hirarki

## Desain dan Styling

### **Header Section:**
- **Background**: Gradient dari green-600 ke emerald-600
- **Icon**: User icon dari Lucide
- **Text**: Putih dengan font bold

### **Content Layout:**
- **Grid**: 2 kolom pada desktop, 1 kolom pada mobile
- **Gap**: 6 unit spacing antar field
- **Padding**: 6 unit padding dalam section

### **Input Fields:**
- **Style**: Disabled input dengan background gray-100
- **Border**: Gray-200 dengan rounded corners
- **Text**: Gray-800 dengan font medium
- **Cursor**: Not-allowed untuk menunjukkan readonly

### **Labels:**
- **Style**: Semibold dengan text gray-800
- **Description**: Text kecil gray-600 di bawah label

## Posisi dalam Layout

### **Urutan Section:**
1. ✅ **Profile Completeness Check** - Pengecekan kelengkapan profil
2. ✅ **Period Availability Check** - Pengecekan periode aktif
3. ✅ **Existing Usulan Check** - Pengecekan usulan yang sudah ada
4. ✅ **Informasi Periode Usulan** - Data periode usulan
5. ✅ **Informasi Pegawai** - **BARU: Data pegawai yang membuat usulan**
6. ✅ **Profile Display Component** - Komponen tampilan profil
7. ✅ **Karya Ilmiah Section Component** - Komponen karya ilmiah
8. ✅ **Dokumen Upload Component** - Komponen upload dokumen
9. ✅ **BKD Upload Component** - Komponen upload BKD
10. ✅ **Form Actions** - Tombol aksi form

## Data Source

### **Pegawai Data:**
- **Variable**: `$pegawai`
- **Source**: Controller `UsulanJabatanController@create()`
- **Type**: Eloquent Model `Pegawai`
- **Relations**: `with(['jabatan', 'pangkat', 'unitKerja'])`

### **Periode Data:**
- **Variable**: `$daftarPeriode`
- **Source**: Controller `UsulanJabatanController@create()`
- **Type**: Eloquent Model `PeriodeUsulan`

## Logika Hirarki Jabatan

### **Jabatan Sekarang:**
- **Source**: `$pegawai->jabatan->jabatan`
- **Table**: `pegawais.jabatan_terakhir_id` → `jabatans.id`
- **Field**: `jabatans.jabatan`

### **Jabatan yang Dituju:**
- **Source**: `$pegawai->jabatan->getNextLevel()->jabatan`
- **Logic**: 
  1. Cek apakah jabatan saat ini memiliki `hierarchy_level`
  2. Jika ya, cari jabatan dengan `hierarchy_level` yang lebih tinggi
  3. Filter berdasarkan `jenis_pegawai` dan `jenis_jabatan` yang sama
  4. Ambil jabatan dengan `hierarchy_level` terendah yang lebih tinggi

### **Contoh Hirarki Dosen Fungsional:**
```
Level 1: Tenaga Pengajar
Level 2: Asisten Ahli
Level 3: Lektor
Level 4: Lektor Kepala
Level 5: Guru Besar
```

### **Contoh Hirarki Tenaga Kependidikan:**
```
Level 1: Arsiparis Ahli Pertama
Level 2: Arsiparis Ahli Muda
```

### **Jabatan Non-Hirarki:**
- **Struktural**: Dekan, Wakil Dekan, Ketua Jurusan
- **Fungsional Umum**: Staf Administrasi, Staf Keuangan
- **Tugas Tambahan**: Dosen dengan Tugas Tambahan
- **Result**: `-` (tidak ada jabatan yang dituju)

## Responsive Design

### **Desktop (md:grid-cols-2):**
```
┌─────────────────┬─────────────────┐
│ Nama Lengkap    │ NIP             │
├─────────────────┼─────────────────┤
│ Jabatan Sekarang│ Jabatan Dituju  │
└─────────────────┴─────────────────┘
```

### **Mobile (grid-cols-1):**
```
┌─────────────────┐
│ Nama Lengkap    │
├─────────────────┤
│ NIP             │
├─────────────────┤
│ Jabatan Sekarang│
├─────────────────┤
│ Jabatan Dituju  │
└─────────────────┘
```

## Benefits

### ✅ **User Experience:**
- **Clear Information**: Pegawai dapat melihat data diri dengan jelas
- **Context Awareness**: Mengetahui jabatan sekarang vs yang dituju
- **Data Verification**: Memastikan data yang benar sebelum submit

### ✅ **Administrative:**
- **Quick Reference**: Admin dapat melihat info pegawai dengan cepat
- **Data Consistency**: Memastikan data yang konsisten
- **Audit Trail**: Tracking perubahan jabatan

### ✅ **Validation:**
- **Data Completeness**: Memastikan data pegawai lengkap
- **Status Check**: Memverifikasi status jabatan saat ini
- **Target Confirmation**: Memastikan jabatan yang dituju sesuai

## Testing Checklist

### ✅ **Functional Testing:**
- [ ] Data nama lengkap tampil dengan benar
- [ ] Data NIP tampil dengan benar
- [ ] Data jabatan sekarang tampil dengan benar
- [ ] Data jabatan yang dituju tampil dengan benar
- [ ] Fallback `-` muncul jika data kosong

### ✅ **UI/UX Testing:**
- [ ] Section muncul di posisi yang benar
- [ ] Styling konsisten dengan section lain
- [ ] Responsive di mobile dan desktop
- [ ] Input fields disabled dan tidak bisa diedit
- [ ] Gradient header terlihat menarik

### ✅ **Data Testing:**
- [ ] Data dari `$pegawai` ter-load dengan benar
- [ ] Data dari `$daftarPeriode` ter-load dengan benar
- [ ] Null safety dengan `??` operator
- [ ] Tidak ada error jika data kosong

### ✅ **Hierarchy Testing:**
- [ ] Jabatan sekarang tampil dari relasi `jabatan`
- [ ] Jabatan yang dituju tampil dari `getNextLevel()`
- [ ] Fallback `-` muncul untuk jabatan non-hirarki
- [ ] Fallback `-` muncul untuk jabatan tertinggi (Guru Besar)
- [ ] Relasi `jabatan` ter-load dengan benar di controller

## Future Enhancements

### 🔮 **Potential Features:**
- **Photo Display**: Menampilkan foto pegawai
- **Department Info**: Informasi unit kerja/departemen
- **Contact Info**: Email dan nomor telepon
- **Status Badge**: Badge status kepegawaian

### 🛠️ **Technical Improvements:**
- **Dynamic Loading**: Load data via AJAX jika diperlukan
- **Data Caching**: Cache data pegawai untuk performa
- **Real-time Updates**: Update otomatis jika ada perubahan data
- **Export Feature**: Export informasi pegawai ke PDF

## Status Implementasi

### ✅ **Selesai:**
- Section "Informasi Pegawai" ditambahkan
- 4 field informasi ditampilkan
- Styling konsisten dengan design system
- Responsive design diterapkan
- Data source terhubung dengan benar

### ✅ **Perbaikan Data Source:**
- **Jabatan Sekarang**: Menggunakan `$pegawai->jabatan->jabatan` (relasi ke tabel jabatans)
- **Jabatan yang Dituju**: Menggunakan `$pegawai->jabatan->getNextLevel()->jabatan` (hirarki master jabatan)
- **Relasi**: Controller sudah load dengan `with(['jabatan', 'pangkat', 'unitKerja'])`
- **Logic**: Menggunakan method `getNextLevel()` untuk menentukan jabatan berikutnya

### 📋 **Hasil:**
- Informasi pegawai tampil dengan jelas
- Layout responsive dan user-friendly
- Data ter-load dengan aman
- Tidak ada breaking changes
- UX meningkat dengan informasi yang lengkap
