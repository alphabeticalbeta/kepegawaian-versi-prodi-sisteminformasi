# Perbaikan Status Display untuk Tim Penilai Universitas

## Permasalahan yang Ditemukan

### **Status Tidak Sesuai untuk Penilai yang Belum Memberikan Penilaian**
- Pada halaman detail usulan Tim Penilai Universitas dengan status "Menunggu Review Admin Univ"
- Jika penilai belum memberikan penilaian, status tetap menampilkan "Menunggu Review Admin Univ"
- Seharusnya menampilkan "Menunggu Penilaian Tim Penilai Universitas" untuk penilai yang belum submit
- Status badge dan status message tidak mencerminkan kondisi sebenarnya

## Analisis Masalah

### **Status Display Statis**
- Status badge dan message menggunakan nilai dari database secara langsung
- Tidak memperhitungkan kondisi penilai saat ini
- Tidak membedakan antara penilai yang sudah dan belum memberikan review

### **User Experience Issue**
- Penilai yang belum submit merasa bingung dengan status "Menunggu Review Admin Univ"
- Status tidak memberikan informasi yang actionable
- Tidak ada indikasi bahwa penilai masih harus memberikan penilaian

## Perbaikan yang Dilakukan

### **File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

#### **1. Perbaikan Status Badge (Baris 308-349)**

**Logika Pengecekan Penilai:**
```php
// Check if current penilai has submitted review (for Tim Penilai role)
$currentPenilaiHasReviewed = false;
$displayStatus = $usulan->status_usulan;

if ($currentRole === 'Tim Penilai' && $usulan->status_usulan === 'Menunggu Review Admin Univ') {
    $currentPenilaiId = auth()->id();
    $penilaiValidation = $usulan->validasi_data['tim_penilai'] ?? [];
    
    // Check if current penilai has submitted review
    if (isset($penilaiValidation['validated_by']) && $penilaiValidation['validated_by'] == $currentPenilaiId) {
        $currentPenilaiHasReviewed = true;
    } elseif (isset($penilaiValidation['perbaikan_usulan']['penilai_id']) && $penilaiValidation['perbaikan_usulan']['penilai_id'] == $currentPenilaiId) {
        $currentPenilaiHasReviewed = true;
    } elseif (isset($penilaiValidation['penilai_id']) && $penilaiValidation['penilai_id'] == $currentPenilaiId) {
        $currentPenilaiHasReviewed = true;
    }
    
    // If current penilai hasn't reviewed, show as waiting for penilai
    if (!$currentPenilaiHasReviewed) {
        $displayStatus = 'Menunggu Penilaian Tim Penilai';
    }
}
```

**Status Colors Update:**
```php
$statusColors = [
    'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
    'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
    'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
    'Menunggu Penilaian Tim Penilai' => 'bg-yellow-100 text-yellow-800 border-yellow-300', // NEW
    'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
    'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
    'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
    'Diusulkan ke Universitas' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
    'Menunggu Review Admin Univ' => 'bg-purple-100 text-purple-800 border-purple-300',
];
```

#### **2. Perbaikan Status Message (Baris 1259-1265)**

**Dynamic Message:**
```php
'Menunggu Review Admin Univ' => [
    'icon' => $currentRole === 'Tim Penilai' && !$currentPenilaiHasReviewed ? 'clock' : 'eye',
    'color' => $currentRole === 'Tim Penilai' && !$currentPenilaiHasReviewed ? 'text-yellow-600' : 'text-purple-600',
    'message' => $currentRole === 'Tim Penilai' && !$currentPenilaiHasReviewed 
        ? 'Menunggu penilaian Tim Penilai Universitas.'
        : 'Usulan menunggu review dari Admin Universitas.'
],
```

## Logika Perbaikan

### **1. Deteksi Status Penilai**
- Mengecek apakah penilai saat ini sudah memberikan review
- Memeriksa tiga kondisi: `validated_by`, `perbaikan_usulan.penilai_id`, `penilai_id`
- Menggunakan `auth()->id()` untuk mendapatkan ID penilai saat ini

### **2. Dynamic Status Display**
- **Penilai belum submit**: Status "Menunggu Penilaian Tim Penilai" (kuning)
- **Penilai sudah submit**: Status "Menunggu Review Admin Univ" (ungu)
- **Role lain**: Tetap menampilkan status normal

### **3. Visual Indicators**
- **Icon**: Clock (⏰) untuk penilai yang belum submit, Eye (👁) untuk yang sudah
- **Color**: Yellow untuk menunggu penilaian, Purple untuk menunggu review admin
- **Message**: Berbeda tergantung kondisi penilai

## Alur Status yang Diperbaiki

### **Sebelum Perbaikan:**
```
Tim Penilai (belum submit) → Status: "Menunggu Review Admin Univ" (ungu)
Tim Penilai (sudah submit) → Status: "Menunggu Review Admin Univ" (ungu)
```

### **Sesudah Perbaikan:**
```
Tim Penilai (belum submit) → Status: "Menunggu Penilaian Tim Penilai" (kuning)
Tim Penilai (sudah submit) → Status: "Menunggu Review Admin Univ" (ungu)
```

## Kondisi Pengecekan

### **Penilai Dianggap Sudah Submit Jika:**
1. **General Review**: `validated_by == current_penilai_id`
2. **Perbaikan Usulan**: `perbaikan_usulan.penilai_id == current_penilai_id`
3. **Rekomendasi**: `penilai_id == current_penilai_id`

### **Data Structure Reference:**
```php
$validasi_data['tim_penilai'] = [
    'validated_by' => 123, // ID penilai yang submit review umum
    'perbaikan_usulan' => [
        'penilai_id' => 123, // ID penilai yang submit perbaikan
        'catatan' => '...',
        'tanggal_return' => '...'
    ],
    'penilai_id' => 123, // ID penilai yang submit rekomendasi
    'recommendation' => 'direkomendasikan',
    'catatan_rekomendasi' => '...'
];
```

## Testing Scenarios

### **1. Test Penilai Belum Submit**
1. Login sebagai Tim Penilai yang belum memberikan review
2. Buka detail usulan dengan status "Menunggu Review Admin Univ"
3. Verifikasi:
   - Status badge: "Menunggu Penilaian Tim Penilai" (kuning)
   - Status message: "Menunggu penilaian Tim Penilai Universitas" (kuning, clock icon)
   - Button review tersedia

### **2. Test Penilai Sudah Submit**
1. Login sebagai Tim Penilai yang sudah submit review
2. Buka detail usulan dengan status "Menunggu Review Admin Univ"
3. Verifikasi:
   - Status badge: "Menunggu Review Admin Univ" (ungu)
   - Status message: "Usulan menunggu review dari Admin Universitas" (ungu, eye icon)

### **3. Test Role Lain**
1. Login sebagai Admin Universitas/Admin Fakultas
2. Buka detail usulan dengan status "Menunggu Review Admin Univ"
3. Verifikasi:
   - Status tetap menampilkan "Menunggu Review Admin Univ" (ungu)
   - Tidak terpengaruh oleh logika penilai

## Keuntungan Perbaikan

### **1. User Experience**
- Status yang lebih informatif dan actionable
- Penilai tahu bahwa mereka masih harus memberikan penilaian
- Tidak ada kebingungan tentang status usulan

### **2. Visual Clarity**
- Warna dan icon yang sesuai dengan kondisi
- Konsistensi visual antara status badge dan message
- Diferensiasi yang jelas antara penilai yang sudah dan belum submit

### **3. System Accuracy**
- Status mencerminkan kondisi sebenarnya
- Role-specific display yang akurat
- Tidak mempengaruhi logic role lain

## Monitoring dan Debugging

### **1. Debug Status Display**
```javascript
// Debug status for penilai
console.log('Current role:', '{{ $currentRole }}');
console.log('Usulan status:', '{{ $usulan->status_usulan }}');
console.log('Current penilai ID:', {{ auth()->id() }});
console.log('Display status:', '{{ $displayStatus }}');
console.log('Penilai has reviewed:', {{ $currentPenilaiHasReviewed ? 'true' : 'false' }});
```

### **2. Check Validation Data**
```php
Log::info('Status display check', [
    'usulan_id' => $usulan->id,
    'current_role' => $currentRole,
    'current_penilai_id' => auth()->id(),
    'usulan_status' => $usulan->status_usulan,
    'penilai_validation' => $usulan->validasi_data['tim_penilai'] ?? null,
    'display_status' => $displayStatus,
    'has_reviewed' => $currentPenilaiHasReviewed
]);
```

## Catatan Penting

1. **Role Specific**: Perbaikan hanya berlaku untuk role 'Tim Penilai'
2. **Status Specific**: Hanya berlaku untuk status 'Menunggu Review Admin Univ'
3. **Non-Destructive**: Tidak mengubah status database, hanya tampilan
4. **Backward Compatible**: Tidak mempengaruhi role atau status lain

## Rollback Plan

Jika terjadi masalah, dapat dilakukan rollback dengan:

1. **Kembalikan status badge** ke logic sebelumnya (static status)
2. **Kembalikan status message** ke logic sebelumnya (static message)
3. **Test semua role** untuk memastikan tidak ada regression
4. **Monitor logs** untuk memastikan tidak ada error
