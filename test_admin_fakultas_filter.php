<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Admin Fakultas Filter...\n";
echo "=====================================\n\n";

try {
    // Ambil admin fakultas pertama
    $adminFakultas = Pegawai::whereHas('roles', function($query) {
        $query->where('name', 'Admin Fakultas');
    })->whereNotNull('unit_kerja_id')->first();

    if (!$adminFakultas) {
        echo "❌ Tidak ada admin fakultas yang ditemukan\n";
        exit(1);
    }

    echo "👤 Admin Fakultas: {$adminFakultas->nama_lengkap}\n";
    $unitKerjaNama = $adminFakultas->unitKerjaPengelola ? $adminFakultas->unitKerjaPengelola->nama : 'Tidak ada';
    echo "🏢 Unit Kerja: {$unitKerjaNama}\n\n";

    if (!$adminFakultas->unitKerjaPengelola) {
        echo "❌ Admin fakultas tidak memiliki unit kerja yang dikelola\n";
        exit(1);
    }

    $unitKerja = $adminFakultas->unitKerjaPengelola;

    // Test 1: Ambil semua usulan tanpa filter
    echo "📊 Test 1: Semua usulan tanpa filter\n";
    $allUsulans = Usulan::count();
    echo "   Total usulan: {$allUsulans}\n\n";

    // Test 2: Ambil usulan dengan filter fakultas
    echo "📊 Test 2: Usulan dengan filter fakultas ({$unitKerja->nama})\n";
    $filteredUsulans = Usulan::whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function($q) use ($unitKerja) {
        $q->where('id', $unitKerja->id);
    })->get();

    echo "   Total usulan di fakultas ini: {$filteredUsulans->count()}\n";

    if ($filteredUsulans->count() > 0) {
        echo "   Detail usulan:\n";
        foreach ($filteredUsulans as $usulan) {
            $pegawai = $usulan->pegawai;
            $fakultas = $pegawai->unitKerja && $pegawai->unitKerja->subUnitKerja && $pegawai->unitKerja->subUnitKerja->unitKerja ? $pegawai->unitKerja->subUnitKerja->unitKerja->nama : 'Tidak diketahui';
            echo "     - ID: {$usulan->id}, Pegawai: {$pegawai->nama_lengkap}, Fakultas: {$fakultas}\n";
        }
    }

    echo "\n";

    // Test 3: Test dengan periode usulan
    echo "📊 Test 3: Periode usulan dengan filter\n";
    $periodeUsulans = PeriodeUsulan::with(['usulans' => function($query) use ($unitKerja) {
        $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function($q) use ($unitKerja) {
            $q->where('id', $unitKerja->id);
        });
    }])->get();

    echo "   Total periode: {$periodeUsulans->count()}\n";

    foreach ($periodeUsulans as $periode) {
        $jumlahPengusul = $periode->usulans->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])->count();
        $totalUsulan = $periode->usulans->count();

        echo "   - Periode: {$periode->nama_periode}\n";
        echo "     Total usulan: {$totalUsulan}, Menunggu review: {$jumlahPengusul}\n";
    }

    echo "\n✅ Test selesai!\n";

} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
