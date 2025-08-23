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
Route::prefix('kepegawaian-universitas')
    ->name('backend.kepegawaian-universitas.')
    ->middleware(['role:Admin Universitas Usulan'])
    ->group(function () {
        // Routes here
    });
```

**Available Routes:**

#### **Dashboard**
- `GET /kepegawaian-universitas/dashboard` - Dashboard Admin Univ Usulan

#### **Master Data**
- `GET /kepegawaian-universitas/data-pegawai` - Index Data Pegawai
- `POST /kepegawaian-universitas/data-pegawai` - Store Data Pegawai
- `GET /kepegawaian-universitas/data-pegawai/{pegawai}/edit` - Edit Data Pegawai
- `PUT /kepegawaian-universitas/data-pegawai/{pegawai}` - Update Data Pegawai
- `DELETE /kepegawaian-universitas/data-pegawai/{pegawai}` - Delete Data Pegawai
- `GET /kepegawaian-universitas/data-pegawai/{pegawai}/dokumen/{field}` - Show Document

#### **Unit Kerja**
- `GET /kepegawaian-universitas/unitkerja` - Index Unit Kerja
- `POST /kepegawaian-universitas/unitkerja` - Store Unit Kerja
- `GET /kepegawaian-universitas/unitkerja/{unitkerja}/edit` - Edit Unit Kerja
- `PUT /kepegawaian-universitas/unitkerja/{unitkerja}` - Update Unit Kerja
- `DELETE /kepegawaian-universitas/unitkerja/{unitkerja}` - Delete Unit Kerja

#### **Sub Unit Kerja**
- `GET /kepegawaian-universitas/sub-unitkerja` - Index Sub Unit Kerja
- `POST /kepegawaian-universitas/sub-unitkerja` - Store Sub Unit Kerja
- `GET /kepegawaian-universitas/sub-unitkerja/{sub_unitkerja}/edit` - Edit Sub Unit Kerja
- `PUT /kepegawaian-universitas/sub-unitkerja/{sub_unitkerja}` - Update Sub Unit Kerja
- `DELETE /kepegawaian-universitas/sub-unitkerja/{sub_unitkerja}` - Delete Sub Unit Kerja

#### **Sub Sub Unit Kerja**
- `GET /kepegawaian-universitas/sub-sub-unitkerja` - Index Sub Sub Unit Kerja
- `POST /kepegawaian-universitas/sub-sub-unitkerja` - Store Sub Sub Unit Kerja
- `GET /kepegawaian-universitas/sub-sub-unitkerja/{sub_sub_unitkerja}/edit` - Edit Sub Sub Unit Kerja
- `PUT /kepegawaian-universitas/sub-sub-unitkerja/{sub_sub_unitkerja}` - Update Sub Sub Unit Kerja
- `DELETE /kepegawaian-universitas/sub-sub-unitkerja/{sub_sub_unitkerja}` - Delete Sub Sub Unit Kerja
- `GET /kepegawaian-universitas/get-sub-unit-kerjas` - Get Sub Unit Kerjas (API)

#### **Jabatan**
- `GET /kepegawaian-universitas/jabatan` - Index Jabatan
- `POST /kepegawaian-universitas/jabatan` - Store Jabatan
- `GET /kepegawaian-universitas/jabatan/{jabatan}/edit` - Edit Jabatan
- `PUT /kepegawaian-universitas/jabatan/{jabatan}` - Update Jabatan
- `DELETE /kepegawaian-universitas/jabatan/{jabatan}` - Delete Jabatan
- `GET /kepegawaian-universitas/jabatan-export` - Export Jabatan

#### **Pangkat**
- `GET /kepegawaian-universitas/pangkat` - Index Pangkat
- `POST /kepegawaian-universitas/pangkat` - Store Pangkat
- `GET /kepegawaian-universitas/pangkat/{pangkat}/edit` - Edit Pangkat
- `PUT /kepegawaian-universitas/pangkat/{pangkat}` - Update Pangkat
- `DELETE /kepegawaian-universitas/pangkat/{pangkat}` - Delete Pangkat

#### **Pusat Usulan**
- `GET /kepegawaian-universitas/pusat-usulan` - Index Pusat Usulan
- `GET /kepegawaian-universitas/pusat-usulan/{usulan}` - Show Pusat Usulan
- `POST /kepegawaian-universitas/pusat-usulan/{usulan}/process` - Process Pusat Usulan
- `GET /kepegawaian-universitas/pusat-usulan/{usulan}/dokumen/{field}` - Show Document

#### **Periode Usulan**
- `GET /kepegawaian-universitas/periode-usulan` - Index Periode Usulan
- `POST /kepegawaian-universitas/periode-usulan` - Store Periode Usulan
- `GET /kepegawaian-universitas/periode-usulan/{periode_usulan}/edit` - Edit Periode Usulan
- `PUT /kepegawaian-universitas/periode-usulan/{periode_usulan}` - Update Periode Usulan
- `DELETE /kepegawaian-universitas/periode-usulan/{periode_usulan}` - Delete Periode Usulan
- `GET /kepegawaian-universitas/periode-usulan/{periodeUsulan}/pendaftar` - Show Pendaftar

#### **Role Pegawai**
- `GET /kepegawaian-universitas/role-pegawai` - Index Role Pegawai
- `GET /kepegawaian-universitas/role-pegawai/{pegawai}/edit` - Edit Role Pegawai
- `PUT /kepegawaian-universitas/role-pegawai/{pegawai}` - Update Role Pegawai

#### **Manajemen Akun Pegawai**
- `GET /kepegawaian-universitas/pegawai` - Index Pegawai
- `GET /kepegawaian-universitas/pegawai/{pegawai}/edit` - Edit Pegawai
- `PUT /kepegawaian-universitas/pegawai/{pegawai}` - Update Pegawai

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
- `backend.kepegawaian-universitas.*` - Admin Univ Usulan routes
- `pegawai-unmul.*` - Pegawai UNMUL routes
- `admin-fakultas.*` - Admin Fakultas routes
- `penilai-universitas.*` - Penilai Universitas routes

### **Naming Examples**
- `admin-universitas.dashboard` - Dashboard Admin Universitas
- `backend.kepegawaian-universitas.dashboard` - Dashboard Admin Univ Usulan
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
route('backend.kepegawaian-universitas.dashboard')
route('pegawai-unmul.dashboard-pegawai-unmul')
route('admin-fakultas.dashboard')
route('penilai-universitas.dashboard-penilai-universitas')

// Resource routes
route('backend.kepegawaian-universitas.data-pegawai.index')
route('backend.kepegawaian-universitas.data-pegawai.show', $pegawai)
route('backend.kepegawaian-universitas.data-pegawai.edit', $pegawai)

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
return redirect()->route('backend.kepegawaian-universitas.data-pegawai.index')
    ->with('success', 'Data berhasil disimpan');
```

---

**Terakhir diperbarui**: {{ date('Y-m-d H:i:s') }}
**Versi**: 2.0.0
**Status**: ✅ Production Ready - Restructured
