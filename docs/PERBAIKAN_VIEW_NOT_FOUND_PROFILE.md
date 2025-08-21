# Perbaikan Error View Not Found pada Profile

## Deskripsi Masalah
Error `View [backend.layouts.pegawai-unmul.profile.profile-header] not found` terjadi ketika mengklik "Lengkapi Profil" pada halaman create jabatan.

## Penyebab Masalah
Path include pada file `show.blade.php` tidak sesuai dengan struktur folder yang sebenarnya.

## File yang Diperbaiki

### 1. View: `show.blade.php`
- **Lokasi**: `resources/views/backend/layouts/views/pegawai-unmul/profile/show.blade.php`
- **Perubahan**: Memperbaiki semua path include dari `backend.layouts.pegawai-unmul.profile.*` menjadi `backend.layouts.views.pegawai-unmul.profile.*`

### 2. Include yang Diperbaiki
```php
// Sebelum (Salah)
@include('backend.layouts.pegawai-unmul.profile.profile-header', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.pegawai-unmul.profile.components.tabs.personal-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.pegawai-unmul.profile.components.tabs.kepegawaian-tab', [...])
@include('backend.layouts.pegawai-unmul.profile.components.tabs.pak-skp-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.pegawai-unmul.profile.components.tabs.dosen-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.pegawai-unmul.profile.components.tabs.security-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.pegawai-unmul.profile.components.tabs.dokumen-tab', [...])

// Sesudah (Benar)
@include('backend.layouts.views.pegawai-unmul.profile.profile-header', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.views.pegawai-unmul.profile.components.tabs.personal-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.views.pegawai-unmul.profile.components.tabs.kepegawaian-tab', [...])
@include('backend.layouts.views.pegawai-unmul.profile.components.tabs.pak-skp-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.views.pegawai-unmul.profile.components.tabs.dosen-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.views.pegawai-unmul.profile.components.tabs.security-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing])
@include('backend.layouts.views.pegawai-unmul.profile.components.tabs.dokumen-tab', [...])
```

## Struktur Folder yang Benar
```
resources/views/backend/layouts/views/pegawai-unmul/profile/
├── show.blade.php
├── profile-header.blade.php
└── components/
    └── tabs/
        ├── personal-tab.blade.php
        ├── kepegawaian-tab.blade.php
        ├── pak-skp-tab.blade.php
        ├── dosen-tab.blade.php
        ├── security-tab.blade.php
        └── dokumen-tab.blade.php
```

## Route yang Terkait
- `pegawai-unmul.profile.show`: Menampilkan profil
- `pegawai-unmul.profile.edit`: Redirect ke show dengan parameter edit=1
- `pegawai-unmul.profile.update`: Update profil

## Status Perbaikan
✅ **Selesai**: Semua path include telah diperbaiki dan error view not found telah teratasi

## Catatan
- File `show.blade.php` digunakan untuk kedua mode (show dan edit)
- Mode edit diaktifkan dengan parameter `edit=1` pada URL
- Semua komponen tab tersedia dan berfungsi dengan baik
