# 🔧 PERBAIKAN LOGIKA SISTEM ADMIN UNIV USULAN

## 🎯 **LOGIKA BARU YANG DIIMPLEMENTASIKAN:**

### **SKENARIO 1: Belum Dinilai oleh Tim Penilai Universitas**
```
1. Admin Univ Usulan → Review usulan
2. Admin Univ Usulan → Input catatan perbaikan sendiri
3. Admin Univ Usulan → Pilih tindakan:
   ├── Perbaikan ke Pegawai (dengan catatan Admin Univ)
   ├── Perbaikan ke Fakultas (dengan catatan Admin Univ)
   └── Teruskan ke Penilai
```

### **SKENARIO 2: Sudah Dinilai oleh Tim Penilai Universitas**
```
1. Admin Univ Usulan → Review hasil penilaian dari Tim Penilai
2. Admin Univ Usulan → Lihat catatan dari Tim Penilai
3. Admin Univ Usulan → Pilih tindakan:
   ├── Teruskan ke Pegawai (dengan catatan dari Penilai)
   ├── Teruskan ke Fakultas (dengan catatan dari Penilai)
   └── Teruskan ke Tim Senat (jika ada rekomendasi)
```

## ✅ **PERUBAHAN YANG TELAH DILAKUKAN:**

### **1. Perbaikan Controller (UsulanValidationController.php)**

#### **Method `returnToPegawai()` - LOGIKA BARU:**
```php
// Deteksi status penilaian
$penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
$hasPenilaiReview = false;
$catatanPenilai = '';

// Check multiple structures untuk deteksi yang lebih akurat
if (!empty($penilaiReview['reviews'])) {
    $hasPenilaiReview = true;
    // Get first review's catatan
    foreach ($penilaiReview['reviews'] as $review) {
        if (!empty($review['perbaikan_usulan']['catatan'])) {
            $catatanPenilai = $review['perbaikan_usulan']['catatan'];
            break;
        }
    }
}
elseif (!empty($penilaiReview['perbaikan_usulan']['catatan'])) {
    $hasPenilaiReview = true;
    $catatanPenilai = $penilaiReview['perbaikan_usulan']['catatan'];
}
elseif (!empty($penilaiReview['validation'])) {
    $hasPenilaiReview = true;
    $catatanPenilai = 'Hasil penilaian dari Tim Penilai Universitas';
}

// SKENARIO 2: Sudah dinilai - Teruskan hasil penilaian
if ($hasPenilaiReview) {
    // Catatan optional karena menggunakan catatan penilai
    $request->validate(['catatan_umum' => 'nullable|string|max:1000']);
    
    // Gunakan catatan dari penilai jika tidak ada input dari admin
    $catatanFinal = $request->input('catatan_umum') ?: $catatanPenilai;
    
    // Simpan informasi bahwa ini adalah hasil penilaian yang diteruskan
    $currentValidasi['admin_universitas']['forward_penilai_result'] = [
        'action' => 'return_to_pegawai',
        'catatan_source' => 'tim_penilai',
        'original_catatan' => $catatanPenilai,
        'admin_catatan' => $request->input('catatan_umum'),
        'final_catatan' => $catatanFinal,
        'forwarded_at' => now()->toDateTimeString(),
        'admin_id' => Auth::id()
    ];
    
    $message = 'Hasil penilaian dari Tim Penilai Universitas berhasil diteruskan ke Pegawai untuk perbaikan.';
}
// SKENARIO 1: Belum dinilai - Admin Univ input catatan sendiri
else {
    // Catatan wajib karena Admin Univ input sendiri
    $request->validate(['catatan_umum' => 'required|string|max:1000']);
    
    // Simpan informasi bahwa ini adalah catatan Admin Univ sendiri
    $currentValidasi['admin_universitas']['direct_review'] = [
        'action' => 'return_to_pegawai',
        'catatan_source' => 'admin_universitas',
        'catatan' => $request->input('catatan_umum'),
        'reviewed_at' => now()->toDateTimeString(),
        'admin_id' => Auth::id()
    ];
    
    $message = 'Usulan berhasil dikembalikan ke Pegawai untuk perbaikan berdasarkan review Admin Universitas.';
}
```

#### **Method `returnToFakultas()` - LOGIKA BARU:**
- ✅ **Sama dengan `returnToPegawai()`** tetapi untuk fakultas
- ✅ **Deteksi status penilaian yang sama**
- ✅ **Logika yang konsisten**

### **2. Perbaikan View (usulan-detail.blade.php)**

#### **Deteksi Status Penilaian:**
```php
@php
    // Cek apakah sudah ada hasil penilaian dari Tim Penilai
    $penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
    
    // Deteksi apakah ada review dari penilai (multiple ways)
    $hasPenilaiReview = false;
    $catatanPenilai = '';
    
    // Check new structure first
    if (!empty($penilaiReview['reviews'])) {
        $hasPenilaiReview = true;
        // Get first review's catatan
        foreach ($penilaiReview['reviews'] as $review) {
            if (!empty($review['perbaikan_usulan']['catatan'])) {
                $catatanPenilai = $review['perbaikan_usulan']['catatan'];
                break;
            }
        }
    }
    // Check old structure
    elseif (!empty($penilaiReview['perbaikan_usulan']['catatan'])) {
        $hasPenilaiReview = true;
        $catatanPenilai = $penilaiReview['perbaikan_usulan']['catatan'];
    }
    // Check if there's any validation data from penilai
    elseif (!empty($penilaiReview['validation'])) {
        $hasPenilaiReview = true;
        $catatanPenilai = 'Hasil penilaian dari Tim Penilai Universitas';
    }
@endphp
```

#### **Button yang Berbeda Berdasarkan Status:**

**SKENARIO 2: Sudah Dinilai (Teruskan Hasil Penilaian)**
```blade
@if($hasPenilaiReview)
    {{-- SKENARIO 2: Sudah dinilai oleh Tim Penilai - Teruskan hasil penilaian --}}
    <div class="flex gap-2 flex-wrap">
        <button type="button" id="btn-teruskan-ke-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
            <i data-lucide="user-x" class="w-4 h-4"></i>
            Teruskan ke Pegawai
        </button>

        <button type="button" id="btn-teruskan-ke-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
            <i data-lucide="building-2" class="w-4 h-4"></i>
            Teruskan ke Fakultas
        </button>

        {{-- Button Teruskan ke Tim Senat - aktif jika penilai merekomendasikan --}}
        @if($hasRecommendation === 'direkomendasikan')
            <button type="button" id="btn-teruskan-senat" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <i data-lucide="crown" class="w-4 h-4"></i>
                Teruskan ke Tim Senat
            </button>
        @endif
    </div>

    {{-- Info: Menampilkan hasil penilaian yang akan diteruskan --}}
    @if($catatanPenilai)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
            <div class="flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Hasil Penilaian dari Tim Penilai yang akan diteruskan:</p>
                    <p class="text-xs bg-blue-100 p-2 rounded">{{ $catatanPenilai }}</p>
                </div>
            </div>
        </div>
    @endif
@else
    {{-- SKENARIO 1: Belum dinilai oleh Tim Penilai - Admin Univ input catatan sendiri --}}
    <div class="flex gap-2 flex-wrap">
        <button type="button" id="btn-perbaikan-pegawai" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
            <i data-lucide="user-x" class="w-4 h-4"></i>
            Perbaikan ke Pegawai
        </button>

        <button type="button" id="btn-perbaikan-fakultas" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
            <i data-lucide="building-2" class="w-4 h-4"></i>
            Perbaikan ke Fakultas
        </button>

        <button type="button" id="btn-teruskan-penilai" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <i data-lucide="user-check" class="w-4 h-4"></i>
            Teruskan ke Penilai
        </button>
    </div>

    {{-- Info: Admin Univ akan input catatan sendiri --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
        <div class="flex items-center gap-2">
            <i data-lucide="edit" class="w-4 h-4 text-yellow-600"></i>
            <span class="text-sm text-yellow-800">
                Belum ada hasil penilaian dari Tim Penilai. Admin Universitas akan memberikan catatan perbaikan sendiri.
            </span>
        </div>
    </div>
@endif
```

### **3. Perbaikan JavaScript**

#### **Button Handlers Baru:**
```javascript
// NEW: Button handlers untuk meneruskan hasil penilaian
if (document.getElementById('btn-teruskan-ke-pegawai')) {
    document.getElementById('btn-teruskan-ke-pegawai').addEventListener('click', function() {
        showTeruskanKePegawaiModal();
    });
}

if (document.getElementById('btn-teruskan-ke-fakultas')) {
    document.getElementById('btn-teruskan-ke-fakultas').addEventListener('click', function() {
        showTeruskanKeFakultasModal();
    });
}
```

#### **Modal Functions Baru:**
```javascript
// NEW: Function untuk meneruskan hasil penilaian ke Pegawai
function showTeruskanKePegawaiModal() {
    Swal.fire({
        title: 'Teruskan Hasil Penilaian ke Pegawai',
        text: 'Hasil penilaian dari Tim Penilai Universitas akan diteruskan ke Pegawai untuk perbaikan. Anda dapat menambahkan catatan tambahan jika diperlukan.',
        input: 'textarea',
        inputPlaceholder: 'Catatan tambahan (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan tambahan'
        },
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Pegawai',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        preConfirm: (catatan) => {
            return catatan || '';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_pegawai', result.value);
        }
    });
}

// NEW: Function untuk meneruskan hasil penilaian ke Fakultas
function showTeruskanKeFakultasModal() {
    Swal.fire({
        title: 'Teruskan Hasil Penilaian ke Fakultas',
        text: 'Hasil penilaian dari Tim Penilai Universitas akan diteruskan ke Admin Fakultas untuk perbaikan. Anda dapat menambahkan catatan tambahan jika diperlukan.',
        input: 'textarea',
        inputPlaceholder: 'Catatan tambahan (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan tambahan'
        },
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Fakultas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d97706',
        preConfirm: (catatan) => {
            return catatan || '';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_fakultas', result.value);
        }
    });
}
```

## 📊 **PERBANDINGAN SEBELUM vs SESUDAH:**

### **SEBELUM (Logika Lama):**
```
Admin Univ → Selalu input catatan → Kirim ke Pegawai/Fakultas
❌ Tidak membedakan sumber catatan
❌ Tidak meneruskan hasil penilaian Tim Penilai
❌ Admin Univ menimpa hasil penilaian
```

### **SESUDAH (Logika Baru):**
```
// Belum dinilai:
Admin Univ → Input catatan sendiri → Kirim ke Pegawai/Fakultas

// Sudah dinilai:
Admin Univ → Teruskan catatan Penilai → Kirim ke Pegawai/Fakultas
✅ Membedakan sumber catatan
✅ Meneruskan hasil penilaian Tim Penilai
✅ Admin Univ tidak menimpa hasil penilaian
```

## 🔄 **ALUR KERJA BARU:**

### **Skenario A: Belum Ada Penilaian**
```
1. Admin Univ → Review usulan
2. Admin Univ → Input catatan perbaikan
3. Admin Univ → Klik "Perbaikan ke Pegawai"
4. Sistem → Simpan catatan Admin Univ → Kirim ke Pegawai
```

### **Skenario B: Sudah Ada Penilaian**
```
1. Admin Univ → Review hasil penilaian dari Tim Penilai
2. Admin Univ → Lihat catatan dari Penilai
3. Admin Univ → Klik "Teruskan ke Pegawai"
4. Sistem → Teruskan catatan dari Penilai → Kirim ke Pegawai
```

## 🧪 **TESTING SCENARIOS:**

### **Test Case 1: Belum Dinilai**
- ✅ Usulan status: "Diusulkan ke Universitas"
- ✅ Tidak ada data penilaian
- ✅ Admin Univ bisa input catatan
- ✅ Button "Perbaikan ke Pegawai" dengan catatan Admin Univ

### **Test Case 2: Sudah Dinilai**
- ✅ Usulan status: "Menunggu Review Admin Univ"
- ✅ Ada data penilaian dari Tim Penilai
- ✅ Admin Univ lihat hasil penilaian
- ✅ Button "Teruskan ke Pegawai" dengan catatan dari Penilai

## 🎉 **KEUNTUNGAN PERBAIKAN:**

### **✅ Logika yang Benar:**
- Admin Univ tidak menimpa hasil penilaian Tim Penilai
- Catatan yang diteruskan sesuai dengan sumbernya
- Workflow yang lebih jelas dan konsisten

### **✅ UX yang Lebih Baik:**
- User tahu apakah sedang input catatan sendiri atau meneruskan
- Tampilan yang berbeda untuk status yang berbeda
- Feedback yang jelas tentang sumber catatan

### **✅ Konsistensi Data:**
- Tidak ada konflik antara catatan Admin Univ dan Tim Penilai
- Tracking yang jelas tentang siapa yang memberikan catatan
- History yang akurat

### **✅ Deteksi yang Akurat:**
- Multiple ways untuk mendeteksi status penilaian
- Support untuk struktur data lama dan baru
- Fallback yang robust

## 🎯 **KESIMPULAN:**

**✅ LOGIKA SISTEM BERHASIL DIPERBAIKI!**

Sekarang sistem membedakan dengan jelas:
1. **Belum dinilai** → Admin Univ input catatan sendiri
2. **Sudah dinilai** → Admin Univ teruskan hasil penilaian Tim Penilai

**Ini membuat sistem lebih akurat dan sesuai dengan alur bisnis yang diinginkan.**

**Silakan test di halaman `http://localhost/admin-univ-usulan/usulan/16`!** 🎯

