# Jenis Jabatan Filtering Debugging Guide

## Masalah
Filtering jenis jabatan pada form edit pegawai tidak berfungsi dengan benar.

## Solusi yang Sudah Diimplementasikan

### 1. Enhanced JavaScript Functions
- **`filterJenisJabatan()`**: Fungsi utama untuk filtering
- **`testJenisJabatanFilter()`**: Fungsi test untuk debugging
- **`forceFilterJenisJabatan()`**: Fungsi force untuk memastikan filtering bekerja
- **`triggerFilters()`**: Fungsi untuk trigger semua filter

### 2. Improved Initialization
- Inisialisasi langsung saat DOM ready
- Inisialisasi tertunda (500ms) untuk memastikan semua elemen ter-load
- Force filtering jika ada nilai jenis pegawai saat halaman dimuat

## Cara Debugging

### 1. Buka Browser Console (F12)
```javascript
// Cek apakah elemen ada
console.log('jenis_pegawai:', !!document.getElementById('jenis_pegawai'));
console.log('jenis_jabatan:', !!document.getElementById('jenis_jabatan'));

// Cek nilai saat ini
console.log('Current jenis pegawai:', document.getElementById('jenis_pegawai')?.value);
console.log('Current jenis jabatan:', document.getElementById('jenis_jabatan')?.value);
```

### 2. Test Filtering Manual
```javascript
// Test fungsi filtering
window.testJenisJabatanFilter();

// Force filtering
window.forceFilterJenisJabatan();

// Trigger semua filter
window.triggerFilters();
```

### 3. Cek Data Attributes
```javascript
// Cek data attributes pada options jenis jabatan
document.querySelectorAll('#jenis_jabatan option').forEach((opt, index) => {
    console.log(`Option ${index}:`, {
        text: opt.textContent,
        value: opt.value,
        dataJenisPegawai: opt.getAttribute('data-jenis-pegawai'),
        display: opt.style.display,
        disabled: opt.disabled
    });
});
```

### 4. Test Perubahan Jenis Pegawai
```javascript
// Set jenis pegawai ke Dosen
document.getElementById('jenis_pegawai').value = 'Dosen';
window.filterJenisJabatan();

// Set jenis pegawai ke Tenaga Kependidikan
document.getElementById('jenis_pegawai').value = 'Tenaga Kependidikan';
window.filterJenisJabatan();
```

## Expected Behavior

### Ketika Jenis Pegawai = "Dosen"
**Jenis Jabatan yang Tampil:**
- Dosen Fungsional
- Dosen dengan Tugas Tambahan

**Jenis Jabatan yang Tersembunyi:**
- Tenaga Kependidikan Fungsional Umum
- Tenaga Kependidikan Fungsional Tertentu
- Tenaga Kependidikan Struktural
- Tenaga Kependidikan Tugas Tambahan

### Ketika Jenis Pegawai = "Tenaga Kependidikan"
**Jenis Jabatan yang Tampil:**
- Tenaga Kependidikan Fungsional Umum
- Tenaga Kependidikan Fungsional Tertentu
- Tenaga Kependidikan Struktural
- Tenaga Kependidikan Tugas Tambahan

**Jenis Jabatan yang Tersembunyi:**
- Dosen Fungsional
- Dosen dengan Tugas Tambahan

## Troubleshooting Steps

### 1. Cek Console Logs
- Buka browser console (F12)
- Lihat apakah ada error JavaScript
- Cek log inisialisasi filters

### 2. Cek Elemen DOM
- Pastikan elemen `jenis_pegawai` dan `jenis_jabatan` ada
- Pastikan data attributes sudah benar

### 3. Test Manual
- Gunakan fungsi test yang sudah disediakan
- Cek apakah filtering bekerja manual

### 4. Cek Event Listeners
- Pastikan event listener untuk change event sudah terpasang
- Cek apakah event listener terpanggil saat dropdown berubah

## Debug Commands

### Quick Debug
```javascript
// Cek status elemen
console.log('Elements found:', {
    jenisPegawai: !!document.getElementById('jenis_pegawai'),
    jenisJabatan: !!document.getElementById('jenis_jabatan')
});

// Cek nilai saat ini
console.log('Current values:', {
    jenisPegawai: document.getElementById('jenis_pegawai')?.value,
    jenisJabatan: document.getElementById('jenis_jabatan')?.value
});

// Test filtering
window.testJenisJabatanFilter();
```

### Force Fix
```javascript
// Force apply filtering
window.forceFilterJenisJabatan();

// Trigger all filters
window.triggerFilters();
```

### Manual Test
```javascript
// Test dengan Dosen
document.getElementById('jenis_pegawai').value = 'Dosen';
window.filterJenisJabatan();

// Test dengan Tenaga Kependidikan
document.getElementById('jenis_pegawai').value = 'Tenaga Kependidikan';
window.filterJenisJabatan();
```

## Common Issues & Solutions

### 1. Elemen Tidak Ditemukan
**Gejala:** Console error "Required elements not found"
**Solusi:** Pastikan JavaScript dimuat setelah DOM ready

### 2. Data Attributes Tidak Ada
**Gejala:** Options tidak ter-filter
**Solusi:** Cek apakah data-jenis-pegawai sudah ada di HTML

### 3. Event Listener Tidak Terpasang
**Gejala:** Filtering tidak bekerja saat dropdown berubah
**Solusi:** Pastikan event listener sudah terpasang dengan benar

### 4. CSS Display Tidak Berubah
**Gejala:** Options masih terlihat meskipun seharusnya tersembunyi
**Solusi:** Cek apakah ada CSS yang override display:none

## File Locations

### JavaScript
- `resources/js/admin-universitas/data-pegawai.js`
  - `filterJenisJabatan()` function
  - Event listeners
  - Test functions

### Blade Template
- `resources/views/backend/layouts/views/admin-univ-usulan/data-pegawai/partials/employment-data.blade.php`
  - Jenis jabatan dropdown HTML
  - Data attributes

### Controller
- `app/Http/Controllers/Backend/AdminUnivUsulan/DataPegawaiController.php`
  - Validation rules

## Testing Checklist

- [ ] Buka form edit pegawai
- [ ] Buka browser console (F12)
- [ ] Cek apakah elemen jenis_pegawai dan jenis_jabatan ada
- [ ] Cek nilai awal jenis_pegawai
- [ ] Test perubahan jenis_pegawai ke "Dosen"
- [ ] Verifikasi jenis_jabatan hanya menampilkan opsi Dosen
- [ ] Test perubahan jenis_pegawai ke "Tenaga Kependidikan"
- [ ] Verifikasi jenis_jabatan hanya menampilkan opsi Tenaga Kependidikan
- [ ] Cek console logs untuk error atau warning

## Next Steps

Jika filtering masih tidak bekerja setelah menggunakan debugging guide ini:

1. **Cek Network Tab**: Pastikan semua JavaScript files ter-load dengan benar
2. **Cek Sources Tab**: Pastikan JavaScript yang benar yang dijalankan
3. **Test di Browser Lain**: Pastikan bukan masalah browser-specific
4. **Clear Cache**: Clear browser cache dan reload halaman
5. **Check Laravel Mix**: Pastikan asset compilation sudah benar

---

*Gunakan debugging guide ini untuk mengidentifikasi dan memperbaiki masalah filtering jenis jabatan.*
