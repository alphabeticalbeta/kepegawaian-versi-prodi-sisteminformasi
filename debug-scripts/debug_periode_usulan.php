<?php
/**
 * Script debug untuk memeriksa data periode usulan dan status kepegawaian
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Pegawai;

echo "=== Debug Periode Usulan ===\n\n";

// Get current user
$pegawai = Auth::user();
if (!$pegawai) {
    echo "❌ Tidak ada user yang login\n";
    exit;
}

echo "=== Data Pegawai ===\n";
echo "ID: " . $pegawai->id . "\n";
echo "NIP: " . $pegawai->nip . "\n";
echo "Nama: " . $pegawai->nama_lengkap . "\n";
echo "Jenis Pegawai: " . $pegawai->jenis_pegawai . "\n";
echo "Status Kepegawaian: " . $pegawai->status_kepegawaian . "\n\n";

// Determine jenis usulan
$jenisUsulanPeriode = '';
if ($pegawai->jenis_pegawai === 'Dosen' && $pegawai->status_kepegawaian === 'Dosen PNS') {
    $jenisUsulanPeriode = 'usulan-jabatan-dosen';
} elseif ($pegawai->jenis_pegawai === 'Tenaga Kependidikan' && $pegawai->status_kepegawaian === 'Tenaga Kependidikan PNS') {
    $jenisUsulanPeriode = 'usulan-jabatan-tendik';
} else {
    $jenisUsulanPeriode = 'usulan-jabatan-dosen'; // Fallback
}

echo "=== Jenis Usulan Periode ===\n";
echo "Jenis Usulan: " . $jenisUsulanPeriode . "\n\n";

// Check all periode usulan
echo "=== Semua Periode Usulan ===\n";
$allPeriodeUsulans = PeriodeUsulan::all();
if ($allPeriodeUsulans->count() > 0) {
    foreach ($allPeriodeUsulans as $periode) {
        echo "ID: " . $periode->id . "\n";
        echo "Nama: " . $periode->nama_periode . "\n";
        echo "Jenis Usulan: " . $periode->jenis_usulan . "\n";
        echo "Status: " . $periode->status . "\n";
        echo "Status Kepegawaian: " . json_encode($periode->status_kepegawaian) . "\n";
        echo "Tanggal Mulai: " . $periode->tanggal_mulai . "\n";
        echo "Tanggal Selesai: " . $periode->tanggal_selesai . "\n";
        echo "---\n";
    }
} else {
    echo "❌ Tidak ada data periode usulan\n";
}

echo "\n=== Filter Periode Usulan ===\n";

// Test query step by step
echo "1. Filter by jenis_usulan = '$jenisUsulanPeriode':\n";
$step1 = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)->get();
echo "   Found: " . $step1->count() . " records\n";
foreach ($step1 as $periode) {
    echo "   - " . $periode->nama_periode . " (ID: " . $periode->id . ")\n";
}

echo "\n2. Filter by status = 'Buka':\n";
$step2 = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
    ->where('status', 'Buka')
    ->get();
echo "   Found: " . $step2->count() . " records\n";
foreach ($step2 as $periode) {
    echo "   - " . $periode->nama_periode . " (ID: " . $periode->id . ")\n";
}

echo "\n3. Filter by status_kepegawaian contains '" . $pegawai->status_kepegawaian . "':\n";
$step3 = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
    ->where('status', 'Buka')
    ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
    ->get();
echo "   Found: " . $step3->count() . " records\n";
foreach ($step3 as $periode) {
    echo "   - " . $periode->nama_periode . " (ID: " . $periode->id . ")\n";
    echo "     Status Kepegawaian: " . json_encode($periode->status_kepegawaian) . "\n";
}

echo "\n=== Final Query Result ===\n";
$periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
    ->where('status', 'Buka')
    ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
    ->orderBy('tanggal_mulai', 'desc')
    ->get();

echo "Total periode usulan found: " . $periodeUsulans->count() . "\n";

if ($periodeUsulans->count() > 0) {
    foreach ($periodeUsulans as $periode) {
        echo "\nPeriode: " . $periode->nama_periode . "\n";
        echo "ID: " . $periode->id . "\n";
        echo "Jenis Usulan: " . $periode->jenis_usulan . "\n";
        echo "Status: " . $periode->status . "\n";
        echo "Status Kepegawaian: " . json_encode($periode->status_kepegawaian) . "\n";
        echo "Tanggal Mulai: " . $periode->tanggal_mulai . "\n";
        echo "Tanggal Selesai: " . $periode->tanggal_selesai . "\n";
    }
} else {
    echo "❌ Tidak ada periode usulan yang sesuai\n";
}

echo "\n=== Debug SQL Query ===\n";
$query = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
    ->where('status', 'Buka')
    ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
    ->orderBy('tanggal_mulai', 'desc');

echo "SQL: " . $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";

echo "\n=== Alternative Queries ===\n";

// Try without JSON contains
echo "1. Without JSON contains:\n";
$alt1 = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
    ->where('status', 'Buka')
    ->get();
echo "   Found: " . $alt1->count() . " records\n";

// Try with LIKE
echo "2. With LIKE search:\n";
$alt2 = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
    ->where('status', 'Buka')
    ->where('status_kepegawaian', 'like', '%' . $pegawai->status_kepegawaian . '%')
    ->get();
echo "   Found: " . $alt2->count() . " records\n";

// Try with raw JSON
echo "3. With raw JSON query:\n";
$alt3 = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
    ->where('status', 'Buka')
    ->whereRaw("JSON_CONTAINS(status_kepegawaian, ?)", ['"' . $pegawai->status_kepegawaian . '"'])
    ->get();
echo "   Found: " . $alt3->count() . " records\n";

echo "\n=== Recommendations ===\n";
if ($periodeUsulans->count() == 0) {
    echo "1. Periksa apakah ada data periode usulan di database\n";
    echo "2. Periksa format JSON pada kolom status_kepegawaian\n";
    echo "3. Periksa apakah status kepegawaian pegawai sesuai\n";
    echo "4. Periksa apakah jenis usulan periode sesuai\n";
    echo "5. Periksa apakah status periode adalah 'Buka'\n";
} else {
    echo "✅ Data periode usulan ditemukan\n";
}

echo "\n=== Sample Data for Testing ===\n";
echo "Jika tidak ada data, Anda bisa menambahkan sample data:\n";
echo "INSERT INTO periode_usulans (nama_periode, jenis_usulan, status_kepegawaian, status, tanggal_mulai, tanggal_selesai) VALUES\n";
echo "('Periode Test 2024', '$jenisUsulanPeriode', '[\"$pegawai->status_kepegawaian\"]', 'Buka', '2024-01-01', '2024-12-31');\n";
?>
