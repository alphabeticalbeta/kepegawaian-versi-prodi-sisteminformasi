# Implementasi Form Usulan Jabatan Dosen

## Deskripsi
Implementasi form usulan jabatan untuk dosen dengan kondisi validasi profil lengkap dan notifikasi berbagai kondisi.

## Kondisi yang Diimplementasikan

### 1. Validasi Kelengkapan Profil
- **Kondisi**: Form hanya bisa diakses jika data profil di `my-profil.blade.php` terisi semua
- **Field yang Diperiksa**:
  - `nama_lengkap`
  - `nip`
  - `email`
  - `tempat_lahir`
  - `tanggal_lahir`
  - `jenis_kelamin`
  - `nomor_handphone`
  - `gelar_depan`
  - `gelar_belakang`
  - `ijazah_terakhir`
  - `transkrip_nilai_terakhir`
  - `sk_pangkat_terakhir`
  - `sk_jabatan_terakhir`
  - `skp_tahun_pertama`
  - `skp_tahun_kedua`

### 2. Notifikasi Kondisi
- **Profil Belum Lengkap**: Menampilkan daftar field yang perlu dilengkapi dengan tombol "Lengkapi Profil"
- **Profil Lengkap**: Notifikasi sukses bahwa profil sudah lengkap
- **Tidak Ada Periode Aktif**: Peringatan jika tidak ada periode usulan yang sedang berlangsung
- **Usulan Sudah Ada**: Informasi jika sudah ada usulan untuk periode ini dengan link ke detail usulan

### 3. Form Berdasarkan Komponen
Form menggunakan komponen-komponen yang sudah ada:
- `profile-display.blade.php`: Menampilkan data profil pegawai
- `dokumen-upload.blade.php`: Upload dokumen pendukung
- `bkd-upload.blade.php`: Upload laporan BKD
- `karya-ilmiah-section.blade.php`: Form karya ilmiah

## File yang Dimodifikasi

### 1. View: `create-jabatan.blade.php`
- **Lokasi**: `resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/create-jabatan.blade.php`
- **Perubahan**:
  - Menambahkan validasi kelengkapan profil
  - Menambahkan notifikasi kondisi
  - Menggunakan komponen yang sudah ada
  - Menambahkan form validation JavaScript

### 2. Controller: `UsulanJabatanController.php`
- **Lokasi**: `app/Http/Controllers/Backend/PegawaiUnmul/UsulanJabatanController.php`
- **Perubahan**:
  - Memperbarui method `create()` untuk menyediakan data yang diperlukan
  - Memperbaiki method `getDocumentKeys()` untuk menerima parameter periode
  - Menambahkan variabel `$catatanPerbaikan`, `$isReadOnly`, `$isEditMode`, `$existingUsulan`

## Struktur Data yang Dikirim ke View

```php
[
    'pegawai' => $pegawai,
    'daftarPeriode' => $daftarPeriode,
    'jabatanTujuan' => $jabatanTujuan,
    'usulan' => $usulan,
    'jenjangType' => $jenjangType,
    'formConfig' => $formConfig,
    'jenisUsulanPeriode' => $jenisUsulanPeriode,
    'bkdSemesters' => $bkdSemesters,
    'documentFields' => $documentFields,
    'catatanPerbaikan' => $catatanPerbaikan,
    'isReadOnly' => $isReadOnly,
    'isEditMode' => $isEditMode,
    'existingUsulan' => $existingUsulan,
]
```

## Fitur Form

### 1. Header Section
- Judul dinamis (Buat/Edit Usulan Jabatan)
- Tombol kembali ke halaman index

### 2. Validasi Kondisi
- Pengecekan kelengkapan profil
- Pengecekan periode aktif
- Pengecekan usulan yang sudah ada

### 3. Informasi Periode Usulan
- Nama periode
- Masa berlaku periode

### 4. Komponen Form
- **Profile Display**: Menampilkan data profil dengan validasi
- **Dokumen Upload**: Upload dokumen pendukung
- **BKD Upload**: Upload laporan BKD 4 semester
- **Karya Ilmiah**: Form karya ilmiah dan artikel

### 5. Form Actions
- Tombol Batal
- Tombol Submit (Kirim Usulan/Update Usulan)

## JavaScript Validation
- Validasi field required
- Highlight field yang kosong
- Alert jika ada field yang belum diisi

## Status Implementasi
✅ **Selesai**: Form usulan jabatan dosen dengan validasi profil lengkap dan notifikasi kondisi

## Catatan
- Form menggunakan komponen yang sudah ada untuk konsistensi
- Validasi dilakukan di frontend dan backend
- Notifikasi kondisi memberikan feedback yang jelas kepada pengguna
- Form dapat digunakan untuk create dan edit usulan
