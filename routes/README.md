# Dokumentasi Route Backend - Kepegawaian UNMUL

## 📋 **Struktur Route**

Route backend diorganisir berdasarkan role dan fitur untuk memudahkan maintenance dan pengembangan.

## 🔐 **Authentication Routes**

```php
// Login & Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
```

## 🛡️ **Protected Routes**

Semua route di bawah ini dilindungi dengan middleware `auth:pegawai` dan hanya dapat diakses setelah login.

## 👥 **Role-Based Routes**

### **1. Admin Universitas**
```php
Route::prefix('admin-universitas')
    ->name('admin-universitas.')
    ->middleware(['role:Admin Universitas'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
```

**Available Routes:**
- `GET /admin-universitas/dashboard` - Dashboard Admin Universitas

### **2. Admin Universitas Usulan**
```php
Route::prefix('admin-univ-usulan')
    ->name('backend.admin-univ-usulan.')
    ->middleware(['role:Admin Universitas Usulan'])
    ->group(function () {
        // Routes here
    });
```

**Available Routes:**

#### **Dashboard**
- `GET /admin-univ-usulan/dashboard` - Dashboard Admin Univ Usulan

#### **Master Data**
- `GET /admin-univ-usulan/data-pegawai` - Index Data Pegawai
- `POST /admin-univ-usulan/data-pegawai` - Store Data Pegawai
- `GET /admin-univ-usulan/data-pegawai/{pegawai}/edit` - Edit Data Pegawai
- `PUT /admin-univ-usulan/data-pegawai/{pegawai}` - Update Data Pegawai
- `DELETE /admin-univ-usulan/data-pegawai/{pegawai}` - Delete Data Pegawai
- `GET /admin-univ-usulan/data-pegawai/{pegawai}/dokumen/{field}` - Show Document

#### **Unit Kerja**
- `GET /admin-univ-usulan/unitkerja` - Index Unit Kerja
- `POST /admin-univ-usulan/unitkerja` - Store Unit Kerja
- `GET /admin-univ-usulan/unitkerja/{unitkerja}/edit` - Edit Unit Kerja
- `PUT /admin-univ-usulan/unitkerja/{unitkerja}` - Update Unit Kerja
- `DELETE /admin-univ-usulan/unitkerja/{unitkerja}` - Delete Unit Kerja

#### **Sub Unit Kerja**
- `GET /admin-univ-usulan/sub-unitkerja` - Index Sub Unit Kerja
- `POST /admin-univ-usulan/sub-unitkerja` - Store Sub Unit Kerja
- `GET /admin-univ-usulan/sub-unitkerja/{sub_unitkerja}/edit` - Edit Sub Unit Kerja
- `PUT /admin-univ-usulan/sub-unitkerja/{sub_unitkerja}` - Update Sub Unit Kerja
- `DELETE /admin-univ-usulan/sub-unitkerja/{sub_unitkerja}` - Delete Sub Unit Kerja

#### **Sub Sub Unit Kerja**
- `GET /admin-univ-usulan/sub-sub-unitkerja` - Index Sub Sub Unit Kerja
- `POST /admin-univ-usulan/sub-sub-unitkerja` - Store Sub Sub Unit Kerja
- `GET /admin-univ-usulan/sub-sub-unitkerja/{sub_sub_unitkerja}/edit` - Edit Sub Sub Unit Kerja
- `PUT /admin-univ-usulan/sub-sub-unitkerja/{sub_sub_unitkerja}` - Update Sub Sub Unit Kerja
- `DELETE /admin-univ-usulan/sub-sub-unitkerja/{sub_sub_unitkerja}` - Delete Sub Sub Unit Kerja
- `GET /admin-univ-usulan/get-sub-unit-kerjas` - Get Sub Unit Kerjas (API)

#### **Jabatan**
- `GET /admin-univ-usulan/jabatan` - Index Jabatan
- `POST /admin-univ-usulan/jabatan` - Store Jabatan
- `GET /admin-univ-usulan/jabatan/{jabatan}/edit` - Edit Jabatan
- `PUT /admin-univ-usulan/jabatan/{jabatan}` - Update Jabatan
- `DELETE /admin-univ-usulan/jabatan/{jabatan}` - Delete Jabatan
- `GET /admin-univ-usulan/jabatan-export` - Export Jabatan

#### **Pangkat**
- `GET /admin-univ-usulan/pangkat` - Index Pangkat
- `POST /admin-univ-usulan/pangkat` - Store Pangkat
- `GET /admin-univ-usulan/pangkat/{pangkat}/edit` - Edit Pangkat
- `PUT /admin-univ-usulan/pangkat/{pangkat}` - Update Pangkat
- `DELETE /admin-univ-usulan/pangkat/{pangkat}` - Delete Pangkat

#### **Pusat Usulan**
- `GET /admin-univ-usulan/pusat-usulan` - Index Pusat Usulan
- `GET /admin-univ-usulan/pusat-usulan/{usulan}` - Show Pusat Usulan
- `POST /admin-univ-usulan/pusat-usulan/{usulan}/process` - Process Pusat Usulan
- `GET /admin-univ-usulan/pusat-usulan/{usulan}/dokumen/{field}` - Show Document

#### **Periode Usulan**
- `GET /admin-univ-usulan/periode-usulan` - Index Periode Usulan
- `POST /admin-univ-usulan/periode-usulan` - Store Periode Usulan
- `GET /admin-univ-usulan/periode-usulan/{periode_usulan}/edit` - Edit Periode Usulan
- `PUT /admin-univ-usulan/periode-usulan/{periode_usulan}` - Update Periode Usulan
- `DELETE /admin-univ-usulan/periode-usulan/{periode_usulan}` - Delete Periode Usulan
- `GET /admin-univ-usulan/periode-usulan/{periodeUsulan}/pendaftar` - Show Pendaftar

#### **Role Pegawai**
- `GET /admin-univ-usulan/role-pegawai` - Index Role Pegawai
- `GET /admin-univ-usulan/role-pegawai/{pegawai}/edit` - Edit Role Pegawai
- `PUT /admin-univ-usulan/role-pegawai/{pegawai}` - Update Role Pegawai

#### **Manajemen Akun Pegawai**
- `GET /admin-univ-usulan/pegawai` - Index Pegawai
- `GET /admin-univ-usulan/pegawai/{pegawai}/edit` - Edit Pegawai
- `PUT /admin-univ-usulan/pegawai/{pegawai}` - Update Pegawai

### **3. Pegawai UNMUL**
```php
Route::prefix('pegawai-unmul')
    ->name('pegawai-unmul.')
    ->group(function () {
        // Routes here
    });
```

**Available Routes:**

#### **Dashboard**
- `GET /pegawai-unmul/dashboard` - Dashboard Pegawai

#### **Profile**
- `GET /pegawai-unmul/profil` - Show Profile
- `GET /pegawai-unmul/profil/edit` - Edit Profile
- `PUT /pegawai-unmul/profil` - Update Profile
- `GET /pegawai-unmul/profil/dokumen/{field}` - Show Document

#### **Usulan**
- `GET /pegawai-unmul/usulan-saya` - Dashboard Usulan
- `GET /pegawai-unmul/usulan-create` - Create Usulan Selector
- `GET /pegawai-unmul/usulan/api/statistics` - Get Statistics (API)

#### **Usulan Jabatan**
- `GET /pegawai-unmul/usulan-jabatan` - Index Usulan Jabatan
- `GET /pegawai-unmul/usulan-jabatan/create` - Create Usulan Jabatan
- `POST /pegawai-unmul/usulan-jabatan` - Store Usulan Jabatan
- `GET /pegawai-unmul/usulan-jabatan/{usulan}/edit` - Edit Usulan Jabatan
- `PUT /pegawai-unmul/usulan-jabatan/{usulan}` - Update Usulan Jabatan
- `DELETE /pegawai-unmul/usulan-jabatan/{usulanJabatan}` - Delete Usulan Jabatan
- `GET /pegawai-unmul/usulan-jabatan/{usulanJabatan}/dokumen/{field}` - Show Document
- `GET /pegawai-unmul/usulan-jabatan/{usulanJabatan}/logs` - Get Logs (API)

#### **Legacy Compatibility**
- `GET /pegawai-unmul/usulan-saya/{usulan}/dokumen/{field}` - Redirect to new route
- `GET /pegawai-unmul/usulan/{usulan}/logs` - Redirect to new route

### **4. Admin Fakultas**
```php
Route::prefix('admin-fakultas')
    ->name('admin-fakultas.')
    ->middleware(['role:Admin Fakultas'])
    ->group(function () {
        // Routes here
    });
```

**Available Routes:**

#### **Dashboard**
- `GET /admin-fakultas/dashboard` - Dashboard Admin Fakultas

#### **Usulan**
- `GET /admin-fakultas/usulan/{adminUsulan}` - Show Usulan
- `POST /admin-fakultas/usulan/{adminUsulan}/validasi` - Save Validation
- `POST /admin-fakultas/usulan/{usulan}/autosave` - Autosave Validation

#### **Document Viewing**
- `GET /admin-fakultas/usulan/{usulan}/dokumen/{field}` - Show Usulan Document
- `GET /admin-fakultas/usulan/{usulan}/profil-dokumen/{field}` - Show Pegawai Document
- `GET /admin-fakultas/usulan/{usulan}/pendukung-dokumen/{field}` - Show Pendukung Document

#### **Periode**
- `GET /admin-fakultas/periode/{periodeUsulan}/pendaftar` - Show Pendaftar

#### **Debug (Development Only)**
- `GET /admin-fakultas/test-dokumen/{usulan}/{field}` - Test Document Route

### **5. Penilai Universitas**
```php
Route::prefix('penilai-universitas')
    ->name('penilai-universitas.')
    ->middleware(['role:Penilai Universitas'])
    ->group(function () {
        // Routes here
    });
```

**Available Routes:**

#### **Dashboard**
- `GET /penilai-universitas/dashboard` - Dashboard Penilai

#### **Pusat Usulan**
- `GET /penilai-universitas/pusat-usulan` - Index Pusat Usulan
- `GET /penilai-universitas/pusat-usulan/{usulan}` - Show Pusat Usulan
- `POST /penilai-universitas/pusat-usulan/{usulan}/process` - Process Pusat Usulan

## 🔗 **Route Model Binding**

### **Usulan Jabatan**
```php
Route::bind('usulanJabatan', function ($value) {
    $usulan = \App\Models\BackendUnivUsulan\Usulan::where('id', $value)->first();
    
    if (!$usulan) {
        abort(404);
    }
    
    // Check ownership untuk pegawai routes
    if (request()->is('pegawai-unmul/*') && $usulan->pegawai_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki akses untuk usulan ini.');
    }
    
    return $usulan;
});
```

### **Admin Usulan**
```php
Route::bind('adminUsulan', function ($value) {
    return \App\Models\BackendUnivUsulan\Usulan::findOrFail($value);
});
```

### **Generic Usulan**
```php
Route::bind('usulan', function ($value) {
    $usulan = \App\Models\BackendUnivUsulan\Usulan::where('id', $value)->first();
    
    if (!$usulan) {
        abort(404);
    }
    
    // For pegawai routes, check ownership
    if (request()->is('pegawai-unmul/*')) {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk usulan ini.');
        }
    }
    
    return $usulan;
});
```

## 🐛 **Development Debugging Routes**

Routes ini hanya tersedia di environment `local`:

```php
Route::prefix('debug')->middleware(['auth:pegawai'])->name('debug.')->group(function () {
    Route::get('/routes', function () {
        // Return all routes as JSON
    })->name('routes');
    
    Route::get('/user', function () {
        // Return user info as JSON
    })->name('user');
});
```

## 📊 **Route Naming Convention**

### **Prefix Pattern**
- `admin-universitas.*` - Admin Universitas routes
- `backend.admin-univ-usulan.*` - Admin Univ Usulan routes
- `pegawai-unmul.*` - Pegawai UNMUL routes
- `admin-fakultas.*` - Admin Fakultas routes
- `penilai-universitas.*` - Penilai Universitas routes

### **Naming Examples**
- `admin-universitas.dashboard` - Dashboard Admin Universitas
- `backend.admin-univ-usulan.dashboard` - Dashboard Admin Univ Usulan
- `pegawai-unmul.dashboard-pegawai-unmul` - Dashboard Pegawai
- `admin-fakultas.dashboard` - Dashboard Admin Fakultas
- `penilai-universitas.dashboard-penilai-universitas` - Dashboard Penilai

## 🔒 **Security Features**

### **Authentication**
- Semua route dilindungi dengan `auth:pegawai` middleware
- Login/logout routes menggunakan `guest:pegawai` middleware

### **Authorization**
- Role-based middleware untuk setiap role
- Ownership check untuk pegawai routes
- Model binding dengan permission checks

### **CSRF Protection**
- Semua POST/PUT/DELETE routes dilindungi CSRF token
- Token otomatis disertakan dalam form

## 🚀 **Best Practices**

### **1. Route Organization**
- Route dikelompokkan berdasarkan role
- Sub-grouping untuk fitur yang terkait
- Clear naming convention

### **2. Middleware Usage**
- Authentication middleware di level group
- Role middleware di level prefix
- Custom middleware untuk specific needs

### **3. Model Binding**
- Custom binding untuk security
- Ownership checks untuk user data
- Proper error handling

### **4. API Routes**
- Separate API routes untuk AJAX calls
- JSON responses untuk API endpoints
- Proper HTTP status codes

## 📝 **Usage Examples**

### **Generating URLs**
```php
// Dashboard routes
route('admin-universitas.dashboard')
route('backend.admin-univ-usulan.dashboard')
route('pegawai-unmul.dashboard-pegawai-unmul')
route('admin-fakultas.dashboard')
route('penilai-universitas.dashboard-penilai-universitas')

// Resource routes
route('backend.admin-univ-usulan.data-pegawai.index')
route('backend.admin-univ-usulan.data-pegawai.show', $pegawai)
route('backend.admin-univ-usulan.data-pegawai.edit', $pegawai)

// Custom routes
route('pegawai-unmul.usulan-jabatan.show-document', [$usulan, 'pakta_integritas'])
route('admin-fakultas.usulan.show-document', [$usulan, 'artikel'])
```

### **Redirecting**
```php
// Redirect to dashboard based on role
return redirect()->route('admin-universitas.dashboard');

// Redirect with parameters
return redirect()->route('pegawai-unmul.usulan-jabatan.show-document', [$usulan, 'dokumen']);

// Redirect with flash message
return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
    ->with('success', 'Data berhasil disimpan');
```

---

**Terakhir diperbarui**: {{ date('Y-m-d H:i:s') }}
**Versi**: 2.0.0
**Status**: ✅ Production Ready - Restructured
