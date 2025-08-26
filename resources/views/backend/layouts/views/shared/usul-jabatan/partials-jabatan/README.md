# Partial Views untuk Usulan Detail Jabatan

Dokumentasi ini menjelaskan struktur partial views yang telah dibuat untuk memecah file `usulan-detail.blade.php` yang sangat besar (4190 baris) menjadi komponen-komponen yang lebih kecil dan mudah dikelola.

## 📁 Struktur File

### File Utama
- **`usulan-detail-jabatan.blade.php`** - File utama yang mengintegrasikan semua partial
- **`usulan-detail.blade.php.backup`** - Backup file asli sebelum refactoring

### Partial Files (di folder `partials-jabatan/`)

#### 1. **Header & Status**
- **`usulan-detail-header.blade.php`** - Header section dengan judul dan tombol kembali
- **`usulan-detail-status-badge.blade.php`** - Badge status usulan dengan warna yang sesuai

#### 2. **Progress & History**
- **`usulan-detail-tim-penilai-progress.blade.php`** - Progress bar dan status tim penilai
- **`usulan-detail-info-history.blade.php`** - Info history perbaikan untuk Admin Fakultas

#### 3. **Perbaikan Sections**
- **`usulan-detail-perbaikan-admin-universitas.blade.php`** - Perbaikan dari Admin Universitas untuk Admin Fakultas
- **`usulan-detail-perbaikan-pegawai.blade.php`** - Perbaikan untuk Role Pegawai dari Admin Fakultas
- **`usulan-detail-perbaikan-admin-universitas-pegawai.blade.php`** - Perbaikan dari Admin Universitas untuk Role Pegawai
- **`usulan-detail-perbaikan-tim-sister.blade.php`** - Perbaikan dari Tim Sister untuk Role Pegawai
- **`usulan-detail-perbaikan-kepegawaian-universitas.blade.php`** - Perbaikan dari Kepegawaian Universitas untuk Admin Fakultas

#### 4. **Validasi Sections**
- **`usulan-detail-hasil-validasi-tim-penilai.blade.php`** - Hasil validasi semua tim penilai (Kepegawaian Universitas)
- **`usulan-detail-hasil-validasi-saya.blade.php`** - Hasil validasi saya (Penilai Universitas)

#### 5. **Informasi & Tabel**
- **`usulan-detail-informasi-usulan.blade.php`** - Informasi umum usulan (pegawai, periode, jenis, dll)
- **`usulan-detail-validation-table.blade.php`** - Tabel validasi utama dengan form input

#### 6. **Action & JavaScript**
- **`usulan-detail-action-bar.blade.php`** - Action buttons dan form submission
- **`usulan-detail-javascript.blade.php`** - JavaScript untuk auto-save, modal, dan interaksi

## 🔧 Cara Penggunaan

### Menggunakan File Utama
```blade
@include('backend.layouts.views.shared.usul-jabatan.usulan-detail-jabatan', [
    'usulan' => $usulan, 
    'role' => $role
])
```

### Menggunakan Partial Individual
```blade
@include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-header')
@include('backend.layouts.views.shared.usul-jabatan.partials-jabatan.usulan-detail-status-badge')
```

## 📋 Variabel yang Diperlukan

### Variabel Wajib
- `$usulan` - Object usulan dengan semua relasi dan data
- `$role` - Role pengguna saat ini (Admin Fakultas, Admin Universitas, dll)

### Variabel yang Dihasilkan Otomatis
- `$currentRole` - Role yang sudah divalidasi
- `$config` - Konfigurasi berdasarkan role
- `$fieldGroups` - Definisi field groups dan labels
- `$existingValidation` - Data validasi yang sudah ada
- `$canEdit` - Boolean apakah user bisa edit
- `$penilaiValidation` - Data validasi penilai (untuk Kepegawaian Universitas)
- `$allPenilaiInvalidFields` - Field yang tidak sesuai dari penilai

## 🎯 Fitur yang Dipertahankan

### ✅ Role-Based Access Control
- Setiap partial mengecek `$currentRole` untuk menampilkan konten yang sesuai
- Validasi dan form input yang berbeda untuk setiap role
- Visibility control untuk section tertentu

### ✅ Enhanced Status Display
- Status yang lebih deskriptif dan sesuai dengan alur kerja
- Mapping status berdasarkan role pengguna
- Warna status yang konsisten dan informatif

#### **Status Mapping Baru:**
- **Pegawai**: "Usulan Dikirim ke Admin Fakultas", "Usulan Perbaikan dari Admin Fakultas", dll.
- **Admin Fakultas**: "Permintaan Perbaikan dari Admin Fakultas", "Usulan Disetujui Admin Fakultas", dll.
- **Kepegawaian Universitas**: "Usulan Disetujui Kepegawaian Universitas", "Permintaan Perbaikan dari Penilai Universitas", dll.
- **Penilai Universitas**: "Usulan Perbaikan dari Penilai Universitas", "Usulan Direkomendasi Penilai Universitas", dll.
- **Tim Senat**: "Usulan Direkomendasikan oleh Tim Senat", "Usulan Sudah Dikirim ke Sister", dll.

### ✅ Dynamic Content
- Field groups yang dinamis berdasarkan data usulan
- BKD fields yang di-generate otomatis
- Conditional rendering berdasarkan status usulan

### ✅ Auto-Save Functionality
- JavaScript auto-save untuk validation status
- Debounced saving untuk performa optimal
- Real-time feedback untuk user

### ✅ Modal Interactions
- SweetAlert2 modals untuk berbagai aksi
- Form validation sebelum submission
- Confirmation dialogs

### ✅ File Upload Handling
- File input untuk dokumen pendukung
- Preview file yang sudah diupload
- Validation untuk file types

## 🔄 Migrasi dari File Lama

### Langkah 1: Backup
```bash
cp resources/views/backend/layouts/views/shared/usulan-detail.blade.php \
   resources/views/backend/layouts/views/shared/usulan-detail.blade.php.backup
```

### Langkah 2: Ganti Include
Ganti semua include dari file lama:
```blade
{{-- Dari --}}
@include('backend.layouts.views.shared.usulan-detail', ['usulan' => $usulan])

{{-- Ke --}}
@include('backend.layouts.views.shared.usulan-detail-new', ['usulan' => $usulan, 'role' => $role])
```

### Langkah 3: Test
- Test semua role dan status usulan
- Pastikan semua fungsi tetap berjalan
- Cek auto-save dan modal interactions

## 🚀 Keuntungan Refactoring

### 1. **Maintainability**
- File lebih kecil dan mudah dibaca
- Setiap partial fokus pada satu fungsi
- Mudah untuk debug dan update

### 2. **Reusability**
- Partial bisa digunakan di tempat lain
- Modular design untuk komponen UI
- Consistent styling dan behavior

### 3. **Performance**
- Load hanya partial yang diperlukan
- Caching yang lebih efisien
- Reduced memory usage

### 4. **Collaboration**
- Multiple developer bisa kerja parallel
- Conflict resolution yang lebih mudah
- Clear separation of concerns

## 🐛 Troubleshooting

### Error: Variable not defined
- Pastikan semua variabel wajib dikirim ke partial
- Cek `@php` block di file utama untuk variable initialization

### Error: Partial not found
- Pastikan path include benar
- Cek file permissions
- Verify file exists di folder partials

### Auto-save tidak berfungsi
- Pastikan CSRF token tersedia
- Cek JavaScript console untuk error
- Verify route untuk auto-save endpoint

### Modal tidak muncul
- Pastikan SweetAlert2 library loaded
- Cek JavaScript untuk modal functions
- Verify event handlers terpasang dengan benar

## 📝 Notes

- Semua partial menggunakan Tailwind CSS untuk styling
- Lucide icons digunakan untuk konsistensi visual
- Responsive design untuk mobile dan desktop
- Accessibility features (ARIA labels, keyboard navigation)

## 🔮 Future Improvements

1. **Component Library** - Buat reusable components untuk common UI patterns
2. **TypeScript** - Migrate JavaScript ke TypeScript untuk type safety
3. **Testing** - Unit tests untuk setiap partial
4. **Documentation** - Auto-generated documentation dari code comments
5. **Performance** - Lazy loading untuk partial yang besar
