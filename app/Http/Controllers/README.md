# Dokumentasi Controller Backend - Kepegawaian UNMUL

## 📋 **Struktur Controller**

Controller backend diorganisir berdasarkan role dan fitur untuk memudahkan maintenance dan pengembangan.

## 🏗️ **Arsitektur Controller**

### **1. Base Controller**
Semua controller extends dari `App\Http\Controllers\Controller` yang menyediakan:
- Authentication middleware
- Common helper methods
- Error handling
- Response formatting

### **2. Role-Based Controllers**
Controller dikelompokkan berdasarkan role pengguna:
- `AdminUniversitas/` - Controller untuk Admin Universitas
- `AdminUnivUsulan/` - Controller untuk Admin Universitas Usulan
- `AdminFakultas/` - Controller untuk Admin Fakultas
- `PegawaiUnmul/` - Controller untuk Pegawai UNMUL
- `PenilaiUniversitas/` - Controller untuk Penilai Universitas

## 👥 **Controller per Role**

### **1. AdminUniversitas Controllers**

#### **DashboardController**
```php
namespace App\Http\Controllers\Backend\AdminUniversitas;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics for dashboard
        $statistics = $this->getDashboardStatistics();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get chart data
        $chartData = $this->getChartData();

        return view('backend.layouts.views.admin-universitas.dashboard', [
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData,
            'user' => Auth::user()
        ]);
    }
}
```

**Features:**
- Dashboard statistics (total pegawai, usulan, periode aktif)
- Recent activities tracking
- Chart data for analytics
- User-specific data

### **2. AdminUnivUsulan Controllers**

#### **DashboardController**
```php
namespace App\Http\Controllers\Backend\AdminUnivUsulan;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics for dashboard
        $statistics = $this->getDashboardStatistics();
        
        // Get recent usulan activities
        $recentUsulans = $this->getRecentUsulans();
        
        // Get chart data
        $chartData = $this->getChartData();
        
        // Get quick actions data
        $quickActions = $this->getQuickActions();

        return view('backend.layouts.views.admin-univ-usulan.dashboard', [
            'statistics' => $statistics,
            'recentUsulans' => $recentUsulans,
            'chartData' => $chartData,
            'quickActions' => $quickActions,
            'user' => Auth::user()
        ]);
    }
}
```

#### **Master Data Controllers**
- **DataPegawaiController** - CRUD untuk data pegawai
- **UnitKerjaController** - CRUD untuk unit kerja
- **SubUnitKerjaController** - CRUD untuk sub unit kerja
- **SubSubUnitKerjaController** - CRUD untuk sub-sub unit kerja
- **JabatanController** - CRUD untuk jabatan
- **PangkatController** - CRUD untuk pangkat
- **RolePegawaiController** - Manajemen role pegawai
- **PegawaiController** - Manajemen akun pegawai

#### **PusatUsulanController**
```php
class PusatUsulanController extends Controller
{
    public function index()
    {
        // Get periode usulan with statistics
        $periodeUsulans = PeriodeUsulan::withCount('usulans')
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.admin-univ-usulan.pusat-usulan.index', [
            'periodeUsulans' => $periodeUsulans
        ]);
    }

    public function show(Usulan $usulan)
    {
        // Get usulan details with relationships
        $usulan->load(['pegawai', 'periodeUsulan', 'jabatan']);
        
        return view('backend.layouts.views.admin-univ-usulan.pusat-usulan.detail-usulan', [
            'usulan' => $usulan,
            'canEdit' => $this->canEditUsulan($usulan),
            'validationFields' => $this->getValidationFields($usulan)
        ]);
    }
}
```

#### **PeriodeUsulanController**
```php
class PeriodeUsulanController extends Controller
{
    public function create(Request $request)
    {
        $jenisUsulan = $request->query('jenis', 'jabatan');

        $viewMapping = [
            'usulan-jabatan-dosen' => 'backend.layouts.views.periode-usulan.form-jabatan-dosen',
            'usulan-jabatan-tendik' => 'backend.layouts.views.periode-usulan.form-jabatan-tendik',
        ];

        $view = $viewMapping[$jenisUsulan] ?? 'backend.layouts.views.periode-usulan.form';

        return view($view, [
            'jenis_usulan_otomatis' => $jenisUsulan
        ]);
    }
}
```

### **3. PegawaiUnmul Controllers**

#### **DashboardController**
```php
namespace App\Http\Controllers\Backend\PegawaiUnmul;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawaiId = Auth::id();
        
        // Get pegawai data
        $pegawai = Pegawai::findOrFail($pegawaiId);
        
        // Get usulan statistics
        $usulanStats = $this->getUsulanStatistics($pegawaiId);
        
        // Get recent usulans
        $recentUsulans = $this->getRecentUsulans($pegawaiId);
        
        // Get active periods
        $activePeriods = $this->getActivePeriods();
        
        // Get chart data
        $chartData = $this->getChartData($pegawaiId);

        return view('backend.layouts.views.pegawai-unmul.dashboard', [
            'pegawai' => $pegawai,
            'usulanStats' => $usulanStats,
            'recentUsulans' => $recentUsulans,
            'activePeriods' => $activePeriods,
            'chartData' => $chartData,
            'user' => Auth::user()
        ]);
    }
}
```

#### **ProfileController**
```php
class ProfileController extends Controller
{
    public function show()
    {
        $pegawai = Auth::user();
        $documentFields = $this->getDocumentFields();

        return view('backend.layouts.views.pegawai-unmul.profile.show', compact(
            'pegawai',
            'documentFields',
            'user'
        ));
    }
}
```

#### **UsulanPegawaiController**
```php
class UsulanPegawaiController extends Controller
{
    public function index()
    {
        $pegawaiId = Auth::id();
        
        // Get usulan statistics
        $statistics = $this->getUsulanStatistics($pegawaiId);
        
        // Get usulans for this pegawai
        $usulans = Usulan::where('pegawai_id', $pegawaiId)
            ->with(['periodeUsulan', 'jabatan'])
            ->latest()
            ->get();

        return view('backend.layouts.views.pegawai-unmul.usulan.dashboard', [
            'usulans' => $usulans,
            'statistics' => $statistics,
            'user' => Auth::user()
        ]);
    }

    public function createUsulan()
    {
        $pegawai = Auth::user();
        $jenisUsulanOptions = $this->getJenisUsulanOptions($pegawai);

        return view('backend.layouts.views.pegawai-unmul.usulan.selector', [
            'pegawai' => $pegawai,
            'jenisUsulanOptions' => $jenisUsulanOptions,
            'user' => Auth::user()
        ]);
    }
}
```

#### **UsulanJabatanController**
```php
class UsulanJabatanController extends Controller
{
    public function index()
    {
        $pegawaiId = Auth::id();
        
        $usulans = Usulan::where('pegawai_id', $pegawaiId)
            ->with(['periodeUsulan', 'jabatan'])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.pegawai-unmul.usulan-jabatan.index', compact('usulans'));
    }

    public function create()
    {
        $pegawai = Auth::user();
        
        // Get available periods
        $daftarPeriode = $this->getAvailablePeriods($pegawai);
        
        // Get jabatan tujuan
        $jabatanTujuan = $this->getJabatanTujuan($pegawai);
        
        // Get form configuration
        $formConfig = $this->getFormConfig($pegawai);

        return view('backend.layouts.views.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'formConfig' => $formConfig,
            'user' => Auth::user()
        ]);
    }
}
```

### **4. AdminFakultas Controllers**

#### **DashboardController**
```php
namespace App\Http\Controllers\Backend\AdminFakultas;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get faculty-specific data
        $facultyData = $this->getFacultyData($user);
        
        // Get usulan statistics for faculty
        $usulanStats = $this->getUsulanStatistics($user);
        
        // Get recent usulans for faculty
        $recentUsulans = $this->getRecentUsulans($user);
        
        // Get chart data
        $chartData = $this->getChartData($user);

        return view('backend.layouts.views.admin-fakultas.dashboard', [
            'facultyData' => $facultyData,
            'usulanStats' => $usulanStats,
            'recentUsulans' => $recentUsulans,
            'chartData' => $chartData,
            'user' => $user
        ]);
    }
}
```

#### **AdminFakultasController**
```php
class AdminFakultasController extends Controller
{
    public function dashboard()
    {
        $admin = Auth::user();
        $unitKerja = $this->getAdminUnitKerja($admin);
        $periodeUsulans = $this->getPeriodeUsulanWithCount($unitKerja);
        $statistics = $this->getDashboardStatistics($periodeUsulans, $unitKerja);

        return view('backend.layouts.views.admin-fakultas.dashboard', compact('periodeUsulans', 'unitKerja', 'statistics'));
    }

    public function indexUsulanJabatan()
    {
        $admin = Auth::user()->load('unitKerjaPengelola');
        $unitKerja = $admin->unitKerjaPengelola;

        $periodeUsulans = PeriodeUsulan::query()
            ->where('jenis_usulan', 'jabatan')
            ->withCount([
                'usulans as jumlah_pengusul' => function ($query) use ($unitKerja) {
                    $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                            $subQuery->where('id', $unitKerja->id);
                        });
                }
            ])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.admin-fakultas.usulan.index', compact('periodeUsulans', 'unitKerja'));
    }
}
```

### **5. PenilaiUniversitas Controllers**

#### **DashboardController**
```php
namespace App\Http\Controllers\Backend\PenilaiUniversitas;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get assessment statistics
        $assessmentStats = $this->getAssessmentStatistics();
        
        // Get recent assessments
        $recentAssessments = $this->getRecentAssessments();
        
        // Get pending assessments
        $pendingAssessments = $this->getPendingAssessments();
        
        // Get chart data
        $chartData = $this->getChartData();

        return view('backend.layouts.views.penilai-universitas.dashboard', [
            'assessmentStats' => $assessmentStats,
            'recentAssessments' => $recentAssessments,
            'pendingAssessments' => $pendingAssessments,
            'chartData' => $chartData,
            'user' => $user
        ]);
    }
}
```

#### **PusatUsulanController**
```php
class PusatUsulanController extends Controller
{
    public function index()
    {
        $query = Usulan::with(['pegawai', 'periodeUsulan', 'jabatan'])
            ->where('status', 'forwarded')
            ->latest();

        $usulans = $query->paginate(15);

        return view('backend.layouts.views.penilai-universitas.pusat-usulan.index', compact('usulans'));
    }

    public function show(Usulan $usulan)
    {
        $validationFields = $this->getValidationFields($usulan);

        return view('backend.layouts.views.penilai-universitas.pusat-usulan.detail-usulan', [
            'usulan' => $usulan,
            'validationFields' => $validationFields,
            'user' => Auth::user()
        ]);
    }
}
```

## 🔧 **Common Patterns**

### **1. Dashboard Pattern**
```php
public function index()
{
    // Get user data
    $user = Auth::user();
    
    // Get role-specific statistics
    $statistics = $this->getStatistics($user);
    
    // Get recent activities
    $recentActivities = $this->getRecentActivities($user);
    
    // Get chart data
    $chartData = $this->getChartData($user);

    return view('backend.layouts.views.{role}.dashboard', [
        'statistics' => $statistics,
        'recentActivities' => $recentActivities,
        'chartData' => $chartData,
        'user' => $user
    ]);
}
```

### **2. CRUD Pattern**
```php
public function index()
{
    $items = Model::with('relationships')
        ->filter($request)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('backend.layouts.views.{role}.{feature}.index', compact('items'));
}

public function create()
{
    $formData = $this->getFormData();
    
    return view('backend.layouts.views.{role}.{feature}.create', compact('formData'));
}

public function store(Request $request)
{
    $validated = $request->validate($this->getValidationRules());
    
    Model::create($validated);
    
    return redirect()->route('{role}.{feature}.index')
        ->with('success', 'Data berhasil ditambahkan.');
}

public function edit(Model $item)
{
    $formData = $this->getFormData();
    
    return view('backend.layouts.views.{role}.{feature}.edit', compact('item', 'formData'));
}

public function update(Request $request, Model $item)
{
    $validated = $request->validate($this->getValidationRules($item->id));
    
    $item->update($validated);
    
    return redirect()->route('{role}.{feature}.index')
        ->with('success', 'Data berhasil diperbarui.');
}

public function destroy(Model $item)
{
    $item->delete();
    
    return redirect()->route('{role}.{feature}.index')
        ->with('success', 'Data berhasil dihapus.');
}
```

### **3. Authorization Pattern**
```php
public function show(Usulan $usulan)
{
    // Check ownership for pegawai routes
    if (request()->is('pegawai-unmul/*') && $usulan->pegawai_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki akses untuk usulan ini.');
    }
    
    // Check role permissions
    if (!Auth::user()->hasRole('Admin Fakultas')) {
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
    
    return view('backend.layouts.views.{role}.{feature}.show', compact('usulan'));
}
```

### **4. Data Processing Pattern**
```php
private function getStatistics($user)
{
    return [
        'total_items' => Model::count(),
        'pending_items' => Model::where('status', 'pending')->count(),
        'approved_items' => Model::where('status', 'approved')->count(),
        'rejected_items' => Model::where('status', 'rejected')->count(),
    ];
}

private function getRecentActivities($user)
{
    return Model::with('relationships')
        ->latest()
        ->take(10)
        ->get();
}

private function getChartData($user)
{
    // Monthly data
    $monthlyData = Model::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month')
        ->toArray();

    // Status distribution
    $statusData = Model::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    return [
        'monthly_data' => $monthlyData,
        'status_distribution' => $statusData,
    ];
}
```

## 🔒 **Security Features**

### **1. Authentication**
- All controllers use `auth:pegawai` middleware
- User data retrieved via `Auth::user()`
- Ownership checks for user-specific data

### **2. Authorization**
- Role-based access control using Spatie Permission
- Route-level middleware for role restrictions
- Method-level authorization checks

### **3. Input Validation**
- Comprehensive validation rules
- Custom validation classes
- Sanitization of user inputs

### **4. Data Protection**
- Ownership checks for user data
- Faculty-specific data filtering
- Proper error handling and logging

## 📊 **Performance Optimizations**

### **1. Database Queries**
- Eager loading with `with()` for relationships
- Query optimization with proper indexing
- Pagination for large datasets

### **2. Caching**
- Statistics caching for dashboard
- Form data caching
- Query result caching

### **3. Memory Management**
- Proper data pagination
- Efficient data processing
- Resource cleanup

## 🚀 **Best Practices**

### **1. Code Organization**
- Single responsibility principle
- Clear method naming
- Proper documentation

### **2. Error Handling**
- Try-catch blocks for database operations
- Proper error logging
- User-friendly error messages

### **3. Data Validation**
- Comprehensive validation rules
- Custom validation classes
- Input sanitization

### **4. Security**
- Authentication and authorization
- CSRF protection
- SQL injection prevention

### **5. Performance**
- Efficient database queries
- Proper caching strategies
- Resource optimization

## 📝 **Usage Examples**

### **Creating a New Controller**
```php
<?php

namespace App\Http\Controllers\Backend\{Role};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Model};

class {Feature}Controller extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $items = Model::with('relationships')
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.{role}.{feature}.index', [
            'items' => $items,
            'user' => $user
        ]);
    }

    public function create()
    {
        $formData = $this->getFormData();
        
        return view('backend.layouts.views.{role}.{feature}.create', [
            'formData' => $formData,
            'user' => Auth::user()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());
        
        Model::create($validated);
        
        return redirect()->route('{role}.{feature}.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    private function getFormData()
    {
        return [
            'options' => Option::all(),
            'categories' => Category::all(),
        ];
    }

    private function getValidationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
```

---

**Terakhir diperbarui**: {{ date('Y-m-d H:i:s') }}
**Versi**: 2.0.0
**Status**: ✅ Production Ready - Restructured
