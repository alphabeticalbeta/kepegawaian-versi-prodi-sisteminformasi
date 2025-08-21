# Penambahan Field Pendidikan pada Profile

## Deskripsi Perubahan
Menambahkan field `nama_universitas_sekolah` dan `nama_prodi_jurusan` pada halaman my profile setelah Pendidikan terakhir.

## Field yang Ditambahkan

### 1. Nama Universitas/Sekolah
- **Field**: `nama_universitas_sekolah`
- **Tipe**: String (nullable)
- **Maksimal**: 255 karakter
- **Icon**: Building
- **Placeholder**: "Universitas Mulawarman"

### 2. Nama Program Studi/Jurusan
- **Field**: `nama_prodi_jurusan`
- **Tipe**: String (nullable)
- **Maksimal**: 255 karakter
- **Icon**: Book-open
- **Placeholder**: "Teknik Informatika"

## File yang Dimodifikasi

### 1. View: `personal-tab.blade.php`
- **Lokasi**: `resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/personal-tab.blade.php`
- **Perubahan**: Menambahkan dua field input setelah Pendidikan terakhir

### 2. Controller: `ProfileController.php`
- **Lokasi**: `app/Http/Controllers/Backend/PegawaiUnmul/ProfileController.php`
- **Perubahan**: Menambahkan validasi untuk field baru

### 3. Model: `Pegawai.php`
- **Lokasi**: `app/Models/BackendUnivUsulan/Pegawai.php`
- **Perubahan**: Menambahkan field ke `$fillable` array

### 4. Migration: `add_nama_prodi_jurusan_to_pegawais_table.php`
- **Lokasi**: `database/migrations/2025_08_18_085821_add_nama_prodi_jurusan_to_pegawais_table.php`
- **Perubahan**: Menambahkan kolom `nama_prodi_jurusan` ke database

## Detail Implementasi

### 1. View Implementation
```php
{{-- Nama Universitas/Sekolah --}}
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        <i data-lucide="building" class="w-4 h-4 inline mr-1"></i>
        Nama Universitas/Sekolah
    </label>
    @if($isEditing)
        <input type="text" name="nama_universitas_sekolah"
               value="{{ old('nama_universitas_sekolah', $pegawai->nama_universitas_sekolah) }}"
               placeholder="Universitas Mulawarman"
               class="w-full px-3 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nama_universitas_sekolah') border-red-500 @else border-gray-300 @enderror">
        @error('nama_universitas_sekolah')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    @else
        <p class="text-gray-900 py-2">{{ $pegawai->nama_universitas_sekolah ?? '-' }}</p>
    @endif
</div>

{{-- Nama Program Studi/Jurusan --}}
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        <i data-lucide="book-open" class="w-4 h-4 inline mr-1"></i>
        Nama Program Studi/Jurusan
    </label>
    @if($isEditing)
        <input type="text" name="nama_prodi_jurusan"
               value="{{ old('nama_prodi_jurusan', $pegawai->nama_prodi_jurusan) }}"
               placeholder="Teknik Informatika"
               class="w-full px-3 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nama_prodi_jurusan') border-red-500 @else border-gray-300 @enderror">
        @error('nama_prodi_jurusan')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    @else
        <p class="text-gray-900 py-2">{{ $pegawai->nama_prodi_jurusan ?? '-' }}</p>
    @endif
</div>
```

### 2. Controller Validation
```php
$validated = $request->validate([
    // ... existing validations ...
    'pendidikan_terakhir' => 'required|string',
    'nama_universitas_sekolah' => 'nullable|string|max:255',
    'nama_prodi_jurusan' => 'nullable|string|max:255',
    // ... other validations ...
]);
```

### 3. Model Fillable
```php
protected $fillable = [
    // ... existing fields ...
    'pendidikan_terakhir',
    'nama_universitas_sekolah',
    'nama_prodi_jurusan',
    'nama_prodi_jurusan_s2',
    // ... other fields ...
];
```

### 4. Database Migration
```php
public function up(): void
{
    Schema::table('pegawais', function (Blueprint $table) {
        $table->string('nama_prodi_jurusan')->nullable()->after('nama_universitas_sekolah');
    });
}

public function down(): void
{
    Schema::table('pegawais', function (Blueprint $table) {
        $table->dropColumn('nama_prodi_jurusan');
    });
}
```

## Fitur yang Ditambahkan

### 1. Input Fields
- Field input untuk nama universitas/sekolah
- Field input untuk nama program studi/jurusan
- Validasi error handling
- Placeholder text yang informatif

### 2. Display Fields
- Menampilkan data dalam mode view (non-edit)
- Menampilkan "-" jika data kosong
- Konsisten dengan field lainnya

### 3. Validation
- Validasi nullable (opsional)
- Validasi maksimal 255 karakter
- Error message yang informatif

## Status Implementasi
✅ **Selesai**: Field pendidikan telah ditambahkan ke profile

## Catatan
- Field `nama_universitas_sekolah` sudah ada di database
- Field `nama_prodi_jurusan` baru ditambahkan melalui migration
- Kedua field bersifat opsional (nullable)
- Data akan tersimpan dan dapat diupdate melalui form profile
