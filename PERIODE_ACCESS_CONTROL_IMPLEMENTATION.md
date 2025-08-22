# IMPLEMENTASI ATURAN AKSES PERIODE

## **DESKRIPSI ATURAN**

Periode hanya dapat dilihat oleh **Tim Senat** dan **Penilai Universitas** apabila **Admin Univ Usulan** sudah mengirimkan usulan ke masing-masing role tersebut.

## **IMPLEMENTASI YANG TELAH DIBUAT**

### **1. MODEL USULAN - ATURAN AKSES**

#### **A. Method `canAccessPeriode($role)`**
**Lokasi**: `app/Models/BackendUnivUsulan/Usulan.php`

**Logika Akses:**
- **Admin Univ Usulan**: Selalu dapat mengakses periode
- **Tim Senat**: Dapat mengakses periode jika status usulan:
  - `Direkomendasikan`
  - `Disetujui`
  - `Ditolak`
  - `Diusulkan ke Sister`
  - `Perbaikan dari Tim Sister`
- **Penilai Universitas**: Dapat mengakses periode jika status usulan:
  - `Sedang Direview`
  - `Direkomendasikan`
  - `Perbaikan Usulan`
  - `Sedang Dinilai`
- **Tim Penilai**: Dapat mengakses periode jika status usulan:
  - `Sedang Direview`
  - `Direkomendasikan`
  - `Perbaikan Usulan`
  - `Sedang Dinilai`
- **Role Lain**: Tidak dapat mengakses periode

#### **B. Method `getPeriodeInfo($role)`**
**Fungsi**: Mengembalikan informasi periode dengan kontrol akses

**Return Value:**
```php
// Jika dapat diakses
[
    'nama_periode' => 'Nama Periode',
    'tanggal_mulai' => '2024-01-01',
    'tanggal_selesai' => '2024-01-31',
    'status' => 'accessible',
    'message' => null
]

// Jika tidak dapat diakses
[
    'nama_periode' => 'Tidak dapat diakses',
    'tanggal_mulai' => null,
    'tanggal_selesai' => null,
    'status' => 'restricted',
    'message' => 'Periode hanya dapat diakses oleh Tim Senat dan Penilai Universitas setelah usulan dikirim'
]
```

### **2. VIEW YANG TELAH DIPERBARUI**

#### **A. Tim Senat Views**
1. **Dashboard** (`dashboard.blade.php`)
   - Menampilkan periode hanya jika dapat diakses
   - Informasi periode ditampilkan dengan warna abu-abu

2. **Index Usulan** (`usulan/index.blade.php`)
   - Kolom periode menampilkan informasi sesuai akses
   - Jika tidak dapat diakses, menampilkan pesan "Tidak dapat diakses"

3. **Keputusan Senat** (`keputusan-senat/index.blade.php`)
   - Menampilkan periode hanya untuk usulan yang dapat diakses

#### **B. Tim Penilai Views**
1. **Index Usulan** (`usulan/index.blade.php`)
   - Kolom periode menampilkan informasi sesuai akses
   - Jika tidak dapat diakses, menampilkan pesan "Tidak dapat diakses"

#### **C. Penilai Universitas Views**
1. **Show Pendaftar** (`pusat-usulan/show-pendaftar.blade.php`)
   - Header menampilkan informasi periode hanya jika dapat diakses
   - Jika tidak dapat diakses, menampilkan pesan "Periode Tidak Dapat Diakses"

### **3. CARA PENGGUNAAN DI VIEW**

#### **A. Contoh Penggunaan Dasar**
```php
@php
    $periodeInfo = $usulan->getPeriodeInfo('Tim Senat');
@endphp

@if($periodeInfo['status'] === 'accessible')
    <div class="text-sm text-gray-900">{{ $periodeInfo['nama_periode'] }}</div>
    <div class="text-xs text-gray-500">
        {{ $periodeInfo['tanggal_mulai'] ? \Carbon\Carbon::parse($periodeInfo['tanggal_mulai'])->format('d/m/Y') : 'N/A' }} - 
        {{ $periodeInfo['tanggal_selesai'] ? \Carbon\Carbon::parse($periodeInfo['tanggal_selesai'])->format('d/m/Y') : 'N/A' }}
    </div>
@else
    <div class="text-sm text-gray-400">{{ $periodeInfo['nama_periode'] }}</div>
    <div class="text-xs text-gray-300">{{ $periodeInfo['message'] }}</div>
@endif
```

#### **B. Contoh Penggunaan di Controller**
```php
// Di controller
public function index()
{
    $usulans = Usulan::with(['pegawai', 'periodeUsulan'])->get();
    
    return view('usulan.index', compact('usulans'));
}
```

### **4. WORKFLOW AKSES PERIODE**

#### **A. Flow untuk Tim Senat**
1. **Usulan Baru**: Periode tidak dapat diakses
2. **Usulan Direkomendasikan**: Periode dapat diakses
3. **Usulan Disetujui/Ditolak**: Periode tetap dapat diakses
4. **Usulan ke Sister**: Periode tetap dapat diakses

#### **B. Flow untuk Penilai Universitas**
1. **Usulan Baru**: Periode tidak dapat diakses
2. **Usulan Sedang Direview**: Periode dapat diakses
3. **Usulan Direkomendasikan**: Periode tetap dapat diakses
4. **Usulan Perbaikan**: Periode tetap dapat diakses

#### **C. Flow untuk Tim Penilai**
1. **Usulan Baru**: Periode tidak dapat diakses
2. **Usulan Sedang Direview**: Periode dapat diakses
3. **Usulan Direkomendasikan**: Periode tetap dapat diakses
4. **Usulan Perbaikan**: Periode tetap dapat diakses

### **5. KEUNTUNGAN IMPLEMENTASI**

#### **A. Keamanan**
- Mencegah akses tidak sah ke informasi periode
- Kontrol akses berbasis status usulan
- Pesan yang jelas untuk akses yang ditolak

#### **B. User Experience**
- Informasi periode ditampilkan dengan jelas
- Pesan error yang informatif
- Konsistensi tampilan di semua view

#### **C. Maintainability**
- Logika akses terpusat di model
- Mudah untuk menambah role baru
- Mudah untuk mengubah aturan akses

### **6. TESTING**

#### **A. Test Cases yang Perlu Dibuat**
1. **Tim Senat Access Test**
   - Test akses periode untuk status "Direkomendasikan"
   - Test akses periode untuk status "Draft"
   - Test akses periode untuk status "Disetujui"

2. **Penilai Universitas Access Test**
   - Test akses periode untuk status "Sedang Direview"
   - Test akses periode untuk status "Diajukan"
   - Test akses periode untuk status "Direkomendasikan"

3. **Tim Penilai Access Test**
   - Test akses periode untuk status "Sedang Direview"
   - Test akses periode untuk status "Diajukan"
   - Test akses periode untuk status "Direkomendasikan"

#### **B. Manual Testing**
1. Login sebagai Tim Senat
2. Cek usulan dengan status "Direkomendasikan"
3. Verifikasi periode dapat diakses
4. Cek usulan dengan status "Draft"
5. Verifikasi periode tidak dapat diakses

### **7. PENGEMBANGAN SELANJUTNYA**

#### **A. Fitur yang Dapat Ditambahkan**
1. **Audit Log**: Mencatat siapa yang mengakses periode
2. **Cache**: Menyimpan hasil pengecekan akses
3. **Notification**: Memberitahu admin ketika periode diakses

#### **B. Optimisasi**
1. **Database Index**: Index pada kolom status_usulan
2. **Eager Loading**: Load periodeUsulan hanya jika diperlukan
3. **Caching**: Cache hasil pengecekan akses

## **KESIMPULAN**

Implementasi aturan akses periode telah berhasil dibuat dengan:

1. ✅ **Model Method**: `canAccessPeriode()` dan `getPeriodeInfo()`
2. ✅ **View Updates**: Semua view Tim Senat, Tim Penilai, dan Penilai Universitas
3. ✅ **Access Control**: Berdasarkan status usulan dan role
4. ✅ **User Experience**: Pesan yang jelas dan konsisten
5. ✅ **Maintainability**: Logika terpusat dan mudah dikembangkan

Aturan ini memastikan bahwa informasi periode hanya dapat diakses oleh role yang berwenang dan pada waktu yang tepat dalam workflow usulan.
