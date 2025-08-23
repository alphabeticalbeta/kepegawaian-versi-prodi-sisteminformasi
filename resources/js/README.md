# JavaScript Structure Documentation

## Overview
Script JavaScript telah dipisahkan ke dalam folder khusus berdasarkan pengelompokan role pengguna untuk memudahkan maintenance dan pengembangan.

## Folder Structure

```
resources/js/
├── app.js                          # Main application file
├── bootstrap.js                    # Bootstrap configuration
├── shared/                         # Shared utilities
│   ├── utils.js                    # Common utility functions
│   └── README.md                   # This file
├── admin-fakultas/                 # Admin Fakultas scripts
│   ├── index.js                    # Main entry point
│   └── admin-fakultas.js           # Admin Fakultas functionality
├── admin-universitas/              # Admin Universitas scripts
│   ├── index.js                    # Main entry point
│   ├── admin-univ-usulan.js        # Admin Univ Usulan functionality
│   └── periode-usulan.js           # Periode Usulan functionality
├── pegawai/                        # Pegawai scripts
│   ├── index.js                    # Main entry point
│   ├── pegawai-profil.js           # Pegawai Profile functionality
│   └── pegawai-usulan.js           # Pegawai Usulan functionality
└── penilai/                        # Penilai scripts
    ├── index.js                    # Main entry point
    └── penilai-universitas.js      # Penilai Universitas functionality
```

## Module Descriptions

### Shared Utilities (`shared/utils.js`)
Berisi fungsi-fungsi umum yang dapat digunakan oleh semua modul:
- CSRF Token setup
- Format currency, date, datetime
- Loading spinner
- Alert/notification system
- Confirm dialog
- Debounce dan throttle functions

### Admin Fakultas (`admin-fakultas/`)
Fungsionalitas khusus untuk Admin Fakultas:
- Sidebar toggle
- Dropdown navigation
- DataTables initialization
- Form validation
- File upload handling
- Usulan management (approval/rejection)

### Admin Universitas (`admin-universitas/`)
Fungsionalitas khusus untuk Admin Universitas:
- Admin Univ Usulan management
- Periode Usulan management
- Date validation
- Jenis usulan info display

### Pegawai (`pegawai/`)
Fungsionalitas khusus untuk Pegawai:
- Profile management
- File preview dengan validation
- Image preview
- Tab functionality
- Usulan form handling
- Syarat Guru Besar handling

### Penilai (`penilai/`)
Fungsionalitas khusus untuk Penilai:
- Penilaian usulan
- Score calculation
- Document download
- Usulan detail viewing

## Usage

### Including Scripts in Blade Templates

Untuk menggunakan script tertentu, import file index yang sesuai:

```html
<!-- For Admin Fakultas -->
<script type="module" src="{{ asset('js/admin-fakultas/index.js') }}"></script>

<!-- For Admin Universitas -->
<script type="module" src="{{ asset('js/admin-universitas/index.js') }}"></script>

<!-- For Pegawai -->
<script type="module" src="{{ asset('js/pegawai/index.js') }}"></script>

<!-- For Penilai -->
<script type="module" src="{{ asset('js/penilai/index.js') }}"></script>
```

### Using Shared Utilities

Shared utilities tersedia secara global setelah diimport:

```javascript
// Format currency
const formatted = formatCurrency(1000000); // "Rp 1.000.000,00"

// Show alerts
showSuccess('Data berhasil disimpan!');
showError('Terjadi kesalahan!');

// Show loading
showLoading();
// ... do something
hideLoading();

// Confirm dialog
confirmDialog('Apakah Anda yakin?', () => {
    // callback function
});
```

## Development Guidelines

1. **Modular Structure**: Setiap role memiliki folder terpisah dengan file index.js sebagai entry point
2. **Shared Utilities**: Gunakan shared/utils.js untuk fungsi umum
3. **Class-based**: Setiap modul menggunakan class untuk encapsulation
4. **Event Listeners**: Gunakan DOMContentLoaded untuk initialization
5. **Error Handling**: Implementasikan try-catch untuk error handling
6. **Console Logging**: Gunakan console.log untuk debugging

## Migration from Old Scripts

Script-script lama yang ada di `resources/views/backend/components/scripts/` telah dipindahkan dan diorganisir ulang:

- `script-admin-univ-usulan.blade.php` → `admin-universitas/admin-univ-usulan.js`
- `script-pegawai-profil.blade.php` → `pegawai/pegawai-profil.js`
- `script-pegawai-usulan.blade.php` → `pegawai/pegawai-usulan.js`
- `script-periode.blade.php` → `admin-universitas/periode-usulan.js`

## Building and Compilation

Untuk menggunakan struktur modular ini, pastikan:

1. Webpack/Vite dikonfigurasi untuk handle ES6 modules
2. Asset compilation dijalankan: `npm run dev` atau `npm run build`
3. File JavaScript dikompilasi ke folder `public/js/`

## Future Enhancements

1. **TypeScript Support**: Pertimbangkan migrasi ke TypeScript untuk type safety
2. **Testing**: Implementasikan unit tests untuk setiap modul
3. **Documentation**: Tambahkan JSDoc comments untuk dokumentasi API
4. **Performance**: Implementasikan lazy loading untuk modul yang tidak sering digunakan
