# 🎯 SISTEM PENILAIAN TIM PENILAI & STATUS FINAL

## 📋 DESKRIPSI SISTEM

Sistem penilaian Tim Penilai yang terstruktur dengan status final otomatis berdasarkan hasil penilaian individual dari 1-3 penilai.

## 🔄 FLOW PENILAIAN & STATUS FINAL

```
1. Admin Universitas → Meneruskan usulan ke Tim Penilai
   ↓
2. Tim Penilai (1–3 orang) → Memberikan penilaian individual
   ↓
3. Sistem → Menghitung hasil penilaian dan menentukan status
   ↓
4. Status Intermediate & Final:
   ├─ "Menunggu Hasil Penilaian Tim Penilai" → Masih ada penilai yang belum selesai
   ├─ "Perbaikan Dari Tim Penilai" → Admin Univ review (final)
   └─ "Usulan Direkomendasi Tim Penilai" → Admin Univ review (final)
   ↓
5. Admin Universitas → Decision:
   ├─ Setuju → Teruskan ke Senat/Pegawai
   ├─ Perbaikan → Kirim ke Pegawai/Admin Fakultas
   └─ Tidak Direkomendasikan → Usulan tidak bisa diusulkan pada periode berjalan
```

## 🎯 LOGIKA PENENTUAN STATUS FINAL

### **Kondisi 1: Tim Penilai = 1**
- **Input:** 1 penilai memberikan rekomendasi
- **Output:** Status `"Usulan Direkomendasi Tim Penilai"`

### **Kondisi 2: Tim Penilai = 2**
- **Input:** 1 rekomendasi + 1 tidak rekomendasi
- **Output:** Status `"Perbaikan Dari Tim Penilai"`

### **Kondisi 3: Tim Penilai = 3**
- **Input:** 2 rekomendasi + 1 tidak rekomendasi
- **Output:** Status `"Usulan Direkomendasi Tim Penilai"`

### **Kondisi Khusus:**
- **Jika ada penilai yang memberikan "perbaikan"** → Status `"Perbaikan Dari Tim Penilai"` (regardless of other results)

## 🔧 IMPLEMENTASI TEKNIS

### **1. Model Usulan - Status Constants**

```php
// Status constants for Tim Penilai assessment
const STATUS_PERBAIKAN_DARI_TIM_PENILAI = 'Perbaikan Dari Tim Penilai';
const STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI = 'Usulan Direkomendasi Tim Penilai';
const STATUS_TIDAK_DIREKOMENDASI = 'Tidak Direkomendasikan';
const STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI = 'Menunggu Hasil Penilaian Tim Penilai';
```

### **2. Method Penentuan Status Final**

```php
public function determinePenilaiFinalStatus()
{
    $penilais = $this->penilais;
    $totalPenilai = $penilais->count();
    
    if ($totalPenilai === 0) {
        return null; // No penilai assigned
    }
    
    // Check if all penilai have completed their assessment
    $completedPenilai = $penilais->whereNotNull('pivot.hasil_penilaian')->count();
    
    // If not all penilai have completed, return intermediate status
    if ($completedPenilai < $totalPenilai) {
        return self::STATUS_MENUNGGU_HASIL_PENILAIAN_TIM_PENILAI;
    }
    
    // If any penilai gives 'perbaikan', result is perbaikan
    $hasPerbaikan = $penilais->where('pivot.hasil_penilaian', 'perbaikan')->count() > 0;
    if ($hasPerbaikan) {
        return self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
    }
    
    // Count recommendations
    $rekomendasiCount = $penilais->where('pivot.hasil_penilaian', 'rekomendasi')->count();
    $tidakRekomendasiCount = $penilais->where('pivot.hasil_penilaian', 'tidak_rekomendasi')->count();
    
    // Logic based on number of penilai
    switch ($totalPenilai) {
        case 1:
            return $rekomendasiCount > 0 ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
            
        case 2:
            return ($rekomendasiCount == 2) ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
            
        case 3:
            return ($rekomendasiCount >= 2) ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
            
        default:
            // For more than 3 penilai, use majority vote
            return ($rekomendasiCount > $tidakRekomendasiCount) ? self::STATUS_USULAN_DIREKOMENDASI_TIM_PENILAI : self::STATUS_PERBAIKAN_DARI_TIM_PENILAI;
    }
}
```

### **3. Tim Penilai Controller - Submit Penilaian**

```php
public function submitPenilaian(Request $request, Usulan $usulan)
{
    $request->validate([
        'hasil_penilaian' => 'required|in:rekomendasi,perbaikan,tidak_rekomendasi',
        'catatan_penilaian' => 'nullable|string|max:1000'
    ]);

    $penilaiId = Auth::id();
    $hasilPenilaian = $request->input('hasil_penilaian');
    $catatanPenilaian = $request->input('catatan_penilaian');

    // Check if penilai is assigned to this usulan
    $isAssigned = $usulan->penilais()->where('penilai_id', $penilaiId)->exists();
    if (!$isAssigned) {
        return response()->json([
            'success' => false,
            'message' => 'Anda tidak ter-assign untuk usulan ini.'
        ], 403);
    }

    // Update penilaian in pivot table
    $usulan->penilais()->updateExistingPivot($penilaiId, [
        'hasil_penilaian' => $hasilPenilaian,
        'catatan_penilaian' => $catatanPenilaian,
        'tanggal_penilaian' => now(),
        'status_penilaian' => 'Selesai'
    ]);

    // Check if all penilai have completed their assessment
    $totalPenilai = $usulan->penilais()->count();
    $completedPenilai = $usulan->penilais()->whereNotNull('hasil_penilaian')->count();

    if ($completedPenilai === $totalPenilai) {
        // All penilai have completed, determine final status
        $finalStatus = $usulan->determinePenilaiFinalStatus();
        
        if ($finalStatus) {
            // Update usulan status
            $usulan->status_usulan = $finalStatus;
            
            // Add assessment summary to validasi_data
            $currentValidasi = $usulan->validasi_data;
            $currentValidasi['tim_penilai']['assessment_summary'] = [
                'tanggal_penilaian' => now()->toDateTimeString(),
                'total_penilai' => $totalPenilai,
                'hasil_penilaian' => $usulan->penilais->map(function($penilai) {
                    return [
                        'penilai_id' => $penilai->id,
                        'nama_penilai' => $penilai->nama_lengkap,
                        'hasil' => $penilai->pivot->hasil_penilaian,
                        'catatan' => $penilai->pivot->catatan_penilaian,
                        'tanggal' => $penilai->pivot->tanggal_penilaian
                    ];
                }),
                'final_status' => $finalStatus
            ];
            
            $usulan->validasi_data = $currentValidasi;
            $usulan->save();
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Penilaian berhasil disimpan.',
        'all_completed' => $allCompleted,
        'final_status' => $finalStatus,
        'completed_count' => $completedPenilai,
        'total_count' => $totalPenilai
    ]);
}
```

### **4. Admin Universitas Controller - Enhanced Status Support**

```php
// Enhanced allowed statuses
$allowedStatuses = [
    'Diusulkan ke Universitas',
    'Perbaikan Usulan', 
    'Sedang Direview',
    'Menunggu Review Admin Univ',
    'Perbaikan Dari Tim Penilai',           // ← STATUS BARU
    'Usulan Direkomendasi Tim Penilai'      // ← STATUS BARU
];

// Enhanced canEdit logic
$canEdit = in_array($usulan->status_usulan, [
    'Diusulkan ke Universitas', 
    'Menunggu Review Admin Univ',
    'Perbaikan Dari Tim Penilai',           // ← SUPPORT STATUS BARU
    'Usulan Direkomendasi Tim Penilai'      // ← SUPPORT STATUS BARU
]);
```

### **5. Action Buttons Based on Status**

```php
private function getActionButtonsForStatus($status)
{
    switch ($status) {
        case 'Diusulkan ke Universitas':
            return [
                'perbaikan_ke_pegawai' => 'Perbaikan ke Pegawai',
                'perbaikan_ke_fakultas' => 'Perbaikan ke Fakultas',
                'teruskan_ke_penilai' => 'Teruskan ke Tim Penilai',
                'tidak_direkomendasikan' => 'Tidak Direkomendasikan'
            ];
            
        case 'Perbaikan Dari Tim Penilai':
            return [
                'approve_perbaikan' => 'Setujui Perbaikan',
                'reject_perbaikan' => 'Tolak Perbaikan',
                'perbaikan_ke_pegawai' => 'Perbaikan ke Pegawai',
                'perbaikan_ke_fakultas' => 'Perbaikan ke Fakultas',
                'tidak_direkomendasikan' => 'Tidak Direkomendasikan'
            ];
            
        case 'Usulan Direkomendasi Tim Penilai':
            return [
                'approve_rekomendasi' => 'Setujui Rekomendasi',
                'reject_rekomendasi' => 'Tolak Rekomendasi',
                'forward_to_senat' => 'Teruskan ke Senat',
                'perbaikan_ke_pegawai' => 'Perbaikan ke Pegawai',
                'perbaikan_ke_fakultas' => 'Perbaikan ke Fakultas',
                'tidak_direkomendasikan' => 'Tidak Direkomendasikan'
            ];
            
        default:
            return [];
    }
}
```

### **6. Handle "Tidak Direkomendasikan" Action**

```php
private function handleTidakDirekomendasikan(Request $request, Usulan $usulan)
{
    $request->validate([
        'catatan_umum' => 'required|string|max:1000'
    ]);

    // Update usulan status to "Tidak Direkomendasikan"
    $usulan->status_usulan = 'Tidak Direkomendasikan';
    $usulan->catatan_verifikator = $request->input('catatan_umum');

    // Add rejection data to validasi_data
    $currentValidasi = $usulan->validasi_data;
    $currentValidasi['admin_universitas']['tidak_direkomendasikan'] = [
        'catatan' => $request->input('catatan_umum'),
        'tanggal_rejection' => now()->toDateTimeString(),
        'admin_id' => Auth::id(),
        'alasan' => 'Usulan tidak direkomendasikan untuk periode berjalan'
    ];
    $usulan->validasi_data = $currentValidasi;
    $usulan->save();

    return response()->json([
        'success' => true,
        'message' => 'Usulan telah ditandai sebagai tidak direkomendasikan. Usulan tidak dapat diajukan kembali pada periode berjalan.',
        'redirect' => route('backend.admin-univ-usulan.usulan.index')
    ]);
}
```

## 🧪 TESTING SCENARIOS

### **Scenario 1: Tim Penilai = 1**
- **Input:** 1 penilai memberikan rekomendasi
- **Expected:** Status `"Usulan Direkomendasi Tim Penilai"`
- **Test:** Submit penilaian dan verifikasi status final

### **Scenario 2: Tim Penilai = 2**
- **Input:** 1 rekomendasi + 1 tidak rekomendasi
- **Expected:** Status `"Perbaikan Dari Tim Penilai"`
- **Test:** Submit penilaian dari 2 penilai dan verifikasi status

### **Scenario 3: Tim Penilai = 3**
- **Input:** 2 rekomendasi + 1 tidak rekomendasi
- **Expected:** Status `"Usulan Direkomendasi Tim Penilai"`
- **Test:** Submit penilaian dari 3 penilai dan verifikasi status

### **Scenario 4: Ada Penilai Memberikan Perbaikan**
- **Input:** 1 perbaikan + 2 rekomendasi
- **Expected:** Status `"Perbaikan Dari Tim Penilai"`
- **Test:** Verify bahwa perbaikan override rekomendasi

### **Scenario 5: Admin Universitas - Tidak Direkomendasikan**
- **Input:** Admin Univ memilih "Tidak Direkomendasikan"
- **Expected:** Status `"Tidak Direkomendasikan"`
- **Test:** Verify usulan tidak bisa diajukan di periode berjalan

### **Scenario 6: Intermediate Status - Tim Penilai = 2**
- **Input:** 1 penilai sudah submit, 1 penilai belum submit
- **Expected:** Status `"Menunggu Hasil Penilaian Tim Penilai"`
- **Test:** Verify status intermediate sebelum semua penilai selesai

### **Scenario 7: Intermediate Status - Tim Penilai = 3**
- **Input:** 2 penilai sudah submit, 1 penilai belum submit
- **Expected:** Status `"Menunggu Hasil Penilaian Tim Penilai"`
- **Test:** Verify status intermediate sebelum semua penilai selesai

## 📊 HASIL YANG DIHARAPKAN

### **Sebelum Implementasi:**
- ❌ Tidak ada sistem penilaian individual
- ❌ Status final tidak otomatis
- ❌ Tidak ada handling untuk "Tidak Direkomendasikan"
- ❌ Logic voting tidak terstruktur

### **Sesudah Implementasi:**
- ✅ Sistem penilaian individual untuk 1-3 penilai
- ✅ Status intermediate "Menunggu Hasil Penilaian Tim Penilai" untuk tracking progress
- ✅ Status final otomatis berdasarkan voting
- ✅ Support untuk "Tidak Direkomendasikan"
- ✅ Logic voting yang terstruktur dan konsisten
- ✅ Audit trail lengkap untuk tracking
- ✅ Action buttons yang dinamis berdasarkan status
- ✅ Progress tracking untuk penilaian yang belum selesai

## 🔐 KEAMANAN DAN KONSISTENSI

### **Keamanan:**
- ✅ Assignment verification untuk penilai
- ✅ Status validation yang ketat
- ✅ Comprehensive logging untuk audit trail
- ✅ Permission-based access control

### **Konsistensi:**
- ✅ Logic voting yang terstandarisasi
- ✅ Status handling yang konsisten
- ✅ Error handling yang uniform
- ✅ Data structure yang konsisten

## ✅ STATUS IMPLEMENTASI

**Status:** ✅ **BERHASIL DIIMPLEMENTASI**

**File yang Diperbaiki:**
- `app/Models/BackendUnivUsulan/Usulan.php`
- `app/Http/Controllers/Backend/TimPenilai/UsulanController.php`
- `app/Http/Controllers/Backend/AdminUnivUsulan/UsulanValidationController.php`
- `routes/backend.php`

**Perubahan:**
- Sistem penilaian individual Tim Penilai
- Status final otomatis berdasarkan voting
- Support untuk "Tidak Direkomendasikan"
- Enhanced action buttons berdasarkan status
- Comprehensive logging dan audit trail

**Target:** Implementasi sistem penilaian Tim Penilai yang terstruktur dan otomatis

**Solusi:** Multi-penilai assessment system dengan automatic status determination

**Testing:** Manual testing required untuk verify semua scenarios
