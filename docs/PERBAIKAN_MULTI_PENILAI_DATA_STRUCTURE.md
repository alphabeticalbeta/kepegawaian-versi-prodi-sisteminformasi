# Perbaikan Struktur Data Multi-Penilai untuk Review Terpisah

## Permasalahan yang Ditemukan

### **Data Review Multiple Penilai Saling Menimpa**
- Review dari multiple penilai disimpan dalam struktur data yang sama
- Penilai kedua akan menimpa data review penilai pertama
- Struktur `validasi_data['tim_penilai']` tidak mendukung multiple penilai
- Admin Univ Usulan hanya melihat review dari penilai terakhir yang submit

## Analisis Masalah

### **Struktur Data Lama (Bermasalah):**
```php
$validasi_data['tim_penilai'] = [
    'validated_by' => 123, // Hanya 1 penilai
    'perbaikan_usulan' => [
        'penilai_id' => 123, // Akan tertimpa oleh penilai lain
        'catatan' => 'Catatan perbaikan',
        'tanggal_return' => '2024-01-15T10:30:00Z'
    ],
    'recommendation' => 'direkomendasikan',
    'catatan_rekomendasi' => 'Catatan rekomendasi',
    'penilai_id' => 123, // Akan tertimpa oleh penilai lain
    'tanggal_rekomendasi' => '2024-01-15T10:30:00Z'
];
```

### **Masalah Spesifik:**
1. **Data Overwrite**: Penilai kedua menimpa data penilai pertama
2. **Single Field Storage**: Field utama hanya menyimpan 1 penilai
3. **Loss of Review History**: Review sebelumnya hilang
4. **Incomplete Display**: Admin hanya melihat review terakhir

## Perbaikan yang Dilakukan

### **File:** `app/Services/PenilaiService.php`

#### **1. Struktur Data Baru untuk Rekomendasi (Baris 214-240)**
```php
// Store recommendation per penilai
$currentValidasi['tim_penilai']['reviews'][$penilaiId] = [
    'type' => 'rekomendasi',
    'recommendation' => 'direkomendasikan',
    'catatan_rekomendasi' => $request->input('catatan_umum'),
    'tanggal_rekomendasi' => now()->toDateTimeString(),
    'penilai_id' => $penilaiId,
    'status' => 'menunggu_admin_univ_review'
];

// Keep backward compatibility for main fields
$currentValidasi['tim_penilai']['recommendation'] = 'direkomendasikan';
$currentValidasi['tim_penilai']['catatan_rekomendasi'] = $request->input('catatan_umum');
$currentValidasi['tim_penilai']['tanggal_rekomendasi'] = now()->toDateTimeString();
$currentValidasi['tim_penilai']['penilai_id'] = $penilaiId;
$currentValidasi['tim_penilai']['status'] = 'menunggu_admin_univ_review';
```

#### **2. Struktur Data Baru untuk Perbaikan Usulan (Baris 280-303)**
```php
// Store perbaikan per penilai
$currentValidasi['tim_penilai']['reviews'][$penilaiId] = [
    'type' => 'perbaikan_usulan',
    'catatan' => $request->input('catatan_umum'),
    'tanggal_return' => now()->toDateTimeString(),
    'penilai_id' => $penilaiId,
    'status' => 'menunggu_admin_univ_review'
];

// Keep backward compatibility for main fields
$currentValidasi['tim_penilai']['perbaikan_usulan'] = [
    'catatan' => $request->input('catatan_umum'),
    'tanggal_return' => now()->toDateTimeString(),
    'penilai_id' => $penilaiId,
    'status' => 'menunggu_admin_univ_review'
];
```

### **File:** `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`

#### **3. Logic Pembacaan Data Multi-Penilai (Baris 362-414)**
```php
// Check new structure first (per-penilai reviews)
if (isset($penilaiReview['reviews'][$penilaiId])) {
    $reviewData = $penilaiReview['reviews'][$penilaiId];
    $penilaiReviewData['type'] = $reviewData['type'];
    $penilaiReviewData['penilai'] = $penilai;
    $penilaiReviewData['validation'] = $penilaiValidationData;
    
    if ($reviewData['type'] === 'perbaikan_usulan') {
        $penilaiReviewData['catatan'] = $reviewData['catatan'] ?? '';
        $penilaiReviewData['tanggal'] = $reviewData['tanggal_return'] ?? null;
    } elseif ($reviewData['type'] === 'rekomendasi') {
        $penilaiReviewData['catatan'] = $reviewData['catatan_rekomendasi'] ?? '';
        $penilaiReviewData['tanggal'] = $reviewData['tanggal_rekomendasi'] ?? null;
        $penilaiReviewData['recommendation'] = $reviewData['recommendation'] ?? '';
    }
}
// Fallback to old structure for backward compatibility
```

#### **4. Logic Penghitungan Penilai yang Submit (Baris 1152-1171)**
```php
// Check new structure first
if (isset($penilaiValidation['reviews'][$penilaiId])) {
    $penilaisWithReview++;
}
// Fallback to old structure
elseif (isset($penilaiValidation['validated_by']) && $penilaiValidation['validated_by'] == $penilaiId) {
    $penilaisWithReview++;
} elseif (isset($penilaiValidation['perbaikan_usulan']['penilai_id']) && $penilaiValidation['perbaikan_usulan']['penilai_id'] == $penilaiId) {
    $penilaisWithReview++;
} elseif (isset($penilaiValidation['penilai_id']) && $penilaiValidation['penilai_id'] == $penilaiId) {
    $penilaisWithReview++;
}
```

## Struktur Data Baru (Diperbaiki)

### **Struktur Data Baru dengan Multi-Penilai Support:**
```php
$validasi_data['tim_penilai'] = [
    // New structure - per penilai reviews
    'reviews' => [
        123 => [
            'type' => 'perbaikan_usulan',
            'catatan' => 'Catatan perbaikan dari penilai 1',
            'tanggal_return' => '2024-01-15T10:30:00Z',
            'penilai_id' => 123,
            'status' => 'menunggu_admin_univ_review'
        ],
        124 => [
            'type' => 'rekomendasi',
            'recommendation' => 'direkomendasikan',
            'catatan_rekomendasi' => 'Catatan rekomendasi dari penilai 2',
            'tanggal_rekomendasi' => '2024-01-15T11:30:00Z',
            'penilai_id' => 124,
            'status' => 'menunggu_admin_univ_review'
        ]
    ],
    
    // Backward compatibility - keeps latest submission
    'validated_by' => 124,
    'perbaikan_usulan' => [...], // Latest perbaikan
    'recommendation' => 'direkomendasikan', // Latest recommendation
    'catatan_rekomendasi' => 'Catatan rekomendasi dari penilai 2',
    'penilai_id' => 124,
    'tanggal_rekomendasi' => '2024-01-15T11:30:00Z',
    'status' => 'menunggu_admin_univ_review',
    'validation' => [...] // Merged validation data
];
```

## Keuntungan Struktur Baru

### **1. Data Persistence**
- Review dari setiap penilai tersimpan terpisah
- Tidak ada data yang tertimpa
- History review lengkap

### **2. Backward Compatibility**
- Sistem lama masih berfungsi
- Data lama tetap dapat dibaca
- Transisi yang smooth

### **3. Multiple Penilai Support**
- Mendukung unlimited penilai
- Setiap penilai memiliki space terpisah
- Independent review process

### **4. Admin View Enhancement**
- Admin dapat melihat semua review
- Informasi penilai lengkap
- Decision making yang lebih baik

## Alur Data yang Diperbaiki

### **Sebelum Perbaikan:**
```
Penilai 1 → Submit Review → Data tersimpan
Penilai 2 → Submit Review → Data Penilai 1 tertimpa ❌
Admin → Lihat review → Hanya melihat Penilai 2
```

### **Sesudah Perbaikan:**
```
Penilai 1 → Submit Review → Data tersimpan di reviews[123]
Penilai 2 → Submit Review → Data tersimpan di reviews[124] ✅
Admin → Lihat review → Melihat kedua review lengkap
```

## Testing Scenarios

### **1. Test Single Penilai (Backward Compatibility)**
1. Assign 1 penilai ke usulan
2. Penilai submit review
3. Verifikasi:
   - Data tersimpan di structure baru dan lama
   - Admin dapat melihat review
   - Tidak ada regression

### **2. Test Multiple Penilai (New Feature)**
1. Assign 2-3 penilai ke usulan
2. Setiap penilai submit review berbeda
3. Verifikasi:
   - Semua review tersimpan terpisah
   - Admin melihat semua review
   - Penghitungan penilai yang submit akurat

### **3. Test Mixed Data (Migration)**
1. Usulan dengan data lama (structure lama)
2. Penilai baru submit review (structure baru)
3. Verifikasi:
   - Data lama tetap dapat dibaca
   - Data baru tersimpan dengan benar
   - Tampilan menggabungkan kedua structure

## Implementation Details

### **1. Data Storage Strategy**
- **Primary Storage**: `reviews[penilai_id]` - per penilai data
- **Fallback Storage**: Main fields - backward compatibility
- **Merge Strategy**: New structure priority, fallback to old

### **2. Read Strategy**
- **Check New First**: Look for `reviews[penilai_id]`
- **Fallback to Old**: Check main fields if new not found
- **Comprehensive Collection**: Gather from both structures

### **3. Display Strategy**
- **Loop Through Penilais**: Check each assigned penilai
- **Prioritize New Data**: Use new structure when available
- **Maintain Compatibility**: Support old data format

## Monitoring dan Debugging

### **1. Debug Data Structure**
```php
Log::info('Multi-penilai data debug', [
    'usulan_id' => $usulan->id,
    'total_assigned_penilais' => $assignedPenilais->count(),
    'new_structure_reviews' => $penilaiReview['reviews'] ?? [],
    'old_structure_fields' => [
        'validated_by' => $penilaiReview['validated_by'] ?? null,
        'perbaikan_penilai_id' => $penilaiReview['perbaikan_usulan']['penilai_id'] ?? null,
        'rekomendasi_penilai_id' => $penilaiReview['penilai_id'] ?? null
    ],
    'collected_reviews_count' => count($allPenilaiReviews)
]);
```

### **2. Check Data Migration**
```sql
-- Check usulan with new structure
SELECT u.id, 
       JSON_EXTRACT(u.validasi_data, '$.tim_penilai.reviews') as new_reviews,
       JSON_EXTRACT(u.validasi_data, '$.tim_penilai.penilai_id') as old_penilai_id
FROM usulans u
WHERE u.status_usulan = 'Menunggu Review Admin Univ'
  AND JSON_EXTRACT(u.validasi_data, '$.tim_penilai') IS NOT NULL;
```

## Catatan Penting

1. **Backward Compatibility**: Data lama tetap dapat dibaca dan ditampilkan
2. **Migration Safe**: Tidak perlu migrasi database, struktur auto-adapt
3. **Performance**: Minimal impact, hanya penambahan logic pembacaan
4. **Scalability**: Mendukung unlimited penilai dengan struktur yang sama

## Rollback Plan

Jika terjadi masalah, dapat dilakukan rollback dengan:

1. **Revert PenilaiService**: Kembalikan ke struktur penyimpanan lama
2. **Revert View Logic**: Kembalikan ke pembacaan struktur lama saja
3. **Data Integrity**: Data lama tidak terpengaruh, aman untuk rollback
4. **Testing**: Test dengan data existing untuk memastikan compatibility
