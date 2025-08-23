<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

// ======================================================================
// AUTHENTICATION ROUTES
// ======================================================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest:pegawai')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest:pegawai');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:pegawai')->name('logout');

// ======================================================================
// TEST ROUTES - UNTUK DEBUGGING (HAPUS SETELAH SELESAI)
// ======================================================================
Route::get('/test-document/{pegawai}/{field}', function($pegawai, $field) {
    return response()->json([
        'message' => 'Test route working',
        'pegawai_id' => $pegawai,
        'field' => $field,
        'url' => request()->url()
    ]);
})->name('test.document');

// Test route for form submission - bypasses all middleware
Route::post('/test-usulan-submission', function() {
    try {
        // Get first pegawai for testing
        $pegawai = \App\Models\KepegawaianUniversitas\Pegawai::first();
        if (!$pegawai) {
            return response()->json(['error' => 'No pegawai found'], 400);
        }

        // Get active periode
        $periode = \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')
            ->where('status', 'Buka')
            ->first();

        if (!$periode) {
            return response()->json(['error' => 'No active periode found'], 400);
        }

        // Create usulan with minimal data
        $usulan = new \App\Models\KepegawaianUniversitas\Usulan();
        $usulan->pegawai_id = $pegawai->id;
        $usulan->periode_usulan_id = $periode->id;
        $usulan->jenis_usulan = 'Usulan Jabatan';
        $usulan->status_usulan = 'Draft';
        $usulan->data_usulan = request()->all();
        $usulan->save();

        // Create usulan log
        $usulanLog = new \App\Models\KepegawaianUniversitas\UsulanLog();
        $usulanLog->usulan_id = $usulan->id;
        $usulanLog->dilakukan_oleh_id = $pegawai->id;
        $usulanLog->status_sebelumnya = null;
        $usulanLog->status_baru = 'Draft';
        $usulanLog->catatan = 'Usulan jabatan dibuat via test route';
        $usulanLog->save();

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dibuat via test route',
            'usulan_id' => $usulan->id,
            'log_id' => $usulanLog->id
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Gagal membuat usulan: ' . $e->getMessage()
        ], 500);
    }
})->name('test.usulan.submission');

// Temporary document route without authentication for testing
Route::get('/temp-document/{pegawai}/{field}', function($pegawai, $field) {
    try {
        $pegawaiModel = \App\Models\KepegawaianUniversitas\Pegawai::find($pegawai);
        if (!$pegawaiModel) {
            return response()->json(['error' => 'Pegawai not found'], 404);
        }

        $filePath = $pegawaiModel->$field;
        if (!$filePath) {
            return response()->json(['error' => 'File not found in database'], 404);
        }

        return response()->json([
            'message' => 'File found',
            'pegawai_id' => $pegawai,
            'field' => $field,
            'file_path' => $filePath,
            'exists' => \Storage::disk('public')->exists($filePath)
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('temp.document');

// ======================================================================
// PROTECTED ROUTES - SEMUA RUTE DI BAWAH INI HANYA BISA DIAKSES SETELAH LOGIN
// ======================================================================
Route::middleware(['web', 'auth:pegawai'])->group(function () {

    // ======================================================================
    // ADMIN UNIVERSITAS ROUTES
    // ======================================================================
    Route::prefix('admin-universitas')
        ->name('admin-universitas.')
        ->middleware(['role:Admin Universitas'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\AdminUniversitas\DashboardController::class, 'index'])
                ->name('dashboard');

            // Periode Usulan Management
            Route::resource('/periode-usulan', App\Http\Controllers\Backend\AdminUniversitas\PeriodeUsulanController::class)
                ->parameters(['periode-usulan' => 'periode']);

            // Dashboard Usulan (Dashboard for each period)
            Route::get('/dashboard-usulan', [App\Http\Controllers\Backend\AdminUniversitas\DashboardUsulanController::class, 'index'])
                ->name('dashboard-usulan.index');
            Route::get('/dashboard-usulan/{periode}', [App\Http\Controllers\Backend\AdminUniversitas\DashboardUsulanController::class, 'show'])
                ->name('dashboard-usulan.show');
        });

    // ======================================================================
    // KEPEGAWAIAN UNIVERSITAS ROUTES
    // ======================================================================
    Route::prefix('kepegawaian-universitas')
        ->name('backend.kepegawaian-universitas.')
        ->middleware(['role:Kepegawaian Universitas'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\KepegawaianUniversitas\DashboardController::class, 'index'])
                ->name('dashboard');

            // =====================================================
            // MASTER DATA ROUTES (STANDARDIZED)
            // =====================================================

            // Data Pegawai (STANDARDIZED)
            Route::prefix('data-pegawai')->name('data-pegawai.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'store'])
                    ->name('store');
                Route::get('/{pegawai}', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'show'])
                    ->name('show');
                Route::get('/{pegawai}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'edit'])
                    ->name('edit');
                Route::put('/{pegawai}', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'update'])
                    ->name('update');
                Route::delete('/{pegawai}', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'destroy'])
                    ->name('destroy');

                // Document routes (STANDARDIZED)
                Route::get('/{pegawai}/dokumen/{field}', [App\Http\Controllers\Backend\KepegawaianUniversitas\DataPegawaiController::class, 'showDocument'])
                    ->name('show-document');
            });

            // Unit Kerja (Hierarchical) (STANDARDIZED)
            Route::prefix('unitkerja')->name('unitkerja.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'store'])
                    ->name('store');
                Route::get('/{type}/{id}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'edit'])
                    ->name('edit');
                Route::put('/{type}/{id}', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'update'])
                    ->name('update');
                Route::delete('/{type}/{id}', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'destroy'])
                    ->name('destroy');

                // API routes untuk dropdown (STANDARDIZED)
                Route::get('/api/sub-unit-kerja/{unitKerjaId}', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'getSubUnitKerja'])
                    ->name('api.sub-unit-kerja');
                Route::get('/api/sub-sub-unit-kerja/{subUnitKerjaId}', [App\Http\Controllers\Backend\KepegawaianUniversitas\UnitKerjaController::class, 'getSubSubUnitKerja'])
                    ->name('api.sub-sub-unit-kerja');
            });

            // Sub Unit Kerja (STANDARDIZED)
            Route::prefix('sub-unitkerja')->name('sub-unitkerja.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubUnitKerjaController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubUnitKerjaController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubUnitKerjaController::class, 'store'])
                    ->name('store');
                Route::get('/{subUnitKerja}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubUnitKerjaController::class, 'edit'])
                    ->name('edit');
                Route::put('/{subUnitKerja}', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubUnitKerjaController::class, 'update'])
                    ->name('update');
                Route::delete('/{subUnitKerja}', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubUnitKerjaController::class, 'destroy'])
                    ->name('destroy');
            });

            // Sub Sub Unit Kerja (STANDARDIZED)
            Route::prefix('sub-sub-unitkerja')->name('sub-sub-unitkerja.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubSubUnitKerjaController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubSubUnitKerjaController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubSubUnitKerjaController::class, 'store'])
                    ->name('store');
                Route::get('/{subSubUnitKerja}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubSubUnitKerjaController::class, 'edit'])
                    ->name('edit');
                Route::put('/{subSubUnitKerja}', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubSubUnitKerjaController::class, 'update'])
                    ->name('update');
                Route::delete('/{subSubUnitKerja}', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubSubUnitKerjaController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/api/sub-unit-kerjas', [App\Http\Controllers\Backend\KepegawaianUniversitas\SubSubUnitKerjaController::class, 'getSubUnitKerjas'])
                    ->name('api.sub-unit-kerjas');
            });

            // Jabatan (STANDARDIZED)
            Route::prefix('jabatan')->name('jabatan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'store'])
                    ->name('store');
                Route::get('/{jabatan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'show'])
                    ->name('show');
                Route::get('/{jabatan}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'edit'])
                    ->name('edit');
                Route::put('/{jabatan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'update'])
                    ->name('update');
                Route::delete('/{jabatan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'destroy'])
                    ->name('destroy');

                // Export route (STANDARDIZED)
                Route::get('/export', [App\Http\Controllers\Backend\KepegawaianUniversitas\JabatanController::class, 'export'])
                    ->name('export');
            });

            // Pangkat (STANDARDIZED)
            Route::prefix('pangkat')->name('pangkat.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'store'])
                    ->name('store');
                Route::get('/{pangkat}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'edit'])
                    ->name('edit');
                Route::put('/{pangkat}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'update'])
                    ->name('update');
                Route::delete('/{pangkat}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/api/hierarchy', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'getHierarchyStructure'])
                    ->name('api.hierarchy');
                Route::get('/api/promotion-targets/{pangkat}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PangkatController::class, 'getPromotionTargets'])
                    ->name('api.promotion-targets');
            });

            // =====================================================
            // PUSAT USULAN ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('pusat-usulan')->name('pusat-usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\PusatUsulanController::class, 'index'])
                    ->name('index');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PusatUsulanController::class, 'show'])
                    ->name('show');
                Route::post('/{usulan}/process', [App\Http\Controllers\Backend\KepegawaianUniversitas\PusatUsulanController::class, 'process'])
                    ->name('process');

                // Document routes (STANDARDIZED)
                Route::get('/{usulan}/dokumen/{field}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PusatUsulanController::class, 'showUsulanDocument'])
                    ->name('show-document');
            });

            // =====================================================
            // DASHBOARD PERIODE USULAN ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('dashboard-periode')->name('dashboard-periode.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\DashboardPeriodeController::class, 'index'])
                    ->name('index');
                Route::get('/{periode}', [App\Http\Controllers\Backend\KepegawaianUniversitas\DashboardPeriodeController::class, 'show'])
                    ->name('show');
            });

            // =====================================================
            // PERIODE USULAN ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('periode-usulan')->name('periode-usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\PeriodeUsulanController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\KepegawaianUniversitas\PeriodeUsulanController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\PeriodeUsulanController::class, 'store'])
                    ->name('store');
                Route::get('/{periodeUsulan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PeriodeUsulanController::class, 'show'])
                    ->name('show');
                Route::get('/{periodeUsulan}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\PeriodeUsulanController::class, 'edit'])
                    ->name('edit');
                Route::put('/{periodeUsulan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PeriodeUsulanController::class, 'update'])
                    ->name('update');
                Route::delete('/{periodeUsulan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PeriodeUsulanController::class, 'destroy'])
                    ->name('destroy');

                // Pendaftar route (STANDARDIZED)
                Route::get('/{periodeUsulan}/pendaftar', [App\Http\Controllers\Backend\KepegawaianUniversitas\PusatUsulanController::class, 'showPendaftar'])
                    ->name('pendaftar');
            });

            // =====================================================
            // ROLE PEGAWAI ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('role-pegawai')->name('role-pegawai.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\RolePegawaiController::class, 'index'])
                    ->name('index');
                Route::get('/{pegawai}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\RolePegawaiController::class, 'edit'])
                    ->name('edit');
                Route::put('/{pegawai}', [App\Http\Controllers\Backend\KepegawaianUniversitas\RolePegawaiController::class, 'update'])
                    ->name('update');
            });

            // =====================================================
            // MANAJEMEN AKUN PEGAWAI ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('pegawai')->name('pegawai.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\PegawaiController::class, 'index'])
                    ->name('index');
                Route::get('/{pegawai}/edit', [App\Http\Controllers\Backend\KepegawaianUniversitas\PegawaiController::class, 'edit'])
                    ->name('edit');
                Route::put('/{pegawai}', [App\Http\Controllers\Backend\KepegawaianUniversitas\PegawaiController::class, 'update'])
                    ->name('update');
            });

            // =====================================================
            // USULAN VALIDATION ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan')->name('usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\KepegawaianUniversitas\UsulanValidationController::class, 'index'])
                    ->name('index');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\KepegawaianUniversitas\UsulanValidationController::class, 'show'])
                    ->name('show');
                Route::post('/{usulan}/save-validation', [App\Http\Controllers\Backend\KepegawaianUniversitas\UsulanValidationController::class, 'saveValidation'])
                    ->name('save-validation');
                Route::get('/{usulan}/document/{field}', [App\Http\Controllers\Backend\KepegawaianUniversitas\UsulanValidationController::class, 'showDocument'])
                    ->name('show-document');
                Route::get('/{usulan}/pegawai-document/{field}', [App\Http\Controllers\Backend\KepegawaianUniversitas\UsulanValidationController::class, 'showPegawaiDocument'])
                    ->name('show-pegawai-document');

                // Toggle periode status
                Route::post('/toggle-periode', [App\Http\Controllers\Backend\KepegawaianUniversitas\UsulanValidationController::class, 'togglePeriode'])
                    ->name('toggle-periode');
            });
        });

    // ======================================================================
    // PEGAWAI UNMUL ROUTES
    // ======================================================================
    Route::prefix('pegawai-unmul')
        ->name('pegawai-unmul.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\PegawaiUnmul\DashboardController::class, 'index'])
                ->name('dashboard-pegawai-unmul');

            // =====================================================
            // PROFILE ROUTES
            // =====================================================
            Route::prefix('profil')->name('profile.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'show'])
                    ->name('show');
                Route::get('/edit', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'edit'])
                    ->name('edit');
                Route::put('/', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'update'])
                    ->name('update');
                Route::get('/dokumen/{field}', [App\Http\Controllers\Backend\PegawaiUnmul\ProfileController::class, 'showDocument'])
                    ->name('show-document');
            });

            // =====================================================
            // USULAN ROUTES - SEPARATED ARCHITECTURE
            // =====================================================

            // Dashboard Usulan Utama (Pusat)
            Route::get('/usulan-saya', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController::class, 'index'])
                ->name('usulan-pegawai.dashboard');

            // Usulan Selector (untuk memilih jenis usulan)
            Route::get('/usulan-create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController::class, 'createUsulan'])
                ->name('usulan-pegawai.create');

            // API untuk statistics dashboard
            Route::get('/usulan/api/statistics', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPegawaiController::class, 'getStatistics'])
                ->name('usulan-pegawai.api.statistics');

            // =====================================================
            // USULAN JABATAN ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-jabatan')->name('usulan-jabatan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'store'])
                    ->name('store');
                Route::post('/test', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'testStore'])
                    ->name('test-store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'destroy'])
                    ->name('destroy');

                // Document routes (STANDARDIZED)
                Route::get('/{usulan}/dokumen/{field}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'showUsulanDocument'])
                    ->name('show-document');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN NUPTK ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-nuptk')->name('usulan-nuptk.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanNuptkController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN LAPORAN LKD ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-laporan-lkd')->name('usulan-laporan-lkd.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanLkdController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN PRESENSI ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-presensi')->name('usulan-presensi.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPresensiController::class, 'getLogs'])
                    ->name('logs');
            });


            // =====================================================
            // USULAN ID SINTA KE SISTER ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-id-sinta-sister')->name('usulan-id-sinta-sister.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanIdSintaSisterController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN SATYALANCANA ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-satyalancana')->name('usulan-satyalancana.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanSatyalancanaController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN TUGAS BELAJAR ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-tugas-belajar')->name('usulan-tugas-belajar.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanTugasBelajarController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN PENGAKTIFAN KEMBALI ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-pengaktifan-kembali')->name('usulan-pengaktifan-kembali.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPengaktifanKembaliController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN PENYESUAIAN MASA KERJA ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-penyesuaian-masa-kerja')->name('usulan-penyesuaian-masa-kerja.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPenyesuaianMasaKerjaController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN UJIAN DINAS IJAZAH ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-ujian-dinas-ijazah')->name('usulan-ujian-dinas-ijazah.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanUjianDinasIjazahController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN LAPORAN SERDOS ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-laporan-serdos')->name('usulan-laporan-serdos.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanLaporanSerdosController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN PENSIUN ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-pensiun')->name('usulan-pensiun.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPensiunController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN KEPANGKATAN ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-kepangkatan')->name('usulan-kepangkatan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanKepangkatanController::class, 'getLogs'])
                    ->name('logs');
            });

            // =====================================================
            // USULAN PENCANTUMAN GELAR ROUTES (STANDARDIZED)
            // =====================================================
            Route::prefix('usulan-pencantuman-gelar')->name('usulan-pencantuman-gelar.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'index'])
                    ->name('index');
                Route::get('/create', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'create'])
                    ->name('create');
                Route::post('/', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'store'])
                    ->name('store');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'show'])
                    ->name('show');
                Route::get('/{usulan}/edit', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'edit'])
                    ->name('edit');
                Route::put('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'update'])
                    ->name('update');
                Route::delete('/{usulan}', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'destroy'])
                    ->name('destroy');

                // API routes (STANDARDIZED)
                Route::get('/{usulan}/logs', [App\Http\Controllers\Backend\PegawaiUnmul\UsulanPencantumanGelarController::class, 'getLogs'])
                    ->name('logs');
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

    // ======================================================================
    // ADMIN FAKULTAS ROUTES
    // ======================================================================
    Route::prefix('admin-fakultas')
        ->name('admin-fakultas.')
        ->middleware(['role:Admin Fakultas'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\AdminFakultas\DashboardController::class, 'index'])
                ->name('dashboard');

            // Dashboard Khusus Usulan Jabatan
            Route::get('/dashboard-jabatan', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'dashboardJabatan'])
                ->name('dashboard-jabatan');

            // Dashboard Khusus Usulan Pangkat
            Route::get('/dashboard-pangkat', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'dashboardPangkat'])
                ->name('dashboard-pangkat');

            // =====================================================
            // USULAN ROUTES
            // =====================================================
            Route::prefix('usulan')->name('usulan.')->group(function () {
                // Detail Usulan untuk Validasi
                Route::get('/{usulan}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'show'])
                    ->name('show');

                // Simpan hasil validasi
                Route::post('/{usulan}/validasi', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'saveValidation'])
                    ->name('save-validation');
                Route::post('/{usulan}/autosave', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'autosaveValidation'])
                    ->name('autosave');

                // =====================================================
                // DOCUMENT VIEWING ROUTES
                // =====================================================

                // Dokumen usulan (pakta, turnitin, artikel, dll)
                Route::get('/{usulan}/dokumen/{field}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showUsulanDocument'])
                    ->name('show-document');

                // Dokumen profil pegawai (ijazah, SK, dll)
                Route::get('/{usulan}/profil-dokumen/{field}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showPegawaiDocument'])
                    ->name('show-pegawai-document');

                // Dokumen pendukung fakultas (surat usulan, berita senat)
                Route::get('/{usulan}/pendukung-dokumen/{field}', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showDokumenPendukung'])
                    ->name('show-dokumen-pendukung');
            });

            // Daftar Pengusul per Periode
            Route::get('/periode/{periodeUsulan}/pendaftar', [App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController::class, 'showPendaftar'])
                ->name('periode.pendaftar');

            // =====================================================
            // DEBUG ROUTES (Development Only)
            // =====================================================
            if (app()->environment('local')) {
                Route::get('/test-dokumen/{usulan}/{field}', function($usulan, $field) {
                    return response()->json([
                        'usulan_id' => $usulan,
                        'field' => $field,
                        'usulan_exists' => \App\Models\KepegawaianUniversitas\Usulan::find($usulan) ? true : false,
                        'document_path' => \App\Models\KepegawaianUniversitas\Usulan::find($usulan)?->getDocumentPath($field),
                        'file_exists' => \Storage::disk('local')->exists(\App\Models\KepegawaianUniversitas\Usulan::find($usulan)?->getDocumentPath($field) ?? '')
                    ]);
                })->name('test-dokumen');
            }
        });

    // ======================================================================
    // PENILAI UNIVERSITAS ROUTES
    // ======================================================================
    Route::prefix('penilai-universitas')
        ->name('penilai-universitas.')
        ->middleware(['role:Penilai Universitas'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\PenilaiUniversitas\DashboardController::class, 'index'])
                ->name('dashboard');

            // =====================================================
            // PUSAT USULAN ROUTES (Khusus Penilai)
            // =====================================================
            Route::prefix('pusat-usulan')->name('pusat-usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'index'])
                    ->name('index');
                Route::get('/periode/{periode}/pendaftar', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'showPendaftar'])
                    ->name('show-pendaftar');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'show'])
                    ->name('show');
                Route::post('/{usulan}/process', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'process'])
                    ->name('process');
                Route::post('/{usulan}/save-validation', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'process'])
                    ->name('save-validation');
                Route::get('/{usulan}/document/{field}', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'showDocument'])
                    ->name('show-document');
                Route::get('/{usulan}/pegawai-document/{field}', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'showPegawaiDocument'])
                    ->name('show-pegawai-document');
                Route::get('/{usulan}/admin-fakultas-document/{field}', [App\Http\Controllers\Backend\PenilaiUniversitas\PusatUsulanController::class, 'showAdminFakultasDocument'])
                    ->name('show-admin-fakultas-document');
            });
        });
});

// ======================================================================
// ROUTE MODEL BINDING CUSTOMIZATION
// ======================================================================

// Usulan Jabatan binding (dengan ownership check untuk pegawai)
Route::bind('usulanJabatan', function ($value) {
    $usulan = \App\Models\KepegawaianUniversitas\Usulan::where('id', $value)->first();

    if (!$usulan) {
        abort(404);
    }

    // Check ownership untuk pegawai routes
    if (request()->is('pegawai-unmul/*') && $usulan->pegawai_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki akses untuk usulan ini.');
    }

    return $usulan;
});

// Generic usulan binding untuk compatibility
Route::bind('usulan', function ($value) {
    $usulan = \App\Models\KepegawaianUniversitas\Usulan::where('id', $value)->first();

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

    // ======================================================================
    // ADMIN KEUANGAN ROUTES
    // ======================================================================
    Route::prefix('admin-keuangan')
        ->name('admin-keuangan.')
        ->middleware(['role:Admin Keuangan'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\AdminKeuangan\DashboardController::class, 'index'])
                ->name('dashboard');

            // Laporan Keuangan
            Route::get('/laporan-keuangan', [App\Http\Controllers\Backend\AdminKeuangan\LaporanKeuanganController::class, 'index'])
                ->name('laporan-keuangan.index');

            // Verifikasi Dokumen Keuangan
            Route::get('/verifikasi-dokumen', [App\Http\Controllers\Backend\AdminKeuangan\VerifikasiDokumenController::class, 'index'])
                ->name('verifikasi-dokumen.index');

            // SK Documents Routes
            Route::get('/sk-pangkat', [App\Http\Controllers\Backend\AdminKeuangan\SKPangkatController::class, 'index'])
                ->name('sk-pangkat.index');
            Route::get('/sk-jabatan', [App\Http\Controllers\Backend\AdminKeuangan\SKJabatanController::class, 'index'])
                ->name('sk-jabatan.index');
            Route::get('/sk-berkala', [App\Http\Controllers\Backend\AdminKeuangan\SKBerkalaController::class, 'index'])
                ->name('sk-berkala.index');
            Route::get('/model-d', [App\Http\Controllers\Backend\AdminKeuangan\ModelDController::class, 'index'])
                ->name('model-d.index');
            Route::get('/sk-cpns', [App\Http\Controllers\Backend\AdminKeuangan\SKCPNSController::class, 'index'])
                ->name('sk-cpns.index');
            Route::get('/sk-pns', [App\Http\Controllers\Backend\AdminKeuangan\SKPNSController::class, 'index'])
                ->name('sk-pns.index');
            Route::get('/sk-pppk', [App\Http\Controllers\Backend\AdminKeuangan\SKPPPKController::class, 'index'])
                ->name('sk-pppk.index');
            Route::get('/sk-mutasi', [App\Http\Controllers\Backend\AdminKeuangan\SKMutasiController::class, 'index'])
                ->name('sk-mutasi.index');
            Route::get('/sk-pensiun', [App\Http\Controllers\Backend\AdminKeuangan\SKPensiunController::class, 'index'])
                ->name('sk-pensiun.index');
            Route::get('/sk-tunjangan-sertifikasi', [App\Http\Controllers\Backend\AdminKeuangan\SKTunjanganSertifikasiController::class, 'index'])
                ->name('sk-tunjangan-sertifikasi.index');
            Route::get('/skpp', [App\Http\Controllers\Backend\AdminKeuangan\SKPPController::class, 'index'])
                ->name('skpp.index');
            Route::get('/sk-pemberhentian-meninggal', [App\Http\Controllers\Backend\AdminKeuangan\SKPemberhentianMeninggalController::class, 'index'])
                ->name('sk-pemberhentian-meninggal.index');
            Route::get('/sk-pengaktifan-kembali', [App\Http\Controllers\Backend\AdminKeuangan\SKPengaktifanKembaliController::class, 'index'])
                ->name('sk-pengaktifan-kembali.index');
            Route::get('/sk-tugas-belajar', [App\Http\Controllers\Backend\AdminKeuangan\SKTugasBelajarController::class, 'index'])
                ->name('sk-tugas-belajar.index');
            Route::get('/sk-pemberhentian-sementara', [App\Http\Controllers\Backend\AdminKeuangan\SKPemberhentianSementaraController::class, 'index'])
                ->name('sk-pemberhentian-sementara.index');
            Route::get('/sk-penyesuaian-masa-kerja', [App\Http\Controllers\Backend\AdminKeuangan\SKPenyesuaianMasaKerjaController::class, 'index'])
                ->name('sk-penyesuaian-masa-kerja.index');
        });





    // ======================================================================
    // TIM SENAT ROUTES
    // ======================================================================
    Route::prefix('tim-senat')
        ->name('tim-senat.')
        ->middleware(['role:Tim Senat'])
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Backend\TimSenat\DashboardController::class, 'index'])
                ->name('dashboard');

            // Rapat Senat
            Route::get('/rapat-senat', [App\Http\Controllers\Backend\TimSenat\RapatSenatController::class, 'index'])
                ->name('rapat-senat.index');

            // Keputusan Senat
            Route::get('/keputusan-senat', [App\Http\Controllers\Backend\TimSenat\KeputusanSenatController::class, 'index'])
                ->name('keputusan-senat.index');

            // Usulan Routes
            Route::prefix('usulan')->name('usulan.')->group(function () {
                Route::get('/', [App\Http\Controllers\Backend\TimSenat\UsulanController::class, 'index'])
                    ->name('index');
                Route::get('/{usulan}', [App\Http\Controllers\Backend\TimSenat\UsulanController::class, 'show'])
                    ->name('show');
                Route::post('/{usulan}/save-validation', [App\Http\Controllers\Backend\TimSenat\UsulanController::class, 'saveValidation'])
                    ->name('save-validation');
                Route::get('/{usulan}/document/{field}', [App\Http\Controllers\Backend\TimSenat\UsulanController::class, 'showDocument'])
                    ->name('show-document');
                Route::get('/{usulan}/pegawai-document/{field}', [App\Http\Controllers\Backend\TimSenat\UsulanController::class, 'showPegawaiDocument'])
                    ->name('show-pegawai-document');
            });
        });

// ======================================================================
// DEVELOPMENT DEBUGGING ROUTES (Local Environment Only)
// ======================================================================
if (app()->environment('local')) {
    Route::prefix('debug')->middleware(['auth:pegawai'])->name('debug.')->group(function () {
        Route::get('/routes', function () {
            $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function ($route) {
                return [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                ];
            });

            return response()->json($routes->toArray());
        })->name('routes');

        Route::get('/user', function () {
            return response()->json([
                'user' => Auth::user(),
                'permissions' => Auth::user()->getAllPermissions()->pluck('name'),
                'roles' => Auth::user()->getRoleNames(),
            ]);
        })->name('user');
    });
}

