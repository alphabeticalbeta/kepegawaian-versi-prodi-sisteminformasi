# Penambahan Tombol Simpan Usulan

## Deskripsi
Menambahkan tombol "Simpan Usulan" di samping tombol "Kirim Usulan" untuk memberikan opsi menyimpan usulan sebagai draft tanpa mengirimkannya.

## Implementasi

### File yang Dimodifikasi

#### **1. View - Form Actions**
**File**: `resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/create-jabatan.blade.php`

#### **Perubahan pada Form Actions:**
```php
// SEBELUM
<button type="submit"
        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
    <i data-lucide="save" class="w-4 h-4"></i>
    {{ $isEditMode ? 'Update Usulan' : 'Kirim Usulan' }}
</button>

// SESUDAH
<button type="submit" name="action" value="save_draft"
        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
    <i data-lucide="save" class="w-4 h-4"></i>
    Simpan Usulan
</button>
<button type="submit" name="action" value="submit"
        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
    <i data-lucide="send" class="w-4 h-4"></i>
    {{ $isEditMode ? 'Update Usulan' : 'Kirim Usulan' }}
</button>
```

#### **2. Controller - Store Method**
**File**: `app/Http/Controllers/Backend/PegawaiUnmul/UsulanJabatanController.php`

#### **Perubahan pada Logic Action:**
```php
// SEBELUM
$statusUsulan = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

// SESUDAH
$action = $request->input('action');
$statusUsulan = match($action) {
    'submit' => 'Diajukan',
    'save_draft' => 'Draft',
    default => 'Draft'
};
```

#### **Perubahan pada Success Message:**
```php
// SEBELUM
$message = $statusUsulan === 'Diajukan'
    ? 'Usulan kenaikan jabatan berhasil diajukan. Tim verifikasi akan meninjau usulan Anda.'
    : 'Usulan jabatan berhasil disimpan sebagai draft. Anda dapat melanjutkan pengisian nanti.';

// SESUDAH
$message = match($action) {
    'submit' => 'Usulan kenaikan jabatan berhasil diajukan. Tim verifikasi akan meninjau usulan Anda.',
    'save_draft' => 'Usulan jabatan berhasil disimpan sebagai draft. Anda dapat melanjutkan pengisian nanti.',
    default => 'Usulan jabatan berhasil disimpan.'
};
```

#### **3. Controller - Update Method**
**File**: `app/Http/Controllers/Backend/PegawaiUnmul/UsulanJabatanController.php`

#### **Perubahan pada Logic Action:**
```php
// SEBELUM
$statusUsulan = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

// SESUDAH
$action = $request->input('action');
$statusUsulan = match($action) {
    'submit' => 'Diajukan',
    'save_draft' => 'Draft',
    default => 'Draft'
};
```

#### **Perubahan pada Success Message:**
```php
// SEBELUM
$message = $statusUsulan === 'Diajukan'
    ? 'Usulan kenaikan jabatan berhasil diperbarui dan diajukan. Tim verifikasi akan meninjau usulan Anda.'
    : 'Perubahan pada usulan Anda berhasil disimpan sebagai Draft.';

// SESUDAH
$message = match($action) {
    'submit' => 'Usulan kenaikan jabatan berhasil diperbarui dan diajukan. Tim verifikasi akan meninjau usulan Anda.',
    'save_draft' => 'Perubahan pada usulan Anda berhasil disimpan sebagai Draft.',
    default => 'Perubahan pada usulan Anda berhasil disimpan.'
};
```

## Fitur yang Ditambahkan

### ✅ **Tombol Simpan Usulan:**
- **Action**: `save_draft`
- **Status**: `Draft`
- **Color**: Green (bg-green-600)
- **Icon**: Save icon
- **Text**: "Simpan Usulan"

### ✅ **Tombol Kirim Usulan:**
- **Action**: `submit`
- **Status**: `Diajukan`
- **Color**: Indigo (bg-indigo-600)
- **Icon**: Send icon
- **Text**: "Kirim Usulan" / "Update Usulan"

### ✅ **Logic Action:**
- **Match Statement**: Menggunakan PHP 8 match expression
- **Action Mapping**: 
  - `submit` → `Diajukan`
  - `save_draft` → `Draft`
  - `default` → `Draft`

### ✅ **Success Messages:**
- **Submit**: Pesan pengajuan usulan
- **Save Draft**: Pesan penyimpanan draft
- **Default**: Pesan penyimpanan umum

## Tampilan Tombol

### **Desktop Layout:**
```
┌─────────────────────────────────────────────────────────────┐
│ [Batal] [Simpan Usulan] [Kirim Usulan]                     │
└─────────────────────────────────────────────────────────────┘
```

### **Mobile Layout:**
```
┌─────────────────────────────────────────────────────────────┐
│ [Batal]                                                     │
│ [Simpan Usulan]                                             │
│ [Kirim Usulan]                                              │
└─────────────────────────────────────────────────────────────┘
```

## Styling dan Design

### **Tombol Simpan Usulan:**
- **Background**: `bg-green-600`
- **Hover**: `hover:bg-green-700`
- **Text**: White
- **Icon**: Save icon (Lucide)
- **Border Radius**: `rounded-lg`
- **Padding**: `px-6 py-3`

### **Tombol Kirim Usulan:**
- **Background**: `bg-indigo-600`
- **Hover**: `hover:bg-indigo-700`
- **Text**: White
- **Icon**: Send icon (Lucide)
- **Border Radius**: `rounded-lg`
- **Padding**: `px-6 py-3`

### **Tombol Batal:**
- **Background**: White
- **Border**: `border-gray-300`
- **Text**: `text-gray-700`
- **Hover**: `hover:bg-gray-50`

## Workflow

### **Simpan Usulan (Draft):**
1. ✅ User mengisi form
2. ✅ Klik "Simpan Usulan"
3. ✅ Action: `save_draft`
4. ✅ Status: `Draft`
5. ✅ Redirect ke dashboard dengan pesan draft
6. ✅ User dapat edit lagi nanti

### **Kirim Usulan (Submit):**
1. ✅ User mengisi form
2. ✅ Klik "Kirim Usulan"
3. ✅ Action: `submit`
4. ✅ Status: `Diajukan`
5. ✅ Redirect ke dashboard dengan pesan submit
6. ✅ Usulan masuk ke proses verifikasi

## Benefits

### ✅ **User Experience:**
- **Flexibility**: User dapat menyimpan progress tanpa submit
- **Confidence**: User dapat review sebelum submit final
- **Safety**: Mencegah kehilangan data saat mengisi form panjang

### ✅ **Administrative:**
- **Draft Management**: Admin dapat melihat usulan draft
- **Progress Tracking**: Tracking progress pengisian usulan
- **Data Integrity**: Memastikan data lengkap sebelum submit

### ✅ **System:**
- **Status Management**: Clear status distinction
- **Workflow Control**: Better control over submission process
- **Audit Trail**: Clear log of save vs submit actions

## Testing Checklist

### ✅ **Functional Testing:**
- [ ] Tombol "Simpan Usulan" berfungsi
- [ ] Tombol "Kirim Usulan" berfungsi
- [ ] Action `save_draft` menghasilkan status `Draft`
- [ ] Action `submit` menghasilkan status `Diajukan`
- [ ] Pesan sukses sesuai dengan action

### ✅ **UI/UX Testing:**
- [ ] Tombol terlihat jelas dan berbeda
- [ ] Hover effects berfungsi
- [ ] Responsive di mobile dan desktop
- [ ] Icon dan text sesuai dengan action

### ✅ **Data Testing:**
- [ ] Status usulan tersimpan dengan benar
- [ ] Log entry dibuat sesuai action
- [ ] Background jobs hanya dijalankan untuk submit
- [ ] Data tidak hilang saat save draft

### ✅ **Workflow Testing:**
- [ ] Draft dapat diedit kembali
- [ ] Submit final berhasil
- [ ] Redirect ke halaman yang benar
- [ ] Flash message muncul dengan benar

## Edge Cases

### ✅ **Handling:**
- **No Action**: Default ke `Draft`
- **Invalid Action**: Default ke `Draft`
- **Empty Form**: Validation tetap berjalan
- **Network Error**: Error handling tetap berfungsi

## Future Enhancements

### 🔮 **Potential Features:**
- **Auto Save**: Auto save setiap 5 menit
- **Draft List**: Halaman khusus untuk melihat draft
- **Draft Expiry**: Auto delete draft lama
- **Draft Sharing**: Share draft dengan admin

### 🛠️ **Technical Improvements:**
- **AJAX Save**: Save tanpa page reload
- **Progress Indicator**: Show save progress
- **Draft Versioning**: Multiple draft versions
- **Draft Comparison**: Compare draft vs submitted

## Status Implementasi

### ✅ **Selesai:**
- Tombol "Simpan Usulan" ditambahkan
- Logic action di controller diperbarui
- Success messages diperbarui
- Styling konsisten dengan design system

### 📋 **Hasil:**
- User dapat menyimpan usulan sebagai draft
- User dapat submit usulan final
- Workflow yang jelas dan user-friendly
- Tidak ada breaking changes
- UX meningkat dengan opsi yang lebih fleksibel
