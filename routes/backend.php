<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Backend\AdminFakultas\DashboardController as AdminFakultasDashboardController;
use App\Http\Controllers\Backend\AdminUniversitas\DashboardController as AdminUniversitasDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DashboardController as AdminUnivUsulanDashboardController;
use App\Http\Controllers\Backend\AdminUnivUsulan\DataPegawaiController;
use App\Http\Controllers\Backend\AdminUnivUsulan\JabatanController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PangkatController;
use App\Http\Controllers\Backend\AdminUnivUsulan\RolePegawaiController;
use App\Http\Controllers\Backend\AdminUnivUsulan\SubSubUnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\SubUnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\UnitKerjaController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PegawaiController; // Digunakan untuk manajemen akun
use App\Http\Controllers\Backend\PegawaiUnmul\DashboardController as PegawaiUnmulDashboardController;
use App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController as PenilaiUniversitasDashboardController;
use App\Http\Controllers\Backend\PegawaiUnmul\ProfileController as ProfileController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PeriodeUsulanController;
use App\Http\Controllers\Backend\AdminUnivUsulan\PusatUsulanController;
use App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController; // Pusat Controller
use App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController; // Jabatan Controller
use App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController;
use App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController as PenilaiPusatUsulanController;


// ------ RUTE HALAMAN LOGIN & LOGOUT ------//
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest:pegawai')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest:pegawai');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:pegawai')->name('logout');

// ======================================================================
// SEMUA RUTE DI BAWAH INI SEKARANG HANYA BISA DIAKSES SETELAH LOGIN
// ======================================================================
Route::middleware(['auth:pegawai'])->group(function () {

    // ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS ------//
    Route::prefix('admin-universitas')->name('admin-universitas.')->group(function () {
        Route::get('/dashboard', [AdminUniversitasDashboardController::class, 'index'])->name('dashboard-universitas');
    });

    // ------ RUTE HALAMAN BACKEND ADMIN UNIVERSITAS USULAN------//
    Route::prefix('admin-universitas-usulan')->middleware(['auth', 'role:Admin Universitas Usulan'])->name('backend.admin-univ-usulan.')->group(function () {
        Route::get('/dashboard', [AdminUnivUsulanDashboardController::class, 'index'])->name('dashboard');

        // Resource Routes untuk Master Data
        Route::resource('/data-pegawai', DataPegawaiController::class)
            ->parameters(['data-pegawai' => 'pegawai']);
        Route::resource('/unitkerja', UnitKerjaController::class);
        Route::resource('/sub-unitkerja', SubUnitKerjaController::class);
        Route::resource('/sub-sub-unitkerja', SubSubUnitKerjaController::class);
        Route::resource('/pangkat', PangkatController::class);
        Route::resource('/jabatan', JabatanController::class);
        Route::get('/jabatan-export', [JabatanController::class, 'export'])->name('jabatan.export');

        // Pusat Usulan Routes
        Route::get('/pusat-usulan', [PusatUsulanController::class, 'index'])->name('pusat-usulan.index');
        Route::get('/periode-usulan/{periodeUsulan}/pendaftar', [PusatUsulanController::class, 'showPendaftar'])->name('periode-usulan.pendaftar');
        Route::get('/pusat-usulan/{usulan}', [PusatUsulanController::class, 'show'])->name('pusat-usulan.show');
        Route::post('/pusat-usulan/{usulan}/process', [PusatUsulanController::class, 'process'])->name('pusat-usulan.process');
        Route::get('/usulan/{usulan}/dokumen/{field}', [PusatUsulanController::class, 'showUsulanDocument'])
            ->name('pusat-usulan.show-document');

        // Route untuk Manajemen Periode Usulan
        Route::resource('/periode-usulan', PeriodeUsulanController::class)
            ->parameters(['periode-usulan' => 'periode_usulan']);

        // Route untuk Manajemen Role Pegawai
        Route::get('/role-pegawai', [RolePegawaiController::class, 'index'])->name('role-pegawai.index');
        Route::get('/role-pegawai/{pegawai}/edit', [RolePegawaiController::class, 'edit'])->name('role-pegawai.edit');
        Route::put('/role-pegawai/{pegawai}', [RolePegawaiController::class, 'update'])->name('role-pegawai.update');

        // Route untuk Manajemen Akun Pegawai
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');

        // Custom Routes
        Route::get('/data-pegawai/{pegawai}/dokumen/{field}', [DataPegawaiController::class, 'showDocument'])->name('data-pegawai.show-document');
        Route::get('/get-sub-unit-kerjas', [SubSubUnitKerjaController::class, 'getSubUnitKerjas'])->name('get-sub-unit-kerjas');
    });

    // ------ RUTE HALAMAN BACKEND USUL PEGAWAI UNMUL ------//
    Route::prefix('pegawai-unmul')->name('pegawai-unmul.')->group(function () {
        Route::get('/dashboard', [PegawaiUnmulDashboardController::class, 'index'])->name('dashboard-pegawai-unmul');

        // Profile Routes
        Route::prefix('profil')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::get('/dokumen/{field}', [ProfileController::class, 'showDocument'])->name('show-document');
        });

        // =====================================================
        // USULAN ROUTES - NEW SEPARATED ARCHITECTURE
        // =====================================================

        // Dashboard Usulan Utama (Pusat)
        Route::get('/usulan-saya', [UsulanPegawaiController::class, 'index'])->name('usulan-pegawai.dashboard');

        // Usulan Selector (untuk memilih jenis usulan)
        Route::get('/usulan-create', [UsulanPegawaiController::class, 'createUsulan'])->name('usulan-pegawai.create');

        // API untuk statistics dashboard
        Route::get('/usulan/api/statistics', [UsulanPegawaiController::class, 'getStatistics'])->name('usulan-pegawai.api.statistics');

        // =====================================================
        // USULAN JABATAN ROUTES (SPECIFIC CONTROLLER) - ENHANCED
        // =====================================================
        Route::prefix('usulan-jabatan')->name('usulan-jabatan.')->group(function () {
            // Main CRUD routes
            Route::get('/', [UsulanJabatanController::class, 'index'])->name('index');
            Route::get('/create', [UsulanJabatanController::class, 'create'])->name('create');
            Route::post('/', [UsulanJabatanController::class, 'store'])->name('store');

            // FIXED: Edit route dengan explicit usulan parameter
            Route::get('/{usulan}/edit', [UsulanJabatanController::class, 'edit'])->name('edit');
            Route::put('/{usulan}', [UsulanJabatanController::class, 'update'])->name('update');
            Route::delete('/{usulanJabatan}', [UsulanJabatanController::class, 'destroy'])->name('destroy');

            // Document routes dengan explicit parameter
            Route::get('/{usulanJabatan}/dokumen/{field}', [UsulanJabatanController::class, 'showUsulanDocument'])
                ->name('show-document');

            // API routes
            Route::get('/{usulanJabatan}/logs', [UsulanJabatanController::class, 'getLogs'])->name('logs');

            // DEBUG: Test controller method
            Route::get('/{usulanJabatan}/debug-doc/{field}', function($usulanJabatan, $field) {
                return response()->json([
                    'usulan_id' => $usulanJabatan->id,
                    'field' => $field,
                    'message' => 'Controller parameters work'
                ]);
            })->name('debug-doc');
        });


        // =====================================================
        // LEGACY COMPATIBILITY - Redirect old routes
        // =====================================================
        Route::get('/usulan-saya/{usulan}/dokumen/{field}', function ($usulan, $field) {
            return redirect()->route('pegawai-unmul.usulan-jabatan.show-document', [$usulan, $field]);
        });

        Route::get('/usulan/{usulan}/logs', function ($usulan) {
            return redirect()->route('pegawai-unmul.usulan-jabatan.logs', $usulan);
        });
    });

// ------ RUTE HALAMAN BACKEND ADMIN FAKULTAS ------//
    Route::prefix('admin-fakultas')->middleware(['auth', 'role:Admin Fakultas'])->name('admin-fakultas.')->group(function () {
        // Dashboard Utama
        Route::get('/dashboard', [AdminFakultasController::class, 'dashboard'])->name('dashboard');

        // Daftar Periode Usulan Jabatan
        Route::get('/usulan', [AdminFakultasController::class, 'indexUsulanJabatan'])->name('usulan-jabatan.index');

        // Daftar Pengusul per Periode
        Route::get('/periode/{periodeUsulan}/pendaftar', [AdminFakultasController::class, 'showPendaftar'])->name('periode.pendaftar');

        // Detail Usulan untuk Validasi
        Route::get('/usulan/{adminUsulan}', [AdminFakultasController::class, 'show'])->name('usulan.show');

        // Simpan hasil validasi (mendukung semua aksi)
        Route::post('/usulan/{adminUsulan}/validasi', [AdminFakultasController::class, 'saveValidation'])->name('usulan.save-validation');

        // ===================================================================
        // DOCUMENT VIEWING ROUTES - NEW: Untuk semua jenis dokumen
        // ===================================================================
        
        // Dokumen usulan (pakta, turnitin, artikel, dll)
        Route::get('/usulan/{usulan}/dokumen/{field}', [AdminFakultasController::class, 'showUsulanDocument'])
            ->name('usulan.show-document');
            
        // NEW: Dokumen profil pegawai (ijazah, SK, dll)
        Route::get('/usulan/{usulan}/profil-dokumen/{field}', [AdminFakultasController::class, 'showPegawaiDocument'])
            ->name('usulan.show-pegawai-document');
            
        // NEW: Dokumen pendukung fakultas (surat usulan, berita senat)
        Route::get('/usulan/{usulan}/pendukung-dokumen/{field}', [AdminFakultasController::class, 'showDokumenPendukung'])
            ->name('usulan.show-dokumen-pendukung');

        // ===================================================================
        // USULAN ROUTES - Hybrid Approach (Clean URLs + Shared Logic)
        // ===================================================================
        Route::prefix('usulan')->name('usulan.')->group(function () {
            Route::get('/jabatan', [AdminFakultasController::class, 'usulanJabatan'])->name('jabatan');
            Route::get('/pangkat', [AdminFakultasController::class, 'usulanPangkat'])->name('pangkat');

            Route::post('/{usulan}/autosave', [AdminFakultasController::class, 'autosaveValidation'])
                ->name('autosave');
        });
        
    });

    // ------ RUTE HALAMAN BACKEND PENILAI UNIVERSITAS ------//
    Route::prefix('penilai-universitas')
        ->name('penilai-universitas.')
        ->middleware(['auth:pegawai', 'role:Penilai Universitas'])
        ->group(function () {

            // Dashboard Penilai
            Route::get('/dashboard', [PenilaiUniversitasDashboardController::class, 'index'])
                ->name('dashboard-penilai-universitas');

            // Pusat Usulan (khusus Penilai)
            Route::get('/pusat-usulan', [PenilaiPusatUsulanController::class, 'index'])
                ->name('pusat-usulan.index');

            Route::get('/pusat-usulan/{usulan}', [PenilaiPusatUsulanController::class, 'show'])
                ->name('pusat-usulan.show');

            Route::post('/pusat-usulan/{usulan}/process', [PenilaiPusatUsulanController::class, 'process'])
                ->name('pusat-usulan.process');
        });
}); // Penutup untuk middleware group

// =====================================================
// ROUTE MODEL BINDING CUSTOMIZATION - FIXED
// =====================================================

// FIXED: Specific model binding untuk usulan jabatan (dengan ownership check)
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

// ADDED: Admin usulan binding (tanpa ownership restriction untuk admin)
Route::bind('adminUsulan', function ($value) {
    return \App\Models\BackendUnivUsulan\Usulan::findOrFail($value);
});

// ADDED: Generic usulan binding untuk compatibility
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

// // =====================================================
// // ADDITIONAL DEBUGGING ROUTES (Development Only)
// // =====================================================
// if (app()->environment('local')) {
//     Route::prefix('debug')->middleware(['auth:pegawai'])->name('debug.')->group(function () {
//         Route::get('/routes', function () {
//             $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function ($route) {
//                 return [
//                     'method' => implode('|', $route->methods()),
//                     'uri' => $route->uri(),
//                     'name' => $route->getName(),
//                     'action' => $route->getActionName(),
//                 ];
//             });

//             return response()->json($routes->toArray());
//         })->name('routes');

//         Route::get('/user', function () {
//             return response()->json([
//                 'user' => Auth::user(),
//                 'permissions' => Auth::user()->getAllPermissions()->pluck('name'),
//                 'roles' => Auth::user()->getRoleNames(),
//             ]);
//         })->name('user');
//     });
}
