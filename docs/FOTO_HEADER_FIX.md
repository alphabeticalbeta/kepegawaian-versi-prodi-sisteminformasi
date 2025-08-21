# Perbaikan Foto Header di Halaman Admin Universitas Usulan

## 🔍 **Masalah yang Ditemukan:**

### **1. Foto Header Tidak Tampil:**
- Foto profil di header halaman admin universitas usulan tidak dapat ditampilkan
- Error 403 Forbidden saat mengakses foto melalui route `show-document`
- Masalah dengan guard authentication di method `showDocument`

### **2. Root Cause Analysis:**
- Method `showDocument` menggunakan `Auth::guard('pegawai')` 
- Admin universitas usulan menggunakan guard yang berbeda
- Tidak ada fallback untuk menangani user dari berbagai guard

## 🔧 **Solusi yang Diterapkan:**

### **1. Perbaikan DataPegawaiController:**

#### **A. Method showDocument - Multi-Guard Support:**
```php
// SEBELUM: Hanya menggunakan guard 'pegawai'
$currentUser = Auth::guard('pegawai')->user();

// SETELAH: Support multiple guards
$currentUser = Auth::guard('pegawai')->user() ?? Auth::guard('web')->user() ?? Auth::user();

if (!$currentUser) {
    abort(403, 'Anda harus login untuk mengakses dokumen ini.');
}
```

#### **B. Method logDocumentAccess - Multi-Guard Support:**
```php
// SEBELUM: Hanya menggunakan guard 'pegawai'
$accessor = Auth::guard('pegawai')->user();

// SETELAH: Support multiple guards
$accessor = Auth::guard('pegawai')->user() ?? Auth::guard('web')->user() ?? Auth::user();
```

### **2. Perbaikan Header View:**

#### **A. Fallback Strategy untuk Foto:**
```php
// SEBELUM: Hanya menggunakan route
src="{{ $user->foto ? route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $user->id, 'field' => 'foto']) : 'fallback-url' }}"

// SETELAH: Multi-level fallback
src="{{ $user->foto ? (Storage::disk('public')->exists($user->foto) ? Storage::url($user->foto) : route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $user->id, 'field' => 'foto'])) : 'fallback-url' }}"
onerror="this.src='fallback-url'"
```

#### **B. Implementasi di Header:**
```php
<!-- Foto kecil di button profil -->
<img
    src="{{ $user->foto ? (Storage::disk('public')->exists($user->foto) ? Storage::url($user->foto) : route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $user->id, 'field' => 'foto'])) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap) . '&size=32&background=6366f1&color=fff' }}"
    alt="{{ $user->nama_lengkap }}"
    class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-200"
    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&size=32&background=6366f1&color=fff'"
/>

<!-- Foto besar di dropdown profil -->
<img
    src="{{ $user->foto ? (Storage::disk('public')->exists($user->foto) ? Storage::url($user->foto) : route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $user->id, 'field' => 'foto'])) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap) . '&size=48&background=6366f1&color=fff' }}"
    alt="{{ $user->nama_lengkap }}"
    class="w-12 h-12 rounded-full object-cover"
    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&size=48&background=6366f1&color=fff'"
/>
```

## 📊 **Fallback Strategy:**

### **1. Multi-Level Fallback:**
```
Level 1: Storage::disk('public')->exists() + Storage::url()
Level 2: Route show-document (dengan perbaikan guard)
Level 3: onerror JavaScript fallback ke UI Avatars
Level 4: Default UI Avatars jika tidak ada foto
```

### **2. Guard Support:**
```
Guard Priority:
1. Auth::guard('pegawai')->user() - untuk pegawai
2. Auth::guard('web')->user() - untuk admin web
3. Auth::user() - fallback default
```

## 🎯 **Testing Steps:**

### **1. Test Foto Header:**
1. Login sebagai admin universitas usulan
2. **Expected:** Foto profil tampil di header (button profil)
3. **Expected:** Foto profil tampil di dropdown profil
4. **Expected:** Jika foto tidak ada, tampil avatar dengan inisial

### **2. Test Fallback Scenarios:**
1. **Foto ada di public disk:**
   - **Expected:** Tampil menggunakan `Storage::url()`

2. **Foto tidak ada di public disk:**
   - **Expected:** Tampil menggunakan route `show-document`

3. **Route show-document error:**
   - **Expected:** JavaScript onerror fallback ke UI Avatars

4. **Tidak ada foto sama sekali:**
   - **Expected:** Default UI Avatars dengan inisial nama

### **3. Test Access Control:**
1. **Admin Universitas Usulan:**
   - **Expected:** Bisa akses foto semua pegawai

2. **Admin Fakultas:**
   - **Expected:** Bisa akses foto pegawai di fakultasnya

3. **Pegawai:**
   - **Expected:** Bisa akses foto sendiri

## ✅ **Expected Outcome:**

Setelah perbaikan, sistem seharusnya:
- ✅ **Foto Header:** Foto profil tampil dengan benar di header
- ✅ **Multi-Guard:** Support untuk berbagai jenis user (admin, pegawai)
- ✅ **Fallback:** Graceful fallback jika foto tidak dapat diakses
- ✅ **Access Control:** Tetap aman dengan permission yang tepat
- ✅ **Error Handling:** Tidak ada error 403 atau broken image

## 🔄 **Data Flow yang Diperbaiki:**

### **1. Foto Access Flow:**
```
Request → Header View → Check Storage → 
If exists: Storage::url() → Display
If not: Route show-document → Controller → 
Multi-guard auth → File serve → Display
If error: JavaScript onerror → UI Avatars → Display
```

### **2. Authentication Flow:**
```
Request → showDocument() → 
Try guard('pegawai') → Try guard('web') → Try Auth::user() → 
Access control check → File serve
```

## 🚀 **Additional Improvements:**

### **1. Error Logging:**
```php
// Tambahkan logging untuk debugging
\Log::info('Foto access attempt', [
    'user_id' => $currentUser->id,
    'guard' => Auth::getDefaultDriver(),
    'field' => $field,
    'file_path' => $filePath
]);
```

### **2. Cache Strategy:**
```php
// Tambahkan cache untuk foto yang sering diakses
$cacheKey = "foto_{$pegawai->id}_{$field}";
return Cache::remember($cacheKey, 3600, function() use ($fullPath) {
    return response()->file($fullPath);
});
```

### **3. Image Optimization:**
```php
// Tambahkan image optimization jika diperlukan
if (str_ends_with($filePath, '.jpg') || str_ends_with($filePath, '.jpeg')) {
    // Optimize image size for header
    $image = Image::make($fullPath)->resize(100, 100, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });
    return $image->response();
}
```

---

**🔧 Fixes Applied - Ready for Testing!**

**Next Steps:**
1. **Test Foto Header** - Pastikan foto tampil di header admin universitas usulan
2. **Test Fallback** - Pastikan fallback berfungsi jika foto tidak ada
3. **Test Access Control** - Pastikan permission tetap aman
4. **Monitor Logs** - Periksa log untuk debugging jika ada masalah
