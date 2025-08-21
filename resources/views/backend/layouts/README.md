# Struktur Layout Backend - Kepegawaian UNMUL

## �� Struktur Direktori Baru

```
resources/views/backend/layouts/
├── README.md                           # Dokumentasi ini
├── base.blade.php                      # Layout base untuk semua role
├── roles/                              # Layout khusus untuk setiap role
│   ├── admin-fakultas/
│   │   └── app.blade.php               # Layout admin fakultas
│   ├── admin-universitas/
│   │   └── app.blade.php               # Layout admin universitas
│   ├── admin-univ-usulan/
│   │   └── app.blade.php               # Layout admin univ usulan
│   ├── pegawai-unmul/
│   │   └── app.blade.php               # Layout pegawai
│   ├── penilai-universitas/
│   │   └── app.blade.php               # Layout penilai
│   └── periode-usulan/
│       └── app.blade.php               # Layout periode usulan
├── views/                              # Views untuk setiap role
│   ├── admin-fakultas/
│   │   ├── dashboard.blade.php         # Dashboard admin fakultas
│   │   └── usulan/                     # Halaman usulan
│   │       ├── index.blade.php
│   │       ├── index-dynamic.blade.php
│   │       └── pengusul.blade.php
│   ├── admin-universitas/
│   │   └── dashboard.blade.php         # Dashboard admin universitas
│   ├── admin-univ-usulan/
│   │   ├── dashboard.blade.php         # Dashboard admin univ usulan
│   │   ├── jabatan/                    # Halaman jabatan
│   │   │   ├── form-jabatan.blade.php
│   │   │   └── master-data-jabatan.blade.php
│   │   ├── pangkat/                    # Halaman pangkat
│   │   │   ├── form-pangkat.blade.php
│   │   │   └── master-data-pangkat.blade.php
│   │   ├── pusat-usulan/               # Halaman pusat usulan
│   │   │   ├── detail-usulan.blade.php
│   │   │   ├── index.blade.php
│   │   │   └── show-pendaftar.blade.php
│   │   └── data-pegawai/               # Halaman data pegawai
│   │       ├── form-datapegawai.blade.php
│   │       ├── master-datapegawai.blade.php
│   │       └── show-datapegawai.blade.php
│   ├── pegawai-unmul/
│   │   ├── dashboard.blade.php         # Dashboard pegawai
│   │   ├── my-profil.blade.php         # Halaman profil
│   │   ├── profile/                    # Komponen profil
│   │   │   ├── profile-header.blade.php
│   │   │   ├── show.blade.php
│   │   │   └── components/
│   │   │       └── tabs/
│   │   │           ├── dokumen-tab.blade.php
│   │   │           ├── dosen-tab.blade.php
│   │   │           ├── kepegawaian-tab.blade.php
│   │   │           ├── pak-skp-tab.blade.php
│   │   │           ├── personal-tab.blade.php
│   │   │           └── security-tab.blade.php
│   │   └── usul-jabatan/               # Halaman usul jabatan
│   │       ├── create-jabatan.blade.php
│   │       └── components/
│   │           ├── bkd-upload.blade.php
│   │           ├── dokumen-upload.blade.php
│   │           ├── karya-ilmiah-section.blade.php
│   │           └── profile-display.blade.php
│   ├── penilai-universitas/
│   │   ├── dashboard.blade.php         # Dashboard penilai
│   │   └── pusat-usulan/               # Halaman pusat usulan
│   │       ├── detail-usulan.blade.php
│   │       ├── index.blade.php
│   │       ├── show-pendaftar.blade.php
│   │       └── partials/
│   │           ├── action-buttons.blade.php
│   │           ├── alert-messages.blade.php
│   │           ├── forward-form.blade.php
│   │           ├── header-info.blade.php
│   │           ├── return-form.blade.php
│   │           ├── riwayat-log.blade.php
│   │           ├── validation-row.blade.php
│   │           ├── validation-scripts.blade.php
│   │           └── validation-section.blade.php
│   └── periode-usulan/
│       ├── index.blade.php             # Halaman index periode
│       └── form.blade.php              # Halaman form periode
└── components/                         # Komponen bersama
    ├── base.blade.php                  # Layout base (dari shared)
    └── usulan-detail/                  # Komponen detail usulan
        └── usulan-detail.blade.php     # Template detail usulan multi-role
```

## 🎯 Sistem Modular JavaScript

Setiap layout memuat JavaScript module yang sesuai dengan role:

### **Admin Fakultas**
- **Layout**: `roles/admin-fakultas/app.blade.php`
- **JS Module**: `resources/js/admin-fakultas/index.js`
- **Fitur**: Validasi usulan, approval/rejection, forward ke universitas

### **Admin Universitas**
- **Layout**: `roles/admin-universitas/app.blade.php`
- **JS Module**: `resources/js/admin-universitas/index.js`
- **Fitur**: Dashboard, monitoring

### **Admin Univ Usulan**
- **Layout**: `roles/admin-univ-usulan/app.blade.php`
- **JS Module**: `resources/js/admin-universitas/index.js`
- **Fitur**: Pengelolaan usulan, return/revision, send to assessor/senate

### **Pegawai**
- **Layout**: `roles/pegawai-unmul/app.blade.php`
- **JS Module**: `resources/js/pegawai/index.js`
- **Fitur**: Profil, usul jabatan, upload dokumen

### **Penilai**
- **Layout**: `roles/penilai-universitas/app.blade.php`
- **JS Module**: `resources/js/penilai/index.js`
- **Fitur**: Penilaian usulan, scoring, download dokumen

### **Periode Usulan**
- **Layout**: `roles/periode-usulan/app.blade.php`
- **JS Module**: `resources/js/admin-universitas/index.js`
- **Fitur**: Pengelolaan periode usulan

## 🔧 Layout Base

### **Fitur Layout Base (`base.blade.php`)**
- **Meta Tags**: CSRF token, description, author
- **Favicon**: Icon aplikasi
- **Vite Integration**: CSS dan JS modules
- **External Libraries**: Lucide, SweetAlert2, Alpine.js
- **Loading Overlay**: Konsisten di semua halaman
- **Accessibility**: Skip to main content link
- **Global Functions**: showLoading, hideLoading, handleError, handleSuccess, confirmAction
- **Flexible Structure**: Breadcrumb, page header, page footer

### **Penggunaan Layout Base**
```php
@extends('backend.layouts.base', [
    'jsModule' => 'admin-fakultas/index.js',
    'sidebarComponent' => 'backend.components.sidebar-admin-fakultas',
    'role' => 'admin-fakultas'
])

@section('title', 'Dashboard Admin Fakultas')
@section('description', 'Dashboard untuk Admin Fakultas')

@push('styles')
<style>
    /* Role-specific styles */
</style>
@endpush

@section('content')
    <!-- Content here -->
@endsection
```

## 📋 Penggunaan Layout

### **1. Layout Khusus Role**
```php
@extends('backend.layouts.roles.admin-fakultas.app')
@extends('backend.layouts.roles.pegawai-unmul.app')
@extends('backend.layouts.roles.penilai-universitas.app')
```

### **2. Layout Base (Shared)**
```php
@extends('backend.layouts.base', [
    'jsModule' => 'admin-fakultas/index.js',
    'sidebarComponent' => 'backend.components.sidebar-admin-fakultas',
    'role' => 'admin-fakultas'
])
```

### **3. Views**
```php
@extends('backend.layouts.roles.admin-fakultas.app')

@section('content')
    @include('backend.layouts.views.admin-fakultas.dashboard')
@endsection
```

### **4. Komponen Bersama**
```php
@include('backend.layouts.components.usulan-detail.usulan-detail', [
    'currentRole' => 'admin_fakultas',
    'usulan' => $usulan
])
```

## 🎨 Styling Konsisten

### **Color Scheme**
- **Primary**: Indigo (`bg-indigo-600`, `text-indigo-600`)
- **Success**: Green (`bg-green-600`, `text-green-600`)
- **Warning**: Yellow (`bg-yellow-600`, `text-yellow-600`)
- **Error**: Red (`bg-red-600`, `text-red-600`)
- **Background**: Slate (`bg-slate-100`)

### **Typography**
- **Font**: `font-sans`
- **Headings**: `text-lg font-semibold`
- **Body**: `text-sm`
- **Labels**: `text-xs font-medium`

### **Spacing**
- **Container**: `p-4 sm:p-6 lg:p-8`
- **Cards**: `p-6`
- **Buttons**: `px-4 py-2`
- **Forms**: `space-y-6`

### **Animations**
- **Hover Effects**: `transition-all duration-300`
- **Card Hover**: `transform: translateY(-2px)`
- **Loading**: `animate-spin`

## 🚀 Best Practices

### **1. Modular Structure**
- **Layouts**: Terpisah berdasarkan role
- **Views**: Terpisah berdasarkan fitur
- **Components**: Dapat digunakan ulang
- **JavaScript**: Modular per role

### **2. Consistent Naming**
- **Files**: kebab-case (`admin-fakultas`)
- **Classes**: kebab-case (`admin-fakultas-dashboard`)
- **Variables**: camelCase (`jsModule`)
- **Functions**: camelCase (`showLoading`)

### **3. Accessibility**
- **Semantic HTML**: Proper heading structure
- **ARIA Labels**: Screen reader friendly
- **Keyboard Navigation**: Tab order
- **Skip Links**: Skip to main content

### **4. Performance**
- **Lazy Loading**: Components loaded on demand
- **Minification**: Vite handles optimization
- **Caching**: Browser caching strategies
- **CDN**: External libraries from CDN

## 🔄 Maintenance

### **Menambah Role Baru**
1. Buat folder di `roles/{role-name}/`
2. Buat `app.blade.php` dengan extends base layout
3. Buat folder di `views/{role-name}/`
4. Buat JavaScript module di `resources/js/{role-name}/index.js`
5. Update dokumentasi ini

### **Menambah View Baru**
1. Buat file di `views/{role-name}/{feature}/`
2. Extends layout role yang sesuai
3. Implement content dalam `@section('content')`
4. Tambahkan styles jika diperlukan

### **Menambah Komponen Bersama**
1. Buat file di `components/`
2. Gunakan variabel untuk kustomisasi
3. Dokumentasikan penggunaan
4. Test di semua role

### **Update Styling**
1. Update di `base.blade.php` untuk global styles
2. Update di role-specific layout untuk role-specific styles
3. Gunakan CSS variables untuk theming
4. Test di semua breakpoints

## 📊 Struktur File Mapping

| Role | Layout File | Views Directory | JS Module |
|------|-------------|-----------------|-----------|
| **Admin Fakultas** | `roles/admin-fakultas/app.blade.php` | `views/admin-fakultas/` | `admin-fakultas/index.js` |
| **Admin Universitas** | `roles/admin-universitas/app.blade.php` | `views/admin-universitas/` | `admin-universitas/index.js` |
| **Admin Univ Usulan** | `roles/admin-univ-usulan/app.blade.php` | `views/admin-univ-usulan/` | `admin-universitas/index.js` |
| **Pegawai** | `roles/pegawai-unmul/app.blade.php` | `views/pegawai-unmul/` | `pegawai/index.js` |
| **Penilai** | `roles/penilai-universitas/app.blade.php` | `views/penilai-universitas/` | `penilai/index.js` |
| **Periode Usulan** | `roles/periode-usulan/app.blade.php` | `views/periode-usulan/` | `admin-universitas/index.js` |

## ✅ Keuntungan Struktur Baru

### **1. Organisasi yang Lebih Baik**
- ✅ Layout dan views terpisah dengan jelas
- ✅ Komponen dapat digunakan ulang
- ✅ Mudah untuk maintenance

### **2. Scalability**
- ✅ Mudah menambah role baru
- ✅ Mudah menambah fitur baru
- ✅ Struktur yang fleksibel

### **3. Developer Experience**
- ✅ Dokumentasi yang lengkap
- ✅ Naming convention yang konsisten
- ✅ Struktur yang mudah dipahami

### **4. Performance**
- ✅ JavaScript modular
- ✅ CSS yang teroptimasi
- ✅ Loading yang efisien

---

**Terakhir diperbarui**: {{ date('Y-m-d H:i:s') }}
**Versi**: 3.0.0
**Status**: ✅ Production Ready - Restructured
