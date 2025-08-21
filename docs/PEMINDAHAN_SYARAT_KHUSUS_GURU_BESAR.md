# Pemindahan Section Syarat Khusus Pengajuan Guru Besar

## Deskripsi
Memindahkan section "Syarat Khusus Pengajuan Guru Besar" dari komponen dokumen-upload ke komponen bkd-upload, sehingga section ini muncul setelah "Beban Kinerja Dosen".

## Implementasi

### File yang Dimodifikasi

#### **1. Dokumen Upload Component**
**File**: `resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/components/dokumen-upload.blade.php`

#### **Perubahan:**
- ✅ **Dihapus**: Section "Syarat Khusus Pengajuan Guru Besar" (baris 336-450)
- ✅ **Diperbarui**: Progress summary untuk tidak menghitung syarat guru besar

#### **Kode yang Dihapus:**
```php
{{-- SYARAT KHUSUS GURU BESAR --}}
@if($formConfig['show_syarat_khusus'] && $formConfig['required_documents']['bukti_syarat_guru_besar'])
    // ... seluruh section syarat guru besar
@endif
```

#### **Progress Summary Update:**
```php
// SEBELUM
if ($formConfig['show_syarat_khusus'] ?? false) {
    $totalDokumen += 2; // syarat_guru_besar + bukti_syarat_guru_besar
}

// SESUDAH
// Syarat Khusus Guru Besar sudah dipindahkan ke BKD component
```

#### **2. BKD Upload Component**
**File**: `resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/components/bkd-upload.blade.php`

#### **Perubahan:**
- ✅ **Ditambahkan**: Section "Syarat Khusus Pengajuan Guru Besar" setelah BKD
- ✅ **Diperbarui**: Progress summary untuk menghitung syarat guru besar
- ✅ **Styling**: Border top untuk memisahkan dari BKD

#### **Kode yang Ditambahkan:**
```php
{{-- SYARAT KHUSUS GURU BESAR --}}
@if($formConfig['show_syarat_khusus'] && $formConfig['required_documents']['bukti_syarat_guru_besar'])
    @php
        $syaratGuruBesarValidation = $catatanPerbaikan['syarat_khusus']['syarat_guru_besar'] ?? null;
        $isSyaratGuruBesarInvalid = $syaratGuruBesarValidation && $syaratGuruBesarValidation['status'] === 'tidak_sesuai';

        $buktiGuruBesarValidation = $catatanPerbaikan['dokumen_usulan']['bukti_syarat_guru_besar'] ?? null;
        $isBuktiGuruBesarInvalid = $buktiGuruBesarValidation && $buktiGuruBesarValidation['status'] === 'tidak_sesuai';

        $buktiGuruBesarExists = false;
        if (isset($usulan)) {
            $buktiGuruBesarExists = !empty($usulan->data_usulan['dokumen_usulan']['bukti_syarat_guru_besar']['path']) ||
                                    !empty($usulan->data_usulan['bukti_syarat_guru_besar']);
        }
    @endphp

    <div class="mt-6 space-y-4 pt-6 border-t {{ ($isSyaratGuruBesarInvalid || $isBuktiGuruBesarInvalid) ? 'border-red-200' : 'border-gray-200' }}">
        // ... seluruh section syarat guru besar
    </div>
@endif
```

#### **Progress Summary Update:**
```php
// SEBELUM
$totalBkd = count($bkdSemesters);

// SESUDAH
$totalBkd = count($bkdSemesters);
// Tambahkan Syarat Khusus Guru Besar jika ada
if ($formConfig['show_syarat_khusus'] && $formConfig['required_documents']['bukti_syarat_guru_besar']) {
    $totalBkd += 2; // syarat_guru_besar + bukti_syarat_guru_besar
    
    // Hitung error untuk Syarat Khusus Guru Besar
    $syaratGuruBesarValidation = $catatanPerbaikan['syarat_khusus']['syarat_guru_besar'] ?? null;
    $isSyaratGuruBesarInvalid = $syaratGuruBesarValidation && $syaratGuruBesarValidation['status'] === 'tidak_sesuai';
    
    $buktiGuruBesarValidation = $catatanPerbaikan['dokumen_usulan']['bukti_syarat_guru_besar'] ?? null;
    $isBuktiGuruBesarInvalid = $buktiGuruBesarValidation && $buktiGuruBesarValidation['status'] === 'tidak_sesuai';
    
    if ($isSyaratGuruBesarInvalid) $bkdErrors++;
    if ($isBuktiGuruBesarInvalid) $bkdErrors++;
}
```

## Urutan Section Baru

### **Sebelum:**
1. ✅ **Informasi Periode Usulan**
2. ✅ **Informasi Pegawai**
3. ✅ **Profile Display Component**
4. ✅ **Karya Ilmiah Section Component**
5. ✅ **Dokumen Upload Component**
   - Pakta Integritas
   - Bukti Korespondensi
   - Turnitin
   - Upload Artikel
   - **Syarat Khusus Pengajuan Guru Besar** ← Dipindahkan
6. ✅ **BKD Upload Component**
   - BKD Semester 1-4
7. ✅ **Form Actions**

### **Sesudah:**
1. ✅ **Informasi Periode Usulan**
2. ✅ **Informasi Pegawai**
3. ✅ **Profile Display Component**
4. ✅ **Karya Ilmiah Section Component**
5. ✅ **Dokumen Upload Component**
   - Pakta Integritas
   - Bukti Korespondensi
   - Turnitin
   - Upload Artikel
6. ✅ **BKD Upload Component**
   - BKD Semester 1-4
   - **Syarat Khusus Pengajuan Guru Besar** ← Dipindahkan ke sini
7. ✅ **Form Actions**

## Styling dan Layout

### **Pemisahan Visual:**
- ✅ **Border Top**: `border-t border-gray-200` untuk memisahkan dari BKD
- ✅ **Margin Top**: `mt-6` untuk spacing yang cukup
- ✅ **Padding Top**: `pt-6` untuk spacing internal

### **Conditional Styling:**
- ✅ **Error State**: Border merah jika ada error
- ✅ **Normal State**: Border abu-abu jika normal
- ✅ **Consistent**: Menggunakan styling yang sama dengan section lain

## Progress Summary Update

### **Dokumen Upload Component:**
- ✅ **Sebelum**: Menghitung syarat guru besar (total +2)
- ✅ **Sesudah**: Tidak menghitung syarat guru besar
- ✅ **Result**: Progress hanya untuk dokumen umum

### **BKD Upload Component:**
- ✅ **Sebelum**: Hanya menghitung BKD (4 semester)
- ✅ **Sesudah**: Menghitung BKD + syarat guru besar (4 + 2 = 6)
- ✅ **Result**: Progress untuk BKD dan syarat guru besar

### **Text Update:**
- ✅ **Sebelum**: "X laporan valid"
- ✅ **Sesudah**: "X dokumen valid"
- ✅ **Reason**: Sekarang mencakup BKD dan syarat guru besar

## Benefits

### ✅ **User Experience:**
- **Logical Flow**: Syarat guru besar muncul setelah BKD yang relevan
- **Better Organization**: Pengelompokan yang lebih logis
- **Clear Separation**: Visual separation yang jelas

### ✅ **Administrative:**
- **Related Content**: BKD dan syarat guru besar dalam satu section
- **Easier Review**: Admin dapat review BKD dan syarat guru besar bersama
- **Better Workflow**: Workflow yang lebih terstruktur

### ✅ **Technical:**
- **Maintainable**: Kode lebih terorganisir
- **Consistent**: Styling dan behavior yang konsisten
- **Scalable**: Mudah untuk menambah section serupa

## Testing Checklist

### ✅ **Functional Testing:**
- [ ] Section syarat guru besar muncul di BKD component
- [ ] Section syarat guru besar tidak muncul di dokumen component
- [ ] Progress summary BKD menghitung syarat guru besar
- [ ] Progress summary dokumen tidak menghitung syarat guru besar
- [ ] Form submission tetap berfungsi normal

### ✅ **UI/UX Testing:**
- [ ] Visual separation yang jelas antara BKD dan syarat guru besar
- [ ] Styling konsisten dengan section lain
- [ ] Responsive di mobile dan desktop
- [ ] Error states berfungsi dengan benar

### ✅ **Data Testing:**
- [ ] Data syarat guru besar tersimpan dengan benar
- [ ] Validation tetap berfungsi
- [ ] File upload tetap berfungsi
- [ ] Progress calculation akurat

### ✅ **Workflow Testing:**
- [ ] Urutan section sesuai dengan requirement
- [ ] User dapat mengisi BKD dan syarat guru besar secara berurutan
- [ ] Form validation tetap berjalan
- [ ] Submit process tetap normal

## Edge Cases

### ✅ **Handling:**
- **No Syarat Khusus**: Section tidak muncul jika tidak diperlukan
- **Error States**: Error styling tetap berfungsi
- **File Upload**: File upload tetap berfungsi normal
- **Validation**: Validation tetap berjalan dengan benar

## Future Enhancements

### 🔮 **Potential Features:**
- **Collapsible Section**: Bisa collapse/expand section
- **Step Indicator**: Progress indicator untuk setiap section
- **Auto Save**: Auto save per section
- **Section Navigation**: Quick navigation antar section

### 🛠️ **Technical Improvements:**
- **Component Reusability**: Buat component terpisah untuk syarat guru besar
- **Dynamic Loading**: Load section berdasarkan kondisi
- **Better Validation**: Enhanced validation per section
- **Performance**: Optimize loading per section

## Status Implementasi

### ✅ **Selesai:**
- Section syarat guru besar dipindahkan ke BKD component
- Progress summary diperbarui di kedua component
- Styling dan layout disesuaikan
- Visual separation ditambahkan

### 📋 **Hasil:**
- Urutan section sesuai requirement
- Progress calculation akurat
- User experience lebih baik
- Tidak ada breaking changes
- Code lebih terorganisir
