# Database Column Fix for New Roles

## 🎯 **Masalah yang Ditemukan**

### **Error Message:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status' in 'where clause' 
(Connection: mysql, SQL: select count(*) as aggregate from `usulans` where `status` = Menunggu Verifikasi)
app/Http/Controllers/Backend/AdminKeuangan/DashboardController.php :20
```

### **Root Cause:**
- Controller menggunakan kolom `status` yang tidak ada di tabel `usulans`
- Kolom yang benar adalah `status_usulan` berdasarkan migration dan model
- Relationship `jabatan` seharusnya `jabatanTujuan` karena menggunakan `jabatan_tujuan_id`

## ✅ **Perbaikan yang Diterapkan**

### **1. Database Structure Analysis**

#### **Tabel `usulans` - Kolom yang Benar:**
```php
// Migration: 2025_08_02_221252_create_usulans_table.php
$table->string('status_usulan')->default('Draft');        // ✅ BENAR
$table->foreignId('jabatan_tujuan_id')->nullable();       // ✅ BENAR
$table->foreignId('jabatan_lama_id')->nullable();         // ✅ BENAR
```

#### **Model Usulan - Fillable Fields:**
```php
protected $fillable = [
    'pegawai_id',
    'periode_usulan_id',
    'jenis_usulan',
    'jabatan_lama_id',        // ✅ BENAR
    'jabatan_tujuan_id',      // ✅ BENAR
    'status_usulan',          // ✅ BENAR
    'data_usulan',
    'validasi_data',
    'catatan_verifikator',
];
```

### **2. Controller Fixes**

#### **Admin Keuangan Controllers:**

**DashboardController.php:**
```diff
- 'usulan_pending' => Usulan::where('status', 'Menunggu Verifikasi')->count(),
+ 'usulan_pending' => Usulan::where('status_usulan', 'Menunggu Verifikasi')->count(),

- 'usulan_approved' => Usulan::where('status', 'Disetujui')->count(),
+ 'usulan_approved' => Usulan::where('status_usulan', 'Disetujui')->count(),

- $usulanByStatus = Usulan::selectRaw('status, COUNT(*) as count')
+ $usulanByStatus = Usulan::selectRaw('status_usulan, COUNT(*) as count')
```

**LaporanKeuanganController.php:**
```diff
- ->where('status', 'Disetujui');
+ ->where('status_usulan', 'Disetujui');
```

**VerifikasiDokumenController.php:**
```diff
- ->whereIn('status', ['Menunggu Verifikasi', 'Dalam Proses']);
+ ->whereIn('status_usulan', ['Menunggu Verifikasi', 'Dalam Proses']);

- ->where('status', $request->status);
+ ->where('status_usulan', $request->status);

- 'jabatan:id,jabatan'
+ 'jabatanTujuan:id,jabatan'
```

#### **Tim Senat Controllers:**

**DashboardController.php:**
```diff
- 'usulan_pending_review' => Usulan::where('status', 'Menunggu Review Senat')->count(),
+ 'usulan_pending_review' => Usulan::where('status_usulan', 'Menunggu Review Senat')->count(),

- Usulan::join('jabatans', 'usulans.jabatan_id', '=', 'jabatans.id')
+ Usulan::join('jabatans', 'usulans.jabatan_tujuan_id', '=', 'jabatans.id')

- 'jabatan:id,jabatan'
+ 'jabatanTujuan:id,jabatan'
```

**RapatSenatController.php:**
```diff
- ->where('status', $request->status);
+ ->where('status_usulan', $request->status);

- ->where('jabatan_id', $request->jabatan);
+ ->where('jabatan_tujuan_id', $request->jabatan);
```

**KeputusanSenatController.php:**
```diff
- ->where('status', 'Sudah Direview Senat');
+ ->where('status_usulan', 'Sudah Direview Senat');

- ->where('status_final', 'Disetujui');
+ ->where('status_usulan', 'Disetujui');

- ->where('jabatan_id', $request->jabatan);
+ ->where('jabatan_tujuan_id', $request->jabatan);
```

### **3. View Template Fixes**

#### **Admin Keuangan Dashboard:**
```diff
- {{ $usulan->status === 'Disetujui' ? 'bg-green-100 text-green-800' :
+ {{ $usulan->status_usulan === 'Disetujui' ? 'bg-green-100 text-green-800' :

- {{ $usulan->status }}
+ {{ $usulan->status_usulan }}
```

#### **Tim Senat Dashboard:**
```diff
- {{ $usulan->jabatan->jabatan ?? 'Jabatan tidak diketahui' }}
+ {{ $usulan->jabatanTujuan->jabatan ?? 'Jabatan tidak diketahui' }}

- {{ $usulan->status === 'Disetujui' ? 'bg-green-100 text-green-800' :
+ {{ $usulan->status_usulan === 'Disetujui' ? 'bg-green-100 text-green-800' :
```

## 🔧 **Penjelasan Teknis**

### **1. Kolom Status**
```php
// SALAH ❌
->where('status', 'Disetujui')

// BENAR ✅
->where('status_usulan', 'Disetujui')
```

**Alasan:** Kolom `status` tidak ada di tabel `usulans`. Kolom yang benar adalah `status_usulan`.

### **2. Relationship Jabatan**
```php
// SALAH ❌
'jabatan:id,jabatan'                    // Tidak ada relationship 'jabatan'
->where('jabatan_id', $request->jabatan) // Kolom 'jabatan_id' tidak ada

// BENAR ✅
'jabatanTujuan:id,jabatan'              // Relationship 'jabatanTujuan' ada
->where('jabatan_tujuan_id', $request->jabatan) // Kolom 'jabatan_tujuan_id' ada
```

**Alasan:** Model `Usulan` memiliki relationship `jabatanTujuan` yang menggunakan foreign key `jabatan_tujuan_id`.

### **3. Join Query**
```php
// SALAH ❌
Usulan::join('jabatans', 'usulans.jabatan_id', '=', 'jabatans.id')

// BENAR ✅
Usulan::join('jabatans', 'usulans.jabatan_tujuan_id', '=', 'jabatans.id')
```

**Alasan:** Foreign key yang benar adalah `jabatan_tujuan_id`, bukan `jabatan_id`.

## 📊 **Status Values**

### **Kemungkinan Status Usulan:**
- `Draft` - Status default
- `Menunggu Verifikasi` - Pending verification
- `Dalam Proses` - Under process
- `Menunggu Review Senat` - Waiting for senate review
- `Sudah Direview Senat` - Reviewed by senate
- `Disetujui` - Approved
- `Ditolak` - Rejected

### **Status Flow:**
```
Draft → Menunggu Verifikasi → Dalam Proses → Menunggu Review Senat → Sudah Direview Senat → Disetujui/Ditolak
```

## 🎯 **Files Modified**

### **Controllers Fixed:**
```
✅ app/Http/Controllers/Backend/AdminKeuangan/DashboardController.php
✅ app/Http/Controllers/Backend/AdminKeuangan/LaporanKeuanganController.php
✅ app/Http/Controllers/Backend/AdminKeuangan/VerifikasiDokumenController.php
✅ app/Http/Controllers/Backend/TimSenat/DashboardController.php
✅ app/Http/Controllers/Backend/TimSenat/RapatSenatController.php
✅ app/Http/Controllers/Backend/TimSenat/KeputusanSenatController.php
```

### **Views Fixed:**
```
✅ resources/views/backend/layouts/views/admin-keuangan/dashboard.blade.php
✅ resources/views/backend/layouts/views/tim-senat/dashboard.blade.php
```

### **Key Changes Summary:**
1. **status** → **status_usulan** (kolom database yang benar)
2. **jabatan** → **jabatanTujuan** (relationship yang benar)
3. **jabatan_id** → **jabatan_tujuan_id** (foreign key yang benar)

## ✅ **Verification Steps**

### **1. Test Admin Keuangan Dashboard:**
1. Login dengan role "Admin Keuangan"
2. Akses: `http://localhost/admin-keuangan/dashboard`
3. ✅ Tidak ada error SQL
4. ✅ Statistics menampilkan data yang benar

### **2. Test Tim Senat Dashboard:**
1. Login dengan role "Tim Senat"
2. Akses: `http://localhost/tim-senat/dashboard`
3. ✅ Tidak ada error SQL
4. ✅ Statistics menampilkan data yang benar

### **3. Test Relationships:**
1. ✅ Usulan menampilkan jabatan tujuan yang benar
2. ✅ Status usulan menampilkan nilai yang benar
3. ✅ Filter dan query berfungsi dengan benar

---

**🎉 Database column issues sudah diperbaiki! Dashboard Admin Keuangan dan Tim Senat sekarang menggunakan kolom database yang benar dan tidak akan menghasilkan error SQL.**
