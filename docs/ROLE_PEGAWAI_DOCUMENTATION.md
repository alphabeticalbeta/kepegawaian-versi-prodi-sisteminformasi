# Role Pegawai Documentation

## 🎯 **Overview Role Pegawai**

Sistem kepegawaian UNMUL sudah memiliki role "Pegawai Unmul" yang merupakan role utama untuk pegawai biasa (non-admin). Role ini memberikan akses terbatas sesuai dengan kebutuhan pegawai.

## 📊 **Struktur Role yang Tersedia**

### **1. Role Utama:**
- **Admin Universitas Usulan** - Super admin dengan akses penuh
- **Admin Universitas** - Admin tingkat universitas
- **Admin Fakultas** - Admin tingkat fakultas
- **Penilai Universitas** - Penilai untuk usulan jabatan
- **Pegawai Unmul** - Role untuk pegawai biasa

### **2. Role Pegawai Unmul:**
Role "Pegawai Unmul" adalah role utama untuk pegawai biasa dengan fitur:

#### **Akses yang Diberikan:**
- ✅ **View Own Documents** - Melihat dokumen pribadi
- ✅ **Profile Management** - Mengelola profil pribadi
- ✅ **Usulan Submission** - Mengajukan usulan jabatan
- ✅ **Document Upload** - Upload dokumen pribadi
- ✅ **Status Tracking** - Melihat status usulan

#### **Akses yang Dibatasi:**
- ❌ **View All Documents** - Tidak bisa melihat dokumen pegawai lain
- ❌ **Admin Functions** - Tidak bisa mengakses fungsi admin
- ❌ **User Management** - Tidak bisa mengelola user lain
- ❌ **System Settings** - Tidak bisa mengubah pengaturan sistem

## 🔧 **Implementasi Role Pegawai**

### **1. Role Definition (RoleSeeder.php):**
```php
// Role sudah didefinisikan di database/seeders/RoleSeeder.php
$roles = [
    'Admin Universitas Usulan',
    'Admin Universitas', 
    'Admin Fakultas',
    'Penilai Universitas',
    'Pegawai Unmul' // Role untuk pegawai biasa
];

// Permission untuk Pegawai Unmul
$permissions = [
    'view_own_documents', // Akses dokumen pribadi
];

// Assign permission ke role
$pegawaiUnmul = Role::where('name', 'Pegawai Unmul')->first();
if ($pegawaiUnmul) {
    $pegawaiUnmul->givePermissionTo('view_own_documents');
}
```

### **2. User Creation (PegawaiSeeder.php):**
```php
// Data pegawai dengan role Pegawai Unmul
$pegawais = [
    [
        'nip' => '199001012015011001',
        'nama_lengkap' => 'Budi Santoso',
        'email' => 'budi.santoso@unmul.ac.id',
        'jenis_pegawai' => 'Dosen',
        'status_kepegawaian' => 'Dosen PNS',
        'is_admin' => false,
        'roles' => ['Pegawai Unmul']
    ],
    [
        'nip' => '199202022016022002',
        'nama_lengkap' => 'Citra Lestari',
        'email' => 'citra.lestari@unmul.ac.id',
        'jenis_pegawai' => 'Tenaga Kependidikan',
        'status_kepegawaian' => 'Tenaga Kependidikan PNS',
        'is_admin' => false,
        'roles' => ['Pegawai Unmul']
    ],
    // ... lebih banyak data pegawai
];

// Assign role saat membuat user
foreach ($roles as $role) {
    $pegawai->assignRole($role);
}
```

### **3. Authentication Guard:**
```php
// config/auth.php
'guards' => [
    'pegawai' => [
        'driver' => 'session',
        'provider' => 'pegawais',
    ],
],

'providers' => [
    'pegawais' => [
        'driver' => 'eloquent',
        'model' => App\Models\BackendUnivUsulan\Pegawai::class,
    ],
],
```

## 🎯 **Fitur Role Pegawai**

### **1. Dashboard Pegawai:**
- **Overview Profile** - Ringkasan profil pribadi
- **Recent Activities** - Aktivitas terbaru
- **Quick Actions** - Aksi cepat (edit profil, ajukan usulan)
- **Notifications** - Notifikasi status usulan

### **2. Profile Management:**
- **Personal Data** - Data pribadi
- **Employment Data** - Data kepegawaian
- **Dosen Data** - Data khusus dosen (jika dosen)
- **Documents** - Dokumen pribadi

### **3. Usulan Management:**
- **Create Usulan** - Membuat usulan jabatan baru
- **View Usulan** - Melihat usulan yang sudah dibuat
- **Track Status** - Melihat status usulan
- **Edit Usulan** - Mengedit usulan (jika masih draft)

### **4. Document Management:**
- **Upload Documents** - Upload dokumen pribadi
- **View Documents** - Melihat dokumen yang sudah diupload
- **Download Documents** - Download dokumen pribadi
- **Document History** - Riwayat dokumen

## 🔐 **Security & Permissions**

### **1. Route Protection:**
```php
// routes/backend.php
Route::middleware(['auth:pegawai', 'role:Pegawai Unmul'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('profile', ProfileController::class);
    Route::resource('usulan', UsulanController::class);
});
```

### **2. Controller Protection:**
```php
// Controllers menggunakan middleware
public function __construct()
{
    $this->middleware('auth:pegawai');
    $this->middleware('role:Pegawai Unmul');
}
```

### **3. View Protection:**
```php
// Blade templates menggunakan directives
@role('Pegawai Unmul')
    {{-- Content hanya untuk pegawai --}}
@endrole

@can('view_own_documents')
    {{-- Content untuk yang punya permission --}}
@endcan
```

## 📋 **Data Pegawai yang Tersedia**

### **1. Dosen:**
- **Budi Santoso** - Dosen PNS
- **Ahmad Fauzi** - Dosen PNS
- **Rizki Pratama** - Dosen PPPK
- **Hendra Wijaya** - Dosen Non ASN
- **Doni Kusuma** - Dosen PNS

### **2. Tenaga Kependidikan:**
- **Citra Lestari** - Tenaga Kependidikan PNS
- **Siti Nurhaliza** - Tenaga Kependidikan PNS
- **Dewi Sartika** - Tenaga Kependidikan PPPK
- **Maya Indah** - Tenaga Kependidikan Non ASN

### **3. Login Credentials:**
```
Username: NIP (contoh: 199001012015011001)
Password: NIP (contoh: 199001012015011001)
```

## 🛠️ **Setup Instructions**

### **1. Jalankan Seeder:**
```bash
# Jalankan role seeder
php artisan db:seed --class=RoleSeeder

# Jalankan pegawai seeder
php artisan db:seed --class=PegawaiSeeder
```

### **2. Verifikasi Role:**
```bash
# Cek role yang tersedia
php artisan tinker
>>> Spatie\Permission\Models\Role::all()->pluck('name')
```

### **3. Test Login:**
```bash
# Test login dengan salah satu pegawai
Username: 199001012015011001
Password: 199001012015011001
```

## 🎯 **Workflow Pegawai**

### **1. Login & Dashboard:**
1. Login dengan NIP dan password
2. Akses dashboard pegawai
3. Lihat overview profil dan aktivitas

### **2. Profile Management:**
1. Edit data pribadi
2. Update data kepegawaian
3. Upload dokumen pribadi
4. Simpan perubahan

### **3. Usulan Submission:**
1. Buat usulan jabatan baru
2. Isi form usulan
3. Upload dokumen pendukung
4. Submit usulan
5. Track status usulan

### **4. Document Management:**
1. Upload dokumen baru
2. View dokumen yang sudah ada
3. Download dokumen
4. Update dokumen jika diperlukan

## 🔧 **Customization Options**

### **1. Tambah Permission Baru:**
```php
// Di RoleSeeder.php
$newPermissions = [
    'edit_own_profile',
    'submit_usulan',
    'view_own_usulan_status'
];

foreach ($newPermissions as $permission) {
    Permission::firstOrCreate([
        'name' => $permission,
        'guard_name' => 'pegawai'
    ]);
}

// Assign ke role Pegawai Unmul
$pegawaiUnmul->givePermissionTo($newPermissions);
```

### **2. Tambah Pegawai Baru:**
```php
// Di PegawaiSeeder.php atau buat seeder baru
$newPegawai = [
    'nip' => '202412012024121001',
    'nama_lengkap' => 'Nama Pegawai Baru',
    'email' => 'pegawai.baru@unmul.ac.id',
    'jenis_pegawai' => 'Dosen',
    'status_kepegawaian' => 'Dosen PNS',
    'is_admin' => false,
    'roles' => ['Pegawai Unmul']
];
```

### **3. Custom Middleware:**
```php
// Buat middleware khusus untuk pegawai
php artisan make:middleware PegawaiMiddleware

// Implementasi middleware
public function handle($request, Closure $next)
{
    if (!auth()->guard('pegawai')->check()) {
        return redirect()->route('login');
    }
    
    if (!auth()->user()->hasRole('Pegawai Unmul')) {
        abort(403, 'Unauthorized');
    }
    
    return $next($request);
}
```

## 📊 **Monitoring & Analytics**

### **1. User Statistics:**
- Total pegawai dengan role Pegawai Unmul
- Breakdown berdasarkan jenis pegawai
- Breakdown berdasarkan status kepegawaian
- Aktivitas login pegawai

### **2. Usulan Statistics:**
- Total usulan yang diajukan
- Status usulan (draft, submitted, approved, rejected)
- Rata-rata waktu proses usulan
- Top usulan berdasarkan jenis

### **3. Document Statistics:**
- Total dokumen yang diupload
- Breakdown berdasarkan jenis dokumen
- Dokumen yang expired
- Dokumen yang perlu update

## 🔄 **Maintenance & Updates**

### **1. Regular Updates:**
- Update data pegawai secara berkala
- Review dan update permissions
- Backup data pegawai
- Monitor aktivitas mencurigakan

### **2. Security Updates:**
- Update password policy
- Review access logs
- Update security patches
- Monitor failed login attempts

### **3. Feature Updates:**
- Tambah fitur baru sesuai kebutuhan
- Update UI/UX berdasarkan feedback
- Optimize performance
- Add new integrations

---

*Role Pegawai Unmul sudah siap digunakan dengan fitur lengkap untuk manajemen profil, usulan, dan dokumen pribadi.*
