<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProfilController as FrontendProfil;
use App\Http\Controllers\Frontend\LayananController as FrontendLayanan;

// ------ RUTE HALAMAN FRONTEND ------//
Route::get('/', function () {
    return view('frontend.index');
});

Route::prefix('profil')->group(function () {
    Route::get('/visi-misi', [FrontendProfil::class, 'visiMisi'])->name('profil.visi-misi');
    Route::get('/struktur-organisasi', [FrontendProfil::class, 'strukturOrganisasi'])->name('profil.struktur-organisasi');
});

// Informasi Routes
Route::get('/berita', function () {
    return view('frontend.layouts.informasi.berita');
})->name('berita');

Route::get('/pengumuman', function () {
    return view('frontend.layouts.informasi.pengumuman');
})->name('pengumuman');

Route::get('/keputusan', function () {
    return view('frontend.layouts.dasar-hukum.keputusan');
})->name('keputusan');

Route::get('/pedoman', function () {
    return view('frontend.layouts.dasar-hukum.pedoman');
})->name('pedoman');

Route::get('/peraturan', function () {
    return view('frontend.layouts.dasar-hukum.peraturan');
})->name('peraturan');

Route::get('/surat-edaran', function () {
    return view('frontend.layouts.dasar-hukum.surat-edaran');
})->name('surat-edaran');

Route::get('/surat-kementerian', function () {
    return view('frontend.layouts.dasar-hukum.surat-kementerian');
})->name('surat-kementerian');

Route::get('/undang-undang', function () {
    return view('frontend.layouts.dasar-hukum.undang-undang');
})->name('undang-undang');

Route::get('/surat-rektor-unmul', function () {
    return view('frontend.layouts.dasar-hukum.surat-rektor-unmul');
})->name('surat-rektor-unmul');

// Admin Universitas Routes (using frontend routes)
Route::prefix('admin-universitas')->group(function () {
    Route::get('/visi-misi', function () {
        return view('backend.layouts.views.admin-universitas.visi-misi');
    })->name('admin-universitas.visi-misi.index');

    Route::get('/struktur-organisasi', function () {
        return view('backend.layouts.views.admin-universitas.struktur-organisasi');
    })->name('admin-universitas.struktur-organisasi.index');

    // API Routes for Admin Universitas
    Route::post('/visi-misi', [App\Http\Controllers\Backend\AdminUniversitas\VisiMisiController::class, 'store'])
        ->name('admin-universitas.visi-misi.store');
    Route::put('/visi-misi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\VisiMisiController::class, 'update'])
        ->name('admin-universitas.visi-misi.update');
    Route::delete('/visi-misi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\VisiMisiController::class, 'destroy'])
        ->name('admin-universitas.visi-misi.destroy');

    Route::get('/aplikasi-kepegawaian', function () {
        return view('backend.layouts.views.admin-universitas.aplikasi-kepegawaian');
    })->name('admin-universitas.aplikasi-kepegawaian.index');

    Route::post('/aplikasi-kepegawaian', [App\Http\Controllers\Backend\AdminUniversitas\AplikasiKepegawaianController::class, 'store'])
        ->name('admin-universitas.aplikasi-kepegawaian.store');
    Route::put('/aplikasi-kepegawaian/{id}', [App\Http\Controllers\Backend\AdminUniversitas\AplikasiKepegawaianController::class, 'update'])
        ->name('admin-universitas.aplikasi-kepegawaian.update');
    Route::delete('/aplikasi-kepegawaian/{id}', [App\Http\Controllers\Backend\AdminUniversitas\AplikasiKepegawaianController::class, 'destroy'])
        ->name('admin-universitas.aplikasi-kepegawaian.destroy');

    // Dasar Hukum Routes
    Route::get('/dasar-hukum', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'index'])
        ->name('admin-universitas.dasar-hukum.index');
    Route::post('/dasar-hukum', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'store'])
        ->name('admin-universitas.dasar-hukum.store');
    Route::get('/dasar-hukum/{id}', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'show'])
        ->name('admin-universitas.dasar-hukum.show');
    Route::put('/dasar-hukum/{id}', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'update'])
        ->name('admin-universitas.dasar-hukum.update');
    Route::delete('/dasar-hukum/{id}', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'destroy'])
        ->name('admin-universitas.dasar-hukum.destroy');
    Route::get('/dasar-hukum-api', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'getData'])
        ->name('admin-universitas.dasar-hukum.get-data');
    Route::get('/dasar-hukum-document/{filename}', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'showDocument'])
        ->name('admin-universitas.dasar-hukum.document');

    Route::get('/informasi', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'index'])
        ->name('admin-universitas.informasi.index');
    Route::get('/informasi/data', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'getData'])
        ->name('admin-universitas.informasi.data');
    Route::post('/informasi', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'store'])
        ->name('admin-universitas.informasi.store');
    Route::put('/informasi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'update'])
        ->name('admin-universitas.informasi.update');
    Route::delete('/informasi/{id}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'destroy'])
        ->name('admin-universitas.informasi.destroy');
    Route::post('/informasi/generate-nomor-surat', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'generateNomorSurat'])
        ->name('admin-universitas.informasi.generate-nomor-surat');
});

Route::prefix('layanan')->group(function () {
    Route::get('/aplikasi', [FrontendLayanan::class, 'aplikasi'])->name('layanan.aplikasi');
    Route::get('/usulan-kepegawaian', [FrontendLayanan::class, 'usulanKepegawaian'])->name('layanan.usulan-kepegawaian');
});

Route::view('/blangko-surat', 'frontend.layouts.blangko-surat')->name('blangko.surat');

// API Routes for Frontend
Route::get('/api/visi-misi', [App\Http\Controllers\Api\VisiMisiController::class, 'index']);
Route::options('/api/visi-misi', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});

Route::get('/api/struktur-organisasi', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'index']);
Route::post('/api/struktur-organisasi', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'store']);
Route::delete('/api/struktur-organisasi', [App\Http\Controllers\Api\StrukturOrganisasiController::class, 'destroy']);
Route::options('/api/struktur-organisasi', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});

Route::get('/api/aplikasi-kepegawaian', [App\Http\Controllers\Api\AplikasiKepegawaianController::class, 'index']);
Route::options('/api/aplikasi-kepegawaian', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});

Route::get('/api/informasi', [App\Http\Controllers\Api\InformasiController::class, 'index']);
Route::get('/api/informasi/featured', [App\Http\Controllers\Api\InformasiController::class, 'featured']);
Route::get('/api/informasi/pinned', [App\Http\Controllers\Api\InformasiController::class, 'pinned']);
Route::get('/api/informasi/latest', [App\Http\Controllers\Api\InformasiController::class, 'latest']);
Route::get('/api/informasi/{id}', [App\Http\Controllers\Api\InformasiController::class, 'show']);

// API routes for Dasar Hukum
Route::get('/api/dasar-hukum', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'getData']);
Route::get('/api/dasar-hukum/{id}', [App\Http\Controllers\Backend\AdminUniversitas\DasarHukumController::class, 'show']);

// Route untuk mengakses file lampiran dengan authentication
Route::get('/lampiran/{filename}', [App\Http\Controllers\Backend\AdminUniversitas\InformasiController::class, 'showDocument'])
    ->name('lampiran.show');


Route::options('/api/informasi', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
});
