# Admin Fakultas Revision System

## Overview

Sistem revisi Admin Fakultas memungkinkan Admin Fakultas untuk mengelola usulan dengan fleksibilitas tinggi saat mengirim ulang ke Admin Univ Usulan. Sistem ini menyimpan riwayat revisi lengkap dan memberikan tiga opsi tindakan per item.

## Fitur Utama

### 1. **Form Pengisian Pertama Kali**
- Menampilkan seluruh field dan dokumen pendukung
- Form sederhana dengan input langsung
- Validasi real-time
- Auto-save functionality

### 2. **Form Revisi (Setelah Dikembalikan)**
- Menampilkan riwayat revisi sebelumnya
- Tiga opsi tindakan per item:
  - **Gunakan Data Sebelumnya**: Tetap menggunakan data yang sudah ada
  - **Ganti Data**: Mengganti seluruh data dengan yang baru
  - **Perbaiki**: Mengedit sebagian data yang ada (hanya status validasi)

### 3. **Riwayat Revisi**
- Timestamp lengkap (tanggal dan waktu)
- Nama pengubah
- Keterangan revisi (opsional)
- Status sebelum dan sesudah
- Maksimal 10 riwayat terakhir

## Struktur Data

### Validation Data Structure
```php
'admin_fakultas' => [
    'validation' => [
        'data_pribadi' => [
            'nama_lengkap' => [
                'value' => 'John Doe',
                'status' => 'sesuai',
                'keterangan' => '',
                'updated_at' => '2025-08-22T06:45:00Z',
                'action' => 'replaced' // atau 'edited', 'kept'
            ]
        ]
    ],
    'dokumen_pendukung' => [
        'nomor_surat_usulan' => '001/FK-UNMUL/2025',
        'file_surat_usulan_path' => 'dokumen-fakultas/surat-usulan/file.pdf',
        'nomor_berita_senat' => '001/Berita-Senat/2025',
        'file_berita_senat_path' => 'dokumen-fakultas/berita-senat/file.pdf'
    ],
    'revision_history' => [
        [
            'timestamp' => '2025-08-22T06:45:00Z',
            'user_id' => 1,
            'user_name' => 'Admin Fakultas',
            'keterangan' => 'Perbaikan data pribadi',
            'status_before' => 'Perbaikan Usulan',
            'status_after' => 'Diusulkan ke Universitas'
        ]
    ]
]
```

## Implementasi

### 1. **View Template**
File: `resources/views/backend/layouts/views/admin-fakultas/usulan/detail.blade.php`

**Fitur:**
- Conditional rendering berdasarkan status usulan
- Form dinamis dengan radio button untuk opsi tindakan
- JavaScript untuk interaksi form
- Validasi client-side

### 2. **Controller Methods**
File: `app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php`

**Methods:**
- `processRevisionData()`: Memproses data revisi dengan tiga opsi tindakan
- `processDokumenPendukungRevision()`: Memproses dokumen pendukung dengan revisi
- `addRevisionHistory()`: Menambahkan entri riwayat revisi
- `autosaveValidation()`: Auto-save dengan dukungan revisi

### 3. **JavaScript Functionality**
- Toggle visibility input berdasarkan opsi yang dipilih
- Validasi form sebelum submit
- Auto-save dengan debouncing

## Workflow

### 1. **Pengusulan Pertama Kali**
```
Pegawai → Admin Fakultas (status: Diajukan)
├── Admin Fakultas mengisi form lengkap
├── Upload dokumen pendukung
└── Kirim ke Universitas (status: Diusulkan ke Universitas)
```

### 2. **Proses Revisi**
```
Admin Univ Usulan → Admin Fakultas (status: Perbaikan Usulan)
├── Admin Fakultas melihat riwayat revisi
├── Pilih opsi tindakan per item:
│   ├── Gunakan Data Sebelumnya
│   ├── Ganti Data
│   └── Perbaiki
├── Isi keterangan revisi (opsional)
└── Kirim perbaikan ke Universitas
```

## Validasi

### 1. **Client-Side Validation**
- Radio button harus dipilih untuk setiap field
- Input required jika memilih "Ganti Data"
- File upload validation

### 2. **Server-Side Validation**
- Validasi format file (PDF)
- Validasi ukuran file (max 2MB)
- Validasi field required
- Authorization check

## Auto-Save

### 1. **Fitur**
- Auto-save setiap 600ms setelah perubahan
- Mendukung mode revisi
- Feedback visual (success/error)
- Cache clearing untuk data terbaru

### 2. **Endpoint**
```
POST /admin-fakultas/usulan/{id}/autosave
```

## Keamanan

### 1. **Authorization**
- Cek unit kerja Admin Fakultas
- Validasi akses ke usulan
- CSRF protection

### 2. **Data Integrity**
- Backup data sebelum update
- Transaction handling
- Validation sebelum save

## Performance

### 1. **Optimization**
- Debounced auto-save
- Cache management
- Lazy loading untuk riwayat revisi
- Efficient database queries

### 2. **Caching**
- Clear cache setelah update
- Cache validation data
- Cache dokumen paths

## Error Handling

### 1. **Client-Side**
- Form validation errors
- Network error handling
- User-friendly error messages

### 2. **Server-Side**
- Validation exceptions
- File upload errors
- Database transaction errors
- Logging untuk debugging

## Testing

### 1. **Test Cases**
- Form submission pertama kali
- Revisi dengan berbagai kombinasi opsi
- Auto-save functionality
- File upload handling
- Error scenarios

### 2. **Edge Cases**
- Empty form submission
- Invalid file formats
- Network interruptions
- Concurrent access

## Maintenance

### 1. **Monitoring**
- Log semua aksi revisi
- Monitor performance
- Track error rates

### 2. **Cleanup**
- Archive old revision history
- Clean up unused files
- Optimize database queries

## Future Enhancements

### 1. **Potential Features**
- Bulk action untuk field
- Template untuk revisi umum
- Advanced search di riwayat
- Export riwayat revisi

### 2. **Improvements**
- Real-time collaboration
- Version comparison
- Advanced validation rules
- Mobile optimization
