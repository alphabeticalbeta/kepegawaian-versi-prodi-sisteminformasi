# PEMBARUAN DASHBOARD TIM SENAT - TAMPILAN PERIODE USULAN

## **DESKRIPSI PERUBAHAN**

Dashboard Tim Senat telah diperbarui untuk menampilkan periode usulan dengan format yang mengikuti tampilan Penilai Universitas. Periode hanya ditampilkan jika ada kiriman dari Admin Univ Usulan.

## **FITUR YANG DITAMBAHKAN**

### **1. Tampilan Periode Usulan**
- **Kondisi Akses**: Periode hanya ditampilkan jika `canAccessPeriode('Tim Senat')` mengembalikan `true`
- **Format**: Mengikuti format Penilai Universitas dengan header periode yang informatif
- **Pengelompokan**: Usulan dikelompokkan berdasarkan periode

### **2. Tabel Usulan dengan Kolom Lengkap**

#### **Kolom yang Ditampilkan:**
1. **Nomor** - Nomor urut
2. **Nama** - Nama lengkap pegawai
3. **NIP** - Nomor Induk Pegawai
4. **Sub-sub Unit Kerja** - Unit kerja terkecil
5. **Unit Kerja** - Unit kerja utama
6. **Tujuan Jabatan** - Jabatan yang diusulkan
7. **Hasil Rekomendasi** - Status rekomendasi dari penilai
8. **Penilai** - Daftar penilai (ditampilkan sebagai "Penilai 1", "Penilai 2", dst.)
9. **Aksi** - Tombol aksi

#### **Status Rekomendasi:**
- **Direkomendasikan** - Hijau dengan ikon check
- **Perbaikan** - Kuning dengan ikon alert
- **Status Lain** - Abu-abu

### **3. Tombol Aksi**

#### **A. Rekomendasi/Tidak Rekomendasi**
- **Kondisi**: Hanya muncul jika status usulan = "Direkomendasikan"
- **Fungsi**: 
  - **Rekomendasi** → Redirect ke detail dengan parameter `action=setujui`
  - **Tidak Rekomendasi** → Redirect ke detail dengan parameter `action=tolak`
- **Modal Konfirmasi**: Menampilkan konfirmasi sebelum melakukan aksi

#### **B. Lihat Detail Pegawai**
- **Route**: `tim-senat.usulan.show`
- **View**: Menggunakan `resources/views/backend/layouts/views/shared/usulan-detail.blade.php`
- **Fungsi**: Menampilkan detail lengkap usulan

### **4. Modal Konfirmasi**
- **Desain**: Modal dengan backdrop overlay
- **Konten**: 
  - Ikon warning
  - Judul konfirmasi
  - Pesan konfirmasi
  - Tombol konfirmasi dan batal
- **Interaksi**: Dapat ditutup dengan klik di luar modal

## **PERUBAHAN TEKNIS**

### **1. Controller Updates**
**File**: `app/Http/Controllers/Backend/TimSenat/DashboardController.php`

**Perubahan**:
```php
// Menambahkan relasi yang diperlukan
$usulans = Usulan::with([
    'pegawai:id,nama_lengkap,nip,unit_kerja_id,sub_sub_unit_kerja_id',
    'pegawai.unitKerja:id,nama',
    'pegawai.subSubUnitKerja:id,nama',  // ← Ditambahkan
    'jabatanLama:id,jabatan',
    'jabatanTujuan:id,jabatan',
    'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai',
    'penilais:id,nama_lengkap,nip'      // ← Ditambahkan
])
```

### **2. Model Updates**
**File**: `app/Models/BackendUnivUsulan/Pegawai.php`

**Perubahan**:
```php
// Menambahkan relasi subSubUnitKerja
public function subSubUnitKerja()
{
    return $this->belongsTo(SubSubUnitKerja::class, 'sub_sub_unit_kerja_id');
}
```

### **3. View Updates**
**File**: `resources/views/backend/layouts/views/tim-senat/dashboard.blade.php`

**Perubahan Utama**:
- Menambahkan section "Periode Usulan"
- Implementasi tabel dengan kolom lengkap
- Menambahkan modal konfirmasi
- Menambahkan JavaScript untuk interaksi modal

## **LOGIKA AKSES PERIODE**

### **Kondisi Akses Tim Senat**
```php
// Di model Usulan
public function canAccessPeriode($role): bool
{
    if ($role === 'Tim Senat') {
        return in_array($this->status_usulan, [
            'Direkomendasikan',
            'Disetujui',
            'Ditolak',
            'Diusulkan ke Sister',
            'Perbaikan dari Tim Sister'
        ]);
    }
    // ...
}
```

### **Implementasi di View**
```php
@php
    // Filter usulan yang dapat diakses oleh Tim Senat
    $accessibleUsulans = $usulans->filter(function($usulan) {
        return $usulan->canAccessPeriode('Tim Senat');
    });
    
    // Group by periode
    $periodeGroups = $accessibleUsulans->groupBy('periode_usulan_id');
@endphp
```

## **TAMPILAN KOLOM PENILAI**

### **Implementasi**
```php
@php
    $penilais = $usulan->penilais ?? collect();
@endphp
@if($penilais->count() > 0)
    @foreach($penilais->take(3) as $index => $penilai)
        <div class="text-xs text-gray-600">
            Penilai {{ $index + 1 }}
        </div>
    @endforeach
@else
    <span class="text-gray-400">Belum ada penilai</span>
@endif
```

### **Fitur Keamanan**
- **Anonimisasi**: Nama asli penilai tidak ditampilkan
- **Format**: "Penilai 1", "Penilai 2", dst.
- **Limit**: Maksimal 3 penilai ditampilkan

## **INTERAKSI MODAL**

### **JavaScript Functions**
```javascript
function showRecommendationModal(usulanId, action) {
    // Set modal content berdasarkan action
    if (action === 'setujui') {
        title.textContent = 'Konfirmasi Rekomendasi';
        message.textContent = 'Apakah Anda yakin ingin memberikan rekomendasi untuk usulan ini?';
        confirmButton.textContent = 'Rekomendasi';
        confirmButton.className = '... bg-green-500 ...';
    } else {
        title.textContent = 'Konfirmasi Tidak Rekomendasi';
        message.textContent = 'Apakah Anda yakin ingin tidak merekomendasikan usulan ini?';
        confirmButton.textContent = 'Tidak Rekomendasi';
        confirmButton.className = '... bg-red-500 ...';
    }
    
    // Redirect ke detail page dengan parameter
    confirmButton.onclick = function() {
        window.location.href = `/tim-senat/usulan/${usulanId}?action=${action}`;
    };
}
```

## **KEUNTUNGAN IMPLEMENTASI**

### **1. User Experience**
- **Tampilan Konsisten**: Mengikuti format Penilai Universitas
- **Informasi Lengkap**: Semua data yang diperlukan ditampilkan
- **Interaksi Intuitif**: Modal konfirmasi untuk aksi penting
- **Keamanan Data**: Nama penilai tidak ditampilkan secara langsung

### **2. Performance**
- **Eager Loading**: Semua relasi dimuat sekaligus
- **Filtering**: Hanya usulan yang dapat diakses yang ditampilkan
- **Optimized Queries**: Query yang efisien dengan relasi yang tepat

### **3. Maintainability**
- **Code Reuse**: Menggunakan view shared untuk detail
- **Consistent Logic**: Menggunakan method `canAccessPeriode` yang sama
- **Modular Design**: Komponen yang dapat digunakan ulang

## **TESTING**

### **Manual Testing Checklist**
1. ✅ Login sebagai Tim Senat
2. ✅ Akses dashboard
3. ✅ Verifikasi periode ditampilkan (jika ada akses)
4. ✅ Verifikasi tabel dengan kolom lengkap
5. ✅ Test tombol "Rekomendasi"
6. ✅ Test tombol "Tidak Rekomendasi"
7. ✅ Test modal konfirmasi
8. ✅ Test tombol "Detail"
9. ✅ Verifikasi redirect ke halaman detail

### **Edge Cases**
- Periode tidak dapat diakses → Tampilkan pesan informatif
- Tidak ada penilai → Tampilkan "Belum ada penilai"
- Status bukan "Direkomendasikan" → Tombol aksi tidak muncul
- Data relasi kosong → Tampilkan "N/A"

## **KESIMPULAN**

Dashboard Tim Senat telah berhasil diperbarui dengan:

1. ✅ **Periode Display**: Menampilkan periode usulan dengan akses kontrol
2. ✅ **Complete Table**: Tabel dengan semua kolom yang diminta
3. ✅ **Action Buttons**: Tombol rekomendasi dan detail
4. ✅ **Modal Confirmation**: Konfirmasi untuk aksi penting
5. ✅ **Security**: Anonimisasi data penilai
6. ✅ **Performance**: Optimized queries dan eager loading
7. ✅ **Consistency**: Mengikuti format Penilai Universitas

Dashboard sekarang memberikan pengalaman yang lengkap dan konsisten untuk Tim Senat dalam mengelola usulan kepegawaian.
