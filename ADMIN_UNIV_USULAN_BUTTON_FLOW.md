# 🔄 ADMIN UNIV USULAN BUTTON FLOW

## ✅ **STATUS IMPLEMENTASI SAAT INI**

### **1. Button Aktif Saat Semua Penilai Submit**
**Status:** ✅ **SUDAH BERFUNGSI**

**Logic di `usulan-detail.blade.php`:**
```php
// Hitung penilai yang sudah submit review
$penilaisWithReview = 0;
$totalPenilais = count($usulan->penilais);

// Cek setiap penilai
foreach ($usulan->penilais as $penilai) {
    $penilaiId = $penilai->id;
    $penilaiValidation = $usulan->validasi_data['tim_penilai'] ?? [];
    
    // Cek struktur baru (reviews)
    if (isset($penilaiValidation['reviews'][$penilaiId])) {
        $penilaisWithReview++;
    }
    // Fallback ke struktur lama
    elseif (isset($penilaiValidation['validated_by']) && $penilaiValidation['validated_by'] == $penilaiId) {
        $penilaisWithReview++;
    }
}

// Button aktif jika semua penilai sudah submit
$allPenilaisSubmitted = $totalPenilais === 0 || $penilaisWithReview === $totalPenilais;
```

**Tampilan:**
```blade
@if($allPenilaisSubmitted)
    {{-- Button aktif --}}
    <div class="flex gap-2">
        <button id="btn-perbaikan-pegawai">Perbaikan ke Pegawai</button>
        <button id="btn-perbaikan-fakultas">Perbaikan ke Fakultas</button>
        <button id="btn-teruskan-penilai">Teruskan ke Penilai</button>
    </div>
@else
    {{-- Menunggu penilai --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <span>Menunggu semua penilai menyelesaikan review ({{ $penilaisWithReview }}/{{ $totalPenilais }})</span>
    </div>
@endif
```

### **2. Button Admin Univ Usulan untuk Kirim Hasil**

#### **A. Perbaikan ke Pegawai**
**Button ID:** `btn-perbaikan-pegawai`
**Action:** `return_to_pegawai`
**Controller:** `UsulanValidationController::returnToPegawai()`

**Flow:**
1. Admin Univ Usulan klik button
2. Modal SweetAlert muncul untuk input catatan
3. Submit ke controller dengan `action_type = 'return_to_pegawai'`
4. Status usulan berubah ke `'Perbaikan Usulan'`
5. Catatan disimpan di `catatan_verifikator`
6. Usulan dikirim ke pegawai untuk perbaikan

**JavaScript:**
```javascript
function showPerbaikanKePegawaiModal() {
    Swal.fire({
        title: 'Perbaikan ke Pegawai',
        text: 'Usulan akan dikembalikan ke pegawai untuk perbaikan.',
        input: 'textarea',
        inputPlaceholder: 'Masukkan catatan perbaikan untuk pegawai...',
        confirmButtonText: 'Kembalikan ke Pegawai',
        confirmButtonColor: '#dc2626',
        preConfirm: (catatan) => {
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                return false;
            }
            return catatan;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_pegawai', result.value);
        }
    });
}
```

#### **B. Perbaikan ke Fakultas**
**Button ID:** `btn-perbaikan-fakultas`
**Action:** `return_to_fakultas`
**Controller:** `UsulanValidationController::returnToFakultas()`

**Flow:**
1. Admin Univ Usulan klik button
2. Modal SweetAlert muncul untuk input catatan
3. Submit ke controller dengan `action_type = 'return_to_fakultas'`
4. Status usulan berubah ke `'Perbaikan Usulan'`
5. Catatan disimpan di `catatan_verifikator`
6. Usulan dikirim ke fakultas untuk perbaikan

**JavaScript:**
```javascript
function showPerbaikanKeFakultasModal() {
    Swal.fire({
        title: 'Perbaikan ke Fakultas',
        text: 'Usulan akan dikembalikan ke fakultas untuk perbaikan.',
        input: 'textarea',
        inputPlaceholder: 'Masukkan catatan perbaikan untuk fakultas...',
        confirmButtonText: 'Kembalikan ke Fakultas',
        confirmButtonColor: '#d97706',
        preConfirm: (catatan) => {
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                return false;
            }
            return catatan;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('return_to_fakultas', result.value);
        }
    });
}
```

#### **C. Teruskan ke Penilai**
**Button ID:** `btn-teruskan-penilai`
**Action:** `forward_to_penilai`
**Controller:** `UsulanValidationController::forwardToPenilai()`

**Flow:**
1. Admin Univ Usulan klik button
2. Modal SweetAlert muncul dengan pilihan penilai
3. Admin pilih penilai yang akan ditugaskan
4. Submit ke controller dengan `action_type = 'forward_to_penilai'`
5. Status usulan berubah ke `'Sedang Direview'`
6. Penilai ditugaskan ke usulan
7. Usulan dikirim ke tim penilai untuk review

**JavaScript:**
```javascript
function showTeruskanKePenilaiModal() {
    // Get penilais data from the page
    const penilais = @json($penilais ?? []);

    // Create HTML for penilai selection
    let penilaiHtml = '<div class="mb-4">';
    penilaiHtml += '<label class="block text-sm font-medium text-gray-700 mb-2">Pilih Penilai (minimal 1):</label>';
    penilaiHtml += '<div class="space-y-2 max-h-40 overflow-y-auto">';

    penilais.forEach(penilai => {
        penilaiHtml += `
            <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                <input type="checkbox" name="selected_penilais[]" value="${penilai.id}" class="penilai-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">
                    <strong>${penilai.nama_lengkap}</strong>
                    ${penilai.bidang_keahlian ? `<br><span class="text-xs text-gray-500">${penilai.bidang_keahlian}</span>` : ''}
                </span>
            </label>
        `;
    });

    penilaiHtml += '</div></div>';
    penilaiHtml += '<div class="mb-4">';
    penilaiHtml += '<label class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional):</label>';
    penilaiHtml += '<textarea id="catatan-penilai" placeholder="Catatan untuk tim penilai..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>';
    penilaiHtml += '</div>';

    Swal.fire({
        title: 'Teruskan ke Tim Penilai',
        html: penilaiHtml,
        showCancelButton: true,
        confirmButtonText: 'Teruskan ke Penilai',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        width: '500px',
        preConfirm: () => {
            // Check if at least one penilai is selected
            const selectedPenilais = document.querySelectorAll('input[name="selected_penilais[]"]:checked');
            if (selectedPenilais.length === 0) {
                Swal.showValidationMessage('⚠️ Pilih minimal 1 penilai terlebih dahulu!');
                return false;
            }

            const penilaiIds = Array.from(selectedPenilais).map(cb => cb.value);
            const catatan = document.getElementById('catatan-penilai').value;

            return {
                selected_penilais: penilaiIds,
                catatan: catatan
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('forward_to_penilai', result.value);
        }
    });
}
```

## 🔄 **ALUR KERJA LENGKAP**

### **1. Usulan Masuk ke Admin Univ Usulan**
```
Pegawai → Admin Fakultas → Admin Univ Usulan
Status: "Diusulkan ke Universitas"
```

### **2. Admin Univ Usulan Review**
```
Admin Univ Usulan dapat:
├── Kirim ke Tim Penilai (forward_to_penilai)
├── Kembalikan ke Fakultas (return_to_fakultas)
└── Kembalikan ke Pegawai (return_to_pegawai)
```

### **3. Tim Penilai Review**
```
Tim Penilai dapat:
├── Perbaikan Usulan (perbaikan_usulan)
└── Rekomendasikan (rekomendasikan)
Status: "Menunggu Review Admin Univ"
```

### **4. Admin Univ Usulan Review Hasil Penilai**
```
Admin Univ Usulan dapat:
├── Setujui Perbaikan (approve_perbaikan)
├── Tolak Perbaikan (reject_perbaikan)
├── Setujui Rekomendasi (approve_rekomendasi)
├── Tolak Rekomendasi (reject_rekomendasi)
├── Perbaikan ke Pegawai (return_to_pegawai)
├── Perbaikan ke Fakultas (return_to_fakultas)
└── Teruskan ke Penilai (forward_to_penilai)
```

## 📊 **STATUS BUTTON BERDASARKAN KONDISI**

### **Button Aktif Saat Semua Penilai Submit:**
- ✅ **Setujui Perbaikan** - Jika ada perbaikan dari penilai
- ✅ **Tolak Perbaikan** - Jika ada perbaikan dari penilai
- ✅ **Setujui Rekomendasi** - Jika ada rekomendasi dari penilai
- ✅ **Tolak Rekomendasi** - Jika ada rekomendasi dari penilai
- ✅ **Perbaikan ke Pegawai** - Selalu aktif
- ✅ **Perbaikan ke Fakultas** - Selalu aktif
- ✅ **Teruskan ke Penilai** - Selalu aktif

### **Button Non-Aktif Saat Belum Semua Penilai Submit:**
- ❌ Semua button di atas
- ⏳ **Menunggu Status** - Tampil pesan "Menunggu semua penilai menyelesaikan review (X/Y)"

## 🎯 **KESIMPULAN**

**✅ SEMUA FITUR SUDAH BERFUNGSI:**

1. **Button aktif saat semua penilai submit** - ✅ Implemented
2. **Admin Univ Usulan kirim ke pegawai** - ✅ Implemented  
3. **Admin Univ Usulan kirim ke fakultas** - ✅ Implemented
4. **Admin Univ Usulan teruskan ke penilai** - ✅ Implemented

**Tidak ada yang perlu diperbaiki - semua sudah berfungsi sesuai permintaan!** 🎉
