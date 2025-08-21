# Update Halaman Usul Jabatan Index

## 🎯 **Aturan yang Diterapkan**

Berdasarkan permintaan Anda, telah diterapkan aturan berikut pada halaman `resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/index.blade.php`:

### **1. Periode Usulan Sesuai Status Kepegawaian**
- ✅ Hanya menampilkan periode usulan yang sesuai dengan status kepegawaian pegawai
- ✅ Menggunakan `whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)`
- ✅ Filter berdasarkan jenis usulan yang sesuai (Dosen/Tenaga Kependidikan)

### **2. Fokus pada Usul Jabatan Dosen**
- ✅ Menggunakan `determineJenisUsulanPeriode()` untuk menentukan jenis usulan
- ✅ Untuk Dosen PNS: `usulan-jabatan-dosen`
- ✅ Untuk Tenaga Kependidikan PNS: `usulan-jabatan-tendik`

### **3. Tabel dengan Kolom yang Diminta**
- ✅ **No** - Nomor urut
- ✅ **Nama Periode** - Nama periode usulan
- ✅ **Tanggal Pembukaan** - `tanggal_mulai`
- ✅ **Tanggal Penutupan** - `tanggal_selesai`
- ✅ **Tanggal Awal Perbaikan** - `tanggal_mulai_perbaikan`
- ✅ **Tanggal Akhir Perbaikan** - `tanggal_selesai_perbaikan`
- ✅ **Aksi** - Tombol berdasarkan status usulan

### **4. Logika Tombol Aksi**
- ✅ **Jika belum ada usulan**: Tombol "Membuat Usulan" (hijau)
- ✅ **Jika sudah ada usulan**: Tombol "Lihat Detail" dan "Hapus"

## 🔧 **Perubahan yang Dilakukan**

### **1. Controller (`UsulanJabatanController.php`)**

#### **Method `index()` - Diperbarui:**
```php
public function index()
{
    $pegawai = Auth::user();

    // Determine jenis usulan berdasarkan status kepegawaian
    $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);

    // Get periode usulan yang sesuai dengan status kepegawaian
    $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
        ->where('status', 'Buka')
        ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
        ->orderBy('tanggal_mulai', 'desc')
        ->get();

    // Get usulan yang sudah dibuat oleh pegawai
    $usulans = $pegawai->usulans()
                      ->where('jenis_usulan', $jenisUsulanPeriode)
                      ->with(['periodeUsulan', 'jabatanLama', 'jabatanTujuan'])
                      ->get();

    return view('backend.layouts.views.pegawai-unmul.usul-jabatan.index', 
                compact('periodeUsulans', 'usulans', 'pegawai'));
}
```

#### **Method `show()` - Ditambahkan:**
```php
public function show($id)
{
    $usulan = Usulan::where('id', $id)
                    ->where('pegawai_id', Auth::id())
                    ->with(['periodeUsulan', 'jabatanLama', 'jabatanTujuan'])
                    ->firstOrFail();

    return view('backend.layouts.views.pegawai-unmul.usul-jabatan.show', compact('usulan'));
}
```

#### **Method `destroy()` - Ditambahkan:**
```php
public function destroy($id)
{
    $usulan = Usulan::where('id', $id)
                    ->where('pegawai_id', Auth::id())
                    ->firstOrFail();

    // Only allow deletion if status is Draft or Perlu Perbaikan
    if (!in_array($usulan->status_usulan, ['Draft', 'Perlu Perbaikan'])) {
        return redirect()->route('pegawai-unmul.usulan-jabatan.index')
            ->with('error', 'Usulan tidak dapat dihapus karena status tidak memungkinkan.');
    }

    // Delete logic with transaction
    // ...
}
```

### **2. View (`index.blade.php`) - Diperbarui:**

#### **Header Section:**
```blade
<h1 class="text-3xl font-bold text-gray-900">
    Periode Usulan Jabatan
</h1>
<p class="mt-2 text-gray-600">
    Daftar periode usulan jabatan yang tersedia untuk status kepegawaian Anda ({{ $pegawai->status_kepegawaian }}).
</p>
```

#### **Tabel Structure:**
```blade
<table class="w-full text-sm text-left text-gray-600">
    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
        <tr>
            <th scope="col" class="px-6 py-4">No</th>
            <th scope="col" class="px-6 py-4">Nama Periode</th>
            <th scope="col" class="px-6 py-4">Tanggal Pembukaan</th>
            <th scope="col" class="px-6 py-4">Tanggal Penutupan</th>
            <th scope="col" class="px-6 py-4">Tanggal Awal Perbaikan</th>
            <th scope="col" class="px-6 py-4">Tanggal Akhir Perbaikan</th>
            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($periodeUsulans as $index => $periode)
            @php
                // Cek apakah pegawai sudah membuat usulan untuk periode ini
                $existingUsulan = $usulans->where('periode_usulan_id', $periode->id)->first();
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $periode->nama_periode }}</td>
                <td>{{ $periode->tanggal_mulai->isoFormat('D MMMM YYYY') }}</td>
                <td>{{ $periode->tanggal_selesai->isoFormat('D MMMM YYYY') }}</td>
                <td>{{ $periode->tanggal_mulai_perbaikan->isoFormat('D MMMM YYYY') }}</td>
                <td>{{ $periode->tanggal_selesai_perbaikan->isoFormat('D MMMM YYYY') }}</td>
                <td>
                    @if($existingUsulan)
                        {{-- Tombol Lihat Detail dan Hapus --}}
                    @else
                        {{-- Tombol Membuat Usulan --}}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### **3. Routes - Diperbarui:**

#### **Route Show - Ditambahkan:**
```php
Route::get('/{usulan}', [UsulanJabatanController::class, 'show'])->name('show');
```

#### **Route Destroy - Sudah Ada:**
```php
Route::delete('/{usulanJabatan}', [UsulanJabatanController::class, 'destroy'])->name('destroy');
```

### **4. View Show (`show.blade.php`) - Dibuat Baru:**

File baru untuk menampilkan detail usulan dengan:
- ✅ Informasi periode usulan
- ✅ Informasi jabatan (lama dan tujuan)
- ✅ Status usulan
- ✅ Catatan verifikator (jika ada)
- ✅ Dokumen usulan
- ✅ Log aktivitas

## 🎨 **UI/UX Improvements**

### **1. Tombol Aksi yang Responsif:**
- **Membuat Usulan**: Tombol hijau dengan ikon plus
- **Lihat Detail**: Tombol biru dengan ikon eye
- **Hapus**: Tombol merah dengan ikon trash

### **2. Modal Konfirmasi Hapus:**
- ✅ Modal popup untuk konfirmasi hapus
- ✅ Pesan peringatan yang jelas
- ✅ Tombol Batal dan Hapus

### **3. Status Badge:**
- ✅ Warna berbeda untuk setiap status
- ✅ Responsive design

## 🔒 **Security Features**

### **1. Authorization:**
- ✅ Hanya pegawai yang bersangkutan yang bisa melihat usulannya
- ✅ Validasi `pegawai_id` di setiap method

### **2. Validation:**
- ✅ Hanya usulan dengan status 'Draft' atau 'Perlu Perbaikan' yang bisa dihapus
- ✅ Validasi file upload dan dokumen

## 📋 **Testing Checklist**

### **1. Periode Usulan:**
- [ ] Hanya menampilkan periode yang sesuai status kepegawaian
- [ ] Tampilan tanggal yang benar
- [ ] Urutan periode (descending)

### **2. Tombol Aksi:**
- [ ] Tombol "Membuat Usulan" muncul jika belum ada usulan
- [ ] Tombol "Lihat Detail" dan "Hapus" muncul jika sudah ada usulan
- [ ] Modal konfirmasi hapus berfungsi

### **3. Navigation:**
- [ ] Link ke halaman create dengan parameter periode
- [ ] Link ke halaman show detail
- [ ] Redirect setelah hapus

### **4. Error Handling:**
- [ ] Pesan error jika tidak ada periode
- [ ] Pesan error jika gagal hapus
- [ ] Validasi status untuk hapus

## 🚀 **Next Steps**

1. **Test aplikasi** untuk memastikan semua fitur berfungsi
2. **Clear cache** jika ada masalah tampilan
3. **Periksa database** untuk memastikan data periode usulan tersedia
4. **Test dengan berbagai status kepegawaian**

**Halaman usul jabatan sekarang sudah sesuai dengan aturan yang Anda minta!** 🎉
