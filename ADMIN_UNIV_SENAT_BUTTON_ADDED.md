# 👑 ADMIN UNIV USULAN: TERUSKAN KE TIM SENAT BUTTON

## ✅ **PERUBAHAN YANG DILAKUKAN:**

### **1. Menambahkan Button "Teruskan ke Tim Senat"**
**File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

```blade
{{-- Button Teruskan ke Tim Senat - aktif jika penilai merekomendasikan --}}
@if($hasRecommendation === 'direkomendasikan')
    <button type="button" id="btn-teruskan-senat" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
        <i data-lucide="crown" class="w-4 h-4"></i>
        Teruskan ke Tim Senat
    </button>
@endif
```

### **2. Menambahkan JavaScript Handler**
**File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

```javascript
if (document.getElementById('btn-teruskan-senat')) {
    document.getElementById('btn-teruskan-senat').addEventListener('click', function() {
        showTeruskanKeSenatModal();
    });
}
```

### **3. Menambahkan Modal Function**
**File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

```javascript
function showTeruskanKeSenatModal() {
    Swal.fire({
        title: 'Teruskan ke Tim Senat',
        text: 'Usulan akan diteruskan ke Tim Senat untuk keputusan final. Pastikan rekomendasi dari Tim Penilai sudah lengkap.',
        input: 'textarea',
        inputPlaceholder: 'Catatan untuk Tim Senat (opsional)...',
        confirmButtonText: 'Teruskan ke Tim Senat',
        confirmButtonColor: '#7c3aed'
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('forward_to_senat', result.value);
        }
    });
}
```

## 🎯 **KONDISI AKTIF BUTTON:**

### **Button "Teruskan ke Tim Senat" AKTIF jika:**
- ✅ **Status usulan** = `'Menunggu Review Admin Univ'`
- ✅ **Tim Penilai sudah submit rekomendasi** (`$hasRecommendation === 'direkomendasikan'`)
- ✅ **Semua penilai sudah submit** (`$allPenilaisSubmitted = true`)

### **Button "Teruskan ke Tim Senat" NON-AKTIF jika:**
- ❌ **Tim Penilai belum submit rekomendasi**
- ❌ **Tim Penilai hanya submit perbaikan** (bukan rekomendasi)
- ❌ **Belum semua penilai submit**

## 🔄 **ALUR KERJA LENGKAP:**

### **Skenario: Admin Univ Usulan → Tim Senat**
```
1. Tim Penilai → Submit rekomendasi → Status: "Menunggu Review Admin Univ"
2. Admin Univ Usulan → Buka detail usulan
3. Admin Univ Usulan → Lihat:
   ├── 📋 Detail rekomendasi dari penilai
   ├── ℹ️ Info: "Rekomendasi dari Tim Penilai telah diterima"
   └── 👑 Button "Teruskan ke Tim Senat" (AKTIF)
4. Admin Univ Usulan → Klik "Teruskan ke Tim Senat"
5. Modal → Input catatan (opsional) → Konfirmasi
6. Sistem → Update status ke "Direkomendasikan"
7. Usulan → Dikirim ke Tim Senat untuk keputusan final
```

## 📊 **VALIDASI DI CONTROLLER:**

### **Method `forwardToSenat()` di `UsulanValidationController.php`:**
```php
// Check if Tim Penilai has given recommendation
$hasRecommendation = $usulan->validasi_data['tim_penilai']['recommendation'] ?? false;
if ($hasRecommendation !== 'direkomendasikan') {
    return response()->json([
        'success' => false,
        'message' => 'Usulan tidak dapat diteruskan ke senat karena belum ada rekomendasi dari tim penilai.'
    ], 422);
}

// Update usulan status
$usulan->status_usulan = 'Direkomendasikan';

// Save forward information
$currentValidasi['admin_universitas']['forward_to_senat'] = [
    'catatan' => $request->input('catatan_umum'),
    'tanggal_forward' => now()->toDateTimeString(),
    'admin_id' => Auth::id()
];
```

## 🎨 **TAMPILAN BUTTON:**

### **Button yang Tersedia untuk Admin Univ Usulan:**
1. **🔴 Perbaikan ke Pegawai** - Untuk semua kasus perbaikan
2. **🟠 Perbaikan ke Fakultas** - Untuk semua kasus perbaikan
3. **🔵 Teruskan ke Penilai** - Untuk mengirim ulang ke penilai
4. **👑 Teruskan ke Tim Senat** - **BARU!** Hanya aktif jika penilai merekomendasikan

### **Styling Button:**
- **Warna:** Purple (`bg-purple-600`, `hover:bg-purple-700`)
- **Icon:** Crown (`data-lucide="crown"`)
- **Kondisi:** Hanya tampil jika `$hasRecommendation === 'direkomendasikan'`

## 🧪 **TESTING:**

**URL Test:** `http://localhost/admin-univ-usulan/usulan/16`

**Expected Results:**
1. ✅ **Button "Teruskan ke Tim Senat" tampil** jika penilai merekomendasikan
2. ✅ **Button dengan warna purple dan icon crown**
3. ✅ **Modal SweetAlert** muncul saat klik button
4. ✅ **Form submit** ke controller dengan action `forward_to_senat`
5. ✅ **Status usulan berubah** ke `'Direkomendasikan'`
6. ✅ **Log/history** tersimpan untuk tracking

## 🎉 **HASIL:**

**✅ BUTTON "TERUSKAN KE TIM SENAT" BERHASIL DITAMBAHKAN!**

Sekarang Admin Univ Usulan dapat mengirim usulan ke Tim Senat ketika Tim Penilai sudah memberikan rekomendasi.

**Alur lengkap:** Admin Univ Usulan → Tim Senat → Keputusan Final → Pegawai

**Silakan refresh halaman `http://localhost/admin-univ-usulan/usulan/16` untuk melihat button baru!** 🎯
