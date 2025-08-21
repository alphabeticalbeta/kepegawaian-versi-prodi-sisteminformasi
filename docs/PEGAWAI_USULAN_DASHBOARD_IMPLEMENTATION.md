# Implementasi Dashboard Usulan Role Pegawai UNMUL

## 🎯 **Tujuan Implementasi:**

Membuat halaman dashboard untuk setiap jenis usulan pada role pegawai, berdasarkan sidebar admin universitas usulan yang sudah ada. Setiap jenis usulan akan memiliki halaman dashboard sendiri yang menampilkan daftar usulan sesuai jenisnya.

## 📋 **Jenis Usulan yang Diimplementasikan:**

Berdasarkan sidebar admin universitas usulan, ada **13 jenis usulan** yang perlu dibuat dashboard-nya:

### **1. Usulan Jabatan** ✅ (Sudah ada)
- **Controller:** `UsulanJabatanController`
- **View:** `usul-jabatan/index.blade.php`
- **Route:** `pegawai-unmul.usulan-jabatan.*`

### **2. Usulan NUPTK** ✅ (Baru dibuat)
- **Controller:** `UsulanNuptkController`
- **View:** `usulan-nuptk/index.blade.php`
- **Route:** `pegawai-unmul.usulan-nuptk.*`

### **3. Usulan Laporan LKD** ✅ (Baru dibuat)
- **Controller:** `UsulanLaporanLkdController`
- **View:** `usulan-laporan-lkd/index.blade.php`
- **Route:** `pegawai-unmul.usulan-laporan-lkd.*`

### **4. Usulan Presensi** ⏳ (Belum dibuat)
- **Controller:** `UsulanPresensiController`
- **View:** `usulan-presensi/index.blade.php`
- **Route:** `pegawai-unmul.usulan-presensi.*`

### **5. Usulan Penyesuaian Masa Kerja** ⏳ (Belum dibuat)
- **Controller:** `UsulanPenyesuaianMasaKerjaController`
- **View:** `usulan-penyesuaian-masa-kerja/index.blade.php`
- **Route:** `pegawai-unmul.usulan-penyesuaian-masa-kerja.*`

### **6. Usulan Ujian Dinas & Ijazah** ⏳ (Belum dibuat)
- **Controller:** `UsulanUjianDinasIjazahController`
- **View:** `usulan-ujian-dinas-ijazah/index.blade.php`
- **Route:** `pegawai-unmul.usulan-ujian-dinas-ijazah.*`

### **7. Usulan Laporan Serdos** ⏳ (Belum dibuat)
- **Controller:** `UsulanLaporanSerdosController`
- **View:** `usulan-laporan-serdos/index.blade.php`
- **Route:** `pegawai-unmul.usulan-laporan-serdos.*`

### **8. Usulan Pensiun** ⏳ (Belum dibuat)
- **Controller:** `UsulanPensiunController`
- **View:** `usulan-pensiun/index.blade.php`
- **Route:** `pegawai-unmul.usulan-pensiun.*`

### **9. Usulan Kepangkatan** ⏳ (Belum dibuat)
- **Controller:** `UsulanKepangkatanController`
- **View:** `usulan-kepangkatan/index.blade.php`
- **Route:** `pegawai-unmul.usulan-kepangkatan.*`

### **10. Usulan Pencantuman Gelar** ⏳ (Belum dibuat)
- **Controller:** `UsulanPencantumanGelarController`
- **View:** `usulan-pencantuman-gelar/index.blade.php`
- **Route:** `pegawai-unmul.usulan-pencantuman-gelar.*`

### **11. Usulan ID SINTA ke SISTER** ⏳ (Belum dibuat)
- **Controller:** `UsulanIdSintaSisterController`
- **View:** `usulan-id-sinta-sister/index.blade.php`
- **Route:** `pegawai-unmul.usulan-id-sinta-sister.*`

### **12. Usulan Satyalancana** ⏳ (Belum dibuat)
- **Controller:** `UsulanSatyalancanaController`
- **View:** `usulan-satyalancana/index.blade.php`
- **Route:** `pegawai-unmul.usulan-satyalancana.*`

### **13. Usulan Tugas Belajar** ⏳ (Belum dibuat)
- **Controller:** `UsulanTugasBelajarController`
- **View:** `usulan-tugas-belajar/index.blade.php`
- **Route:** `pegawai-unmul.usulan-tugas-belajar.*`

### **14. Usulan Pengaktifan Kembali** ⏳ (Belum dibuat)
- **Controller:** `UsulanPengaktifanKembaliController`
- **View:** `usulan-pengaktifan-kembali/index.blade.php`
- **Route:** `pegawai-unmul.usulan-pengaktifan-kembali.*`

## 🔧 **Implementasi yang Sudah Dibuat:**

### **1. Controller Structure:**
```php
<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsulanNuptkController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user();
        $usulans = $pegawai->usulans()
                          ->where('jenis_usulan', 'usulan-nuptk')
                          ->with(['periodeUsulan'])
                          ->latest()
                          ->paginate(10);

        return view('backend.layouts.views.pegawai-unmul.usulan-nuptk.index', compact('usulans'));
    }

    public function create()
    {
        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                         ->with('info', 'Fitur Usulan NUPTK akan segera tersedia.');
    }

    // ... other methods (show, edit, update, destroy)
}
```

### **2. View Structure:**
```blade
@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Usulan NUPTK Saya')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Usulan NUPTK Saya
                </h1>
                <p class="mt-2 text-gray-600">
                    Pantau status dan riwayat usulan NUPTK yang telah Anda ajukan.
                </p>
            </div>
            <a href="{{ route('pegawai-unmul.usulan-nuptk.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Buat Usulan Baru
            </a>
        </div>
    </div>

    {{-- Content Table --}}
    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                Daftar Usulan NUPTK
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Berikut adalah semua usulan NUPTK yang pernah Anda buat.
            </p>
        </div>

        <div class="overflow-x-auto">
            @if($usulans->count() > 0)
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">Periode</th>
                            <th scope="col" class="px-6 py-4">Tanggal Pengajuan</th>
                            <th scope="col" class="px-6 py-4 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usulans as $usulan)
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4">{{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $usulan->created_at->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($usulan->status_usulan) {
                                            'Draft' => 'bg-gray-100 text-gray-800',
                                            'Diajukan' => 'bg-blue-100 text-blue-800',
                                            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                                            'Perlu Perbaikan' => 'bg-orange-100 text-orange-800',
                                            'Dikembalikan' => 'bg-red-100 text-red-800',
                                            'Disetujui' => 'bg-green-100 text-green-800',
                                            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                        {{ $usulan->status_usulan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('pegawai-unmul.usulan-nuptk.show', $usulan->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                            Detail
                                        </a>
                                        @if($usulan->status_usulan === 'Draft')
                                            <a href="{{ route('pegawai-unmul.usulan-nuptk.edit', $usulan->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $usulans->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada usulan</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Anda belum pernah membuat usulan NUPTK.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('pegawai-unmul.usulan-nuptk.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Buat Usulan Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
```

### **3. Routes Structure:**
```php
// =====================================================
// USULAN NUPTK ROUTES
// =====================================================
Route::resource('usulan-nuptk', App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class)
    ->names('usulan-nuptk');

// =====================================================
// USULAN LAPORAN LKD ROUTES
// =====================================================
Route::resource('usulan-laporan-lkd', App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class)
    ->names('usulan-laporan-lkd');
```

### **4. Sidebar Update:**
```php
$usulanMenus = [
    ['route' => route('pegawai-unmul.usulan-jabatan.index'), 'icon' => 'file-user', 'label' => 'Usulan Jabatan', 'pattern' => 'pegawai-unmul.usulan-jabatan.*'],
    ['route' => route('pegawai-unmul.usulan-nuptk.index'), 'icon' => 'user-check', 'label' => 'Usulan NUPTK', 'pattern' => 'pegawai-unmul.usulan-nuptk.*'],
    ['route' => route('pegawai-unmul.usulan-laporan-lkd.index'), 'icon' => 'file-bar-chart-2', 'label' => 'Usulan Laporan LKD', 'pattern' => 'pegawai-unmul.usulan-laporan-lkd.*'],
    // ... other usulan types
];
```

## 🚀 **Generator Script:**

File `generate_usulan_controllers.php` telah dibuat untuk menghasilkan semua controller dan view yang diperlukan secara otomatis. Script ini berisi:

### **1. Usulan Types Array:**
```php
$usulanTypes = [
    'nuptk' => [
        'name' => 'NUPTK',
        'icon' => 'user-check',
        'description' => 'Pengajuan NUPTK'
    ],
    'laporan-lkd' => [
        'name' => 'Laporan LKD',
        'icon' => 'file-bar-chart-2',
        'description' => 'Pengajuan Laporan LKD'
    ],
    // ... all 13 usulan types
];
```

### **2. Controller Generator Function:**
```php
function generateControllerContent($controllerName, $key, $type) {
    // Generate complete controller content
}
```

### **3. View Generator Function:**
```php
function generateViewContent($key, $type) {
    // Generate complete view content
}
```

## 📊 **Features yang Diimplementasikan:**

### **1. Dashboard Features:**
- ✅ **Tabel Daftar Usulan:** Menampilkan semua usulan sesuai jenis
- ✅ **Status Badges:** Color-coded status dengan visual feedback
- ✅ **Pagination:** Support untuk data yang banyak
- ✅ **Action Buttons:** Detail dan Edit (untuk Draft)
- ✅ **Empty State:** Tampilan ketika belum ada usulan
- ✅ **Flash Messages:** Notifikasi success, error, warning, info

### **2. Navigation Features:**
- ✅ **Sidebar Integration:** Menu dropdown "Layanan Usulan"
- ✅ **Active State:** Highlight menu yang sedang aktif
- ✅ **Route Protection:** Hanya usulan milik user yang bisa diakses
- ✅ **Consistent Layout:** Semua halaman menggunakan layout yang sama

### **3. Security Features:**
- ✅ **Authorization:** Cek kepemilikan usulan di setiap method
- ✅ **Route Protection:** Middleware auth:pegawai
- ✅ **Data Isolation:** User hanya bisa lihat usulan sendiri

## 🎯 **Testing Steps:**

### **1. Test Navigation:**
1. Login sebagai pegawai
2. Klik dropdown "Layanan Usulan" di sidebar
3. Klik "Usulan NUPTK" atau "Usulan Laporan LKD"
4. **Expected:** Halaman dashboard usulan tampil

### **2. Test Empty State:**
1. Akses halaman usulan yang belum pernah dibuat
2. **Expected:** Empty state dengan CTA button tampil
3. **Expected:** Message "Belum ada usulan"

### **3. Test Flash Messages:**
1. Klik "Buat Usulan Baru" (untuk yang belum tersedia)
2. **Expected:** Redirect ke dashboard dengan info message
3. **Expected:** Message "Fitur akan segera tersedia"

### **4. Test Responsive:**
1. Test di berbagai ukuran layar
2. **Expected:** Tabel responsive, tidak overflow
3. **Expected:** Button dan layout tetap rapi

## 📋 **Next Steps untuk Implementasi Lengkap:**

### **1. Generate Remaining Controllers:**
```bash
# Run generator script untuk membuat semua controller
php generate_usulan_controllers.php
```

### **2. Create All View Files:**
- Buat direktori untuk setiap jenis usulan
- Generate view files sesuai template

### **3. Add All Routes:**
```php
// Add routes for all remaining usulan types
Route::resource('usulan-presensi', UsulanPresensiController::class)->names('usulan-presensi');
Route::resource('usulan-penyesuaian-masa-kerja', UsulanPenyesuaianMasaKerjaController::class)->names('usulan-penyesuaian-masa-kerja');
// ... continue for all types
```

### **4. Update Sidebar Completely:**
- Update semua route di sidebar
- Test semua link berfungsi

### **5. Implement Form Pages:**
- Create form untuk setiap jenis usulan
- Implement validation dan storage logic

## ✅ **Current Status:**

- ✅ **Architecture:** Framework sudah siap
- ✅ **Template:** Controller dan view template sudah dibuat
- ✅ **Navigation:** Sidebar sudah diupdate untuk 3 jenis usulan
- ✅ **Routes:** Routes untuk 3 jenis usulan sudah ditambahkan
- ⏳ **Remaining:** 11 jenis usulan perlu dibuat
- ⏳ **Forms:** Form pages belum diimplementasi

---

**🔧 Implementation Ready - Framework Complete!**

**Next Steps:**
1. **Run Generator:** Execute `generate_usulan_controllers.php`
2. **Create Views:** Generate all view files
3. **Add Routes:** Add routes for all usulan types
4. **Test Navigation:** Verify all sidebar links work
5. **Implement Forms:** Create form pages for each usulan type
