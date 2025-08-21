# Penerapan ProfileDisplayHelper pada Semua Halaman My Profile

## Deskripsi Perubahan
Menerapkan aturan konsisten untuk menampilkan data pada semua halaman My Profile agar sesuai dengan kondisi DatapegawaiController. Field yang kosong akan menampilkan placeholder "-", namun pada halaman show, jika nilai field hanya "-", maka tidak akan ditampilkan.

## Helper Class yang Dibuat

### ProfileDisplayHelper
**Lokasi**: `app/Helpers/ProfileDisplayHelper.php`

Helper class ini menyediakan method untuk menampilkan data profile dengan konsistensi:

#### Method Utama:
1. **`displayValue($value, $placeholder = '-')`**
   - Menampilkan nilai field dengan placeholder "-" jika kosong
   - Digunakan untuk halaman edit/view

2. **`displayValueForShow($value, $placeholder = '-')`**
   - Menampilkan nilai field untuk halaman show
   - Jika nilai hanya "-", maka tidak ditampilkan (return null)

#### Method Khusus:
- `displayNamaLengkap($pegawai)` - Menampilkan nama lengkap dengan gelar
- `displayTempatTanggalLahir($pegawai)` - Menampilkan tempat dan tanggal lahir
- `displayUnitKerja($pegawai)` - Menampilkan unit kerja lengkap
- `displayPangkat($pegawai)` - Menampilkan pangkat
- `displayJabatan($pegawai)` - Menampilkan jabatan
- `displayEmail($pegawai)` - Menampilkan email
- `displayNomorHandphone($pegawai)` - Menampilkan nomor handphone
- `displayNuptk($pegawai)` - Menampilkan NUPTK
- `displayPendidikanTerakhir($pegawai)` - Menampilkan pendidikan terakhir
- `displayNamaUniversitasSekolah($pegawai)` - Menampilkan nama universitas/sekolah
- `displayNamaProdiJurusan($pegawai)` - Menampilkan nama prodi/jurusan
- `displayMataKuliahDiampu($pegawai)` - Menampilkan mata kuliah diampu
- `displayRantingIlmuKepakaran($pegawai)` - Menampilkan ranting ilmu kepakaran
- `displayUrlProfilSinta($pegawai)` - Menampilkan URL profil SINTA
- `displayPredikatKinerjaTahunPertama($pegawai)` - Menampilkan predikat kinerja tahun pertama
- `displayPredikatKinerjaTahunKedua($pegawai)` - Menampilkan predikat kinerja tahun kedua
- `displayNilaiKonversi($pegawai)` - Menampilkan nilai konversi

## File yang Dimodifikasi

### 1. Helper Class
- **File**: `app/Helpers/ProfileDisplayHelper.php`
- **Status**: ✅ Dibuat

### 2. Composer Configuration
- **File**: `composer.json`
- **Perubahan**: Menambahkan helper ke autoload files
- **Status**: ✅ Diupdate

### 3. Tab Components

#### Personal Tab
- **File**: `resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/personal-tab.blade.php`
- **Perubahan**: 
  - Nama lengkap menggunakan `displayNamaLengkap()`
  - Email menggunakan `displayEmail()`
  - Gelar depan/belakang menggunakan `displayValue()`
  - Tempat lahir menggunakan `displayValue()`
  - Jenis kelamin menggunakan `displayValue()`
  - Nomor handphone menggunakan `displayNomorHandphone()`
  - Pendidikan terakhir menggunakan `displayPendidikanTerakhir()`
  - Nama universitas/sekolah menggunakan `displayNamaUniversitasSekolah()`
  - Nama prodi/jurusan menggunakan `displayNamaProdiJurusan()`
- **Status**: ✅ Diupdate

#### Kepegawaian Tab
- **File**: `resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/kepegawaian-tab.blade.php`
- **Perubahan**:
  - Nomor kartu pegawai menggunakan `displayValue()`
- **Status**: ✅ Diupdate

#### Dosen Tab
- **File**: `resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/dosen-tab.blade.php`
- **Perubahan**:
  - NUPTK menggunakan `displayValue()` dengan placeholder "Belum diisi"
  - URL profil SINTA menggunakan `displayValue()` dengan placeholder "Belum diisi"
  - Ranting ilmu kepakaran menggunakan `displayValue()` dengan placeholder "Belum diisi"
  - Mata kuliah diampu menggunakan `displayValue()` dengan placeholder "Belum diisi"
- **Status**: ✅ Diupdate

#### PAK-SKP Tab
- **File**: `resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/pak-skp-tab.blade.php`
- **Perubahan**:
  - Nilai konversi menggunakan `displayValue()` dengan placeholder "Belum diisi"
- **Status**: ✅ Diupdate

## Aturan Tampilan yang Diterapkan

### 1. Halaman Edit/View (Normal)
- Field kosong menampilkan "-"
- Field dengan nilai menampilkan nilai asli
- Konsisten dengan kondisi DatapegawaiController

### 2. Halaman Show (Detail)
- Field kosong tidak ditampilkan sama sekali
- Field dengan nilai menampilkan nilai asli
- Menghindari tampilan "-" yang tidak informatif

### 3. Placeholder Khusus
- Beberapa field menggunakan placeholder "Belum diisi" untuk konteks yang lebih informatif
- Contoh: NUPTK, URL SINTA, bidang kepakaran, dll.

## Contoh Penggunaan

### Di View (Normal):
```php
{{ \App\Helpers\ProfileDisplayHelper::displayNamaLengkap($pegawai) }}
// Output: "Dr. John Doe S.Kom." atau "-" jika kosong
```

### Di Show Page:
```php
@if(\App\Helpers\ProfileDisplayHelper::displayNamaLengkapForShow($pegawai))
    <p>{{ \App\Helpers\ProfileDisplayHelper::displayNamaLengkapForShow($pegawai) }}</p>
@endif
// Output: Hanya ditampilkan jika ada data, tidak menampilkan "-"
```

## Script Otomatis

### apply_profile_display_helper.php
**Lokasi**: `debug-scripts/apply_profile_display_helper.php`

Script ini otomatis menerapkan helper ke semua tab profile dengan mapping yang telah ditentukan.

**Cara menjalankan**:
```bash
docker-compose exec app php debug-scripts/apply_profile_display_helper.php
```

## Status Implementasi

### ✅ Selesai:
- Helper class dibuat dan terdaftar di composer
- Personal tab diupdate
- Kepegawaian tab diupdate  
- Dosen tab diupdate
- PAK-SKP tab diupdate
- Script otomatis dibuat dan dijalankan

### 📋 Fitur yang Ditambahkan:
- Konsistensi tampilan data di semua halaman profile
- Penanganan field kosong yang lebih baik
- Pemisahan logika untuk halaman normal vs show
- Helper method yang reusable dan maintainable
- Script otomatis untuk penerapan massal

## Catatan Penting

1. **Autoload**: Helper class telah didaftarkan di `composer.json` dan `composer dump-autoload` telah dijalankan
2. **Konsistensi**: Semua tab profile sekarang menggunakan helper yang sama
3. **Maintainability**: Perubahan logika tampilan cukup dilakukan di helper class
4. **Flexibility**: Helper mendukung placeholder kustom untuk konteks yang berbeda
5. **Performance**: Helper menggunakan method static untuk efisiensi

## Testing

Untuk memastikan implementasi berjalan dengan baik:

1. **Test halaman profile normal**: Pastikan field kosong menampilkan "-"
2. **Test halaman show**: Pastikan field kosong tidak ditampilkan
3. **Test field dengan data**: Pastikan data ditampilkan dengan benar
4. **Test placeholder kustom**: Pastikan "Belum diisi" muncul di field yang sesuai
