# 🔘 PERBAIKAN LOGIKA TOMBOL BERDASARKAN SUMBER PERBAIKAN

## 🎯 **TUJUAN PERBAIKAN:**

Menyesuaikan logika tombol submit berdasarkan siapa yang mengirim perbaikan:
- **Admin Fakultas** → Tombol "Kirim Perbaikan ke Fakultas"
- **Admin Univ Usulan ke Pegawai** → Tombol "Perbaikan ke Universitas"

## 📁 **FILE YANG DIPERBAIKI:**

`resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/create-jabatan.blade.php`

## 🔧 **PERUBAHAN YANG DILAKUKAN:**

### **1. Logika Deteksi Sumber Perbaikan**

#### **SEBELUM:**
```php
// Determine who sent the revision request based on validation data
$isRevisionFromUniversity = false;
$isRevisionFromFakultas = false;

if ($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan') {
    // Check validation data to determine source of revision
    $adminUnivValidation = $usulan->getValidasiByRole('admin_universitas');
    $adminFakultasValidation = $usulan->getValidasiByRole('admin_fakultas');

    // If Admin Universitas has validation data, revision is from university
    if (!empty($adminUnivValidation)) {
        $isRevisionFromUniversity = true;
    }
    // If only Admin Fakultas has validation data, revision is from fakultas
    elseif (!empty($adminFakultasValidation)) {
        $isRevisionFromFakultas = true;
    }
    // Default: if uncertain, assume from fakultas
    else {
        $isRevisionFromFakultas = true;
    }
}
```

#### **SESUDAH:**
```php
// Determine who sent the revision request based on validation data
$isRevisionFromUniversity = false;
$isRevisionFromFakultas = false;

if ($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan') {
    // Check validation data to determine source of revision
    $adminUnivValidation = $usulan->getValidasiByRole('admin_universitas');
    $adminFakultasValidation = $usulan->getValidasiByRole('admin_fakultas');

    // If Admin Universitas has validation data, revision is from university
    if (!empty($adminUnivValidation)) {
        $isRevisionFromUniversity = true;
    }
    // If only Admin Fakultas has validation data, revision is from fakultas
    elseif (!empty($adminFakultasValidation)) {
        $isRevisionFromFakultas = true;
    }
    // Default: if uncertain, assume from fakultas
    else {
        $isRevisionFromFakultas = true;
    }
}
```

### **2. Logika Tombol Submit**

#### **SEBELUM:**
```blade
{{-- Conditional Submit Buttons --}}
@if($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan')
    {{-- Revision Mode: Submit back to university --}}
    <button type="submit" name="action" value="submit_to_university"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
        <i data-lucide="send" class="w-4 h-4"></i>
        Kirim ke Universitas
    </button>
@else
    {{-- Normal Mode: Submit to fakultas --}}
    <button type="submit" name="action" value="submit"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
        <i data-lucide="send" class="w-4 h-4"></i>
        Kirim Usulan
    </button>
@endif
```

#### **SESUDAH:**
```blade
{{-- Conditional Submit Buttons --}}
@if($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan')
    @if($isRevisionFromUniversity)
        {{-- Perbaikan dari Admin Univ Usulan ke Pegawai --}}
        <button type="submit" name="action" value="submit_to_university"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <i data-lucide="send" class="w-4 h-4"></i>
            Perbaikan ke Universitas
        </button>
    @elseif($isRevisionFromFakultas)
        {{-- Perbaikan dari Admin Fakultas --}}
        <button type="submit" name="action" value="submit"
                class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
            <i data-lucide="send" class="w-4 h-4"></i>
            Kirim Perbaikan ke Fakultas
        </button>
    @else
        {{-- Fallback: Submit back to university --}}
        <button type="submit" name="action" value="submit_to_university"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <i data-lucide="send" class="w-4 h-4"></i>
            Kirim ke Universitas
        </button>
    @endif
@else
    {{-- Normal Mode: Submit to fakultas --}}
    <button type="submit" name="action" value="submit"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
        <i data-lucide="send" class="w-4 h-4"></i>
        Kirim Usulan
    </button>
@endif
```

## 🎨 **PERUBAHAN DESAIN:**

### **1. Warna Tombol:**
- **Admin Fakultas**: `bg-amber-600` (kuning-orange)
- **Admin Universitas**: `bg-blue-600` (biru)
- **Normal Mode**: `bg-indigo-600` (indigo)

### **2. Text Tombol:**
- **Admin Fakultas**: "Kirim Perbaikan ke Fakultas"
- **Admin Universitas**: "Perbaikan ke Universitas"
- **Normal Mode**: "Kirim Usulan"

### **3. Action Value:**
- **Admin Fakultas**: `value="submit"` (kirim ke fakultas)
- **Admin Universitas**: `value="submit_to_university"` (kirim ke universitas)
- **Normal Mode**: `value="submit"` (kirim ke fakultas)

## 🔄 **LOGIKA WORKFLOW:**

### **Scenario 1: Perbaikan dari Admin Fakultas**
1. **Admin Fakultas** → Review usulan → Kirim perbaikan ke Pegawai
2. **Pegawai** → Melihat perbaikan dari Admin Fakultas
3. **Pegawai** → Klik "Kirim Perbaikan ke Fakultas" (tombol amber)
4. **Usulan** → Dikirim kembali ke Admin Fakultas

### **Scenario 2: Perbaikan dari Admin Univ Usulan ke Pegawai**
1. **Admin Univ Usulan** → Review usulan → Kirim perbaikan ke Pegawai
2. **Pegawai** → Melihat perbaikan dari Admin Universitas
3. **Pegawai** → Klik "Perbaikan ke Universitas" (tombol biru)
4. **Usulan** → Dikirim kembali ke Admin Universitas

### **Scenario 3: Usulan Baru**
1. **Pegawai** → Buat usulan baru
2. **Pegawai** → Klik "Kirim Usulan" (tombol indigo)
3. **Usulan** → Dikirim ke Admin Fakultas

## 🧪 **TESTING:**

### **Test Case 1: Perbaikan dari Admin Fakultas**
1. **Login sebagai Pegawai**
2. **Akses usulan yang mendapat perbaikan dari Admin Fakultas**
3. **Verifikasi**: Tombol "Kirim Perbaikan ke Fakultas" (amber) tampil
4. **Verifikasi**: Action value = "submit"

### **Test Case 2: Perbaikan dari Admin Universitas**
1. **Login sebagai Pegawai**
2. **Akses usulan yang mendapat perbaikan dari Admin Universitas**
3. **Verifikasi**: Tombol "Perbaikan ke Universitas" (biru) tampil
4. **Verifikasi**: Action value = "submit_to_university"

### **Test Case 3: Usulan Baru**
1. **Login sebagai Pegawai**
2. **Buat usulan baru**
3. **Verifikasi**: Tombol "Kirim Usulan" (indigo) tampil
4. **Verifikasi**: Action value = "submit"

### **Test Case 4: Deteksi Otomatis**
1. **Test berbagai kondisi data validation**
2. **Verifikasi**: Tombol yang tepat tampil berdasarkan data
3. **Verifikasi**: Fallback logic berfungsi dengan baik

## 🎉 **HASIL AKHIR:**

### **✅ Logika Tombol yang Tepat:**
- **Admin Fakultas** → "Kirim Perbaikan ke Fakultas" (amber)
- **Admin Universitas** → "Perbaikan ke Universitas" (biru)
- **Usulan Baru** → "Kirim Usulan" (indigo)

### **✅ Deteksi Otomatis:**
- **Berdasarkan validation data** dari masing-masing role
- **Fallback logic** untuk kondisi yang tidak pasti
- **Konsistensi** dengan workflow sistem

### **✅ User Experience yang Baik:**
- **Tombol yang jelas** menunjukkan tujuan pengiriman
- **Warna yang berbeda** untuk membedakan jenis perbaikan
- **Text yang informatif** untuk user

### **✅ Workflow yang Benar:**
- **Pegawai tahu** kemana usulan akan dikirim
- **Tidak ada kebingungan** tentang langkah selanjutnya
- **Proses yang smooth** dari perbaikan ke pengiriman

**Sekarang tombol submit menampilkan text dan warna yang tepat berdasarkan sumber perbaikan!** 🔘✨

**Workflow yang jelas dan user-friendly!** 🎯

**Deteksi otomatis berdasarkan data validation!** 🔍
