<?php
/**
 * Script untuk menambahkan sample data periode usulan
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Carbon\Carbon;

echo "=== Create Sample Periode Usulan ===\n\n";

// Check existing data
$existingPeriode = PeriodeUsulan::all();
echo "Existing periode usulan: " . $existingPeriode->count() . "\n\n";

if ($existingPeriode->count() > 0) {
    echo "=== Existing Data ===\n";
    foreach ($existingPeriode as $periode) {
        echo "ID: " . $periode->id . "\n";
        echo "Nama: " . $periode->nama_periode . "\n";
        echo "Jenis Usulan: " . $periode->jenis_usulan . "\n";
        echo "Status: " . $periode->status . "\n";
        echo "Status Kepegawaian: " . json_encode($periode->status_kepegawaian) . "\n";
        echo "---\n";
    }
}

// Sample data untuk periode usulan
$sampleData = [
    [
        'nama_periode' => 'Periode Usulan Jabatan Dosen 2024',
        'jenis_usulan' => 'usulan-jabatan-dosen',
        'status_kepegawaian' => ['Dosen PNS'],
        'status' => 'Buka',
        'tanggal_mulai' => '2024-01-01',
        'tanggal_selesai' => '2024-12-31',
        'tanggal_mulai_perbaikan' => '2024-02-01',
        'tanggal_selesai_perbaikan' => '2024-11-30',
        'senat_min_setuju' => 3,
        'tahun_periode' => 2024
    ],
    [
        'nama_periode' => 'Periode Usulan Jabatan Tenaga Kependidikan 2024',
        'jenis_usulan' => 'usulan-jabatan-tendik',
        'status_kepegawaian' => ['Tenaga Kependidikan PNS'],
        'status' => 'Buka',
        'tanggal_mulai' => '2024-01-01',
        'tanggal_selesai' => '2024-12-31',
        'tanggal_mulai_perbaikan' => '2024-02-01',
        'tanggal_selesai_perbaikan' => '2024-11-30',
        'senat_min_setuju' => 3,
        'tahun_periode' => 2024
    ],
    [
        'nama_periode' => 'Periode Usulan Jabatan Dosen 2025',
        'jenis_usulan' => 'usulan-jabatan-dosen',
        'status_kepegawaian' => ['Dosen PNS'],
        'status' => 'Buka',
        'tanggal_mulai' => '2025-01-01',
        'tanggal_selesai' => '2025-12-31',
        'tanggal_mulai_perbaikan' => '2025-02-01',
        'tanggal_selesai_perbaikan' => '2025-11-30',
        'senat_min_setuju' => 3,
        'tahun_periode' => 2025
    ],
    [
        'nama_periode' => 'Periode Usulan Jabatan Tenaga Kependidikan 2025',
        'jenis_usulan' => 'usulan-jabatan-tendik',
        'status_kepegawaian' => ['Tenaga Kependidikan PNS'],
        'status' => 'Buka',
        'tanggal_mulai' => '2025-01-01',
        'tanggal_selesai' => '2025-12-31',
        'tanggal_mulai_perbaikan' => '2025-02-01',
        'tanggal_selesai_perbaikan' => '2025-11-30',
        'senat_min_setuju' => 3,
        'tahun_periode' => 2025
    ]
];

echo "=== Creating Sample Data ===\n";

$createdCount = 0;
foreach ($sampleData as $data) {
    // Check if already exists
    $exists = PeriodeUsulan::where('nama_periode', $data['nama_periode'])
        ->where('jenis_usulan', $data['jenis_usulan'])
        ->exists();

    if (!$exists) {
        try {
            PeriodeUsulan::create($data);
            echo "✅ Created: " . $data['nama_periode'] . "\n";
            $createdCount++;
        } catch (Exception $e) {
            echo "❌ Failed to create: " . $data['nama_periode'] . " - " . $e->getMessage() . "\n";
        }
    } else {
        echo "ℹ️  Already exists: " . $data['nama_periode'] . "\n";
    }
}

echo "\n=== Summary ===\n";
echo "Created: $createdCount new periode usulan\n";

// Show final data
$finalPeriode = PeriodeUsulan::all();
echo "Total periode usulan: " . $finalPeriode->count() . "\n\n";

echo "=== Final Data ===\n";
foreach ($finalPeriode as $periode) {
    echo "ID: " . $periode->id . "\n";
    echo "Nama: " . $periode->nama_periode . "\n";
    echo "Jenis Usulan: " . $periode->jenis_usulan . "\n";
    echo "Status: " . $periode->status . "\n";
    echo "Status Kepegawaian: " . json_encode($periode->status_kepegawaian) . "\n";
    echo "Tanggal Mulai: " . $periode->tanggal_mulai . "\n";
    echo "Tanggal Selesai: " . $periode->tanggal_selesai . "\n";
    echo "---\n";
}

echo "\n=== Test Queries ===\n";

// Test query for Dosen PNS
echo "1. Query for Dosen PNS:\n";
$dosenQuery = PeriodeUsulan::where('jenis_usulan', 'usulan-jabatan-dosen')
    ->where('status', 'Buka')
    ->whereJsonContains('status_kepegawaian', 'Dosen PNS')
    ->get();
echo "   Found: " . $dosenQuery->count() . " records\n";
foreach ($dosenQuery as $periode) {
    echo "   - " . $periode->nama_periode . "\n";
}

// Test query for Tenaga Kependidikan PNS
echo "\n2. Query for Tenaga Kependidikan PNS:\n";
$tendikQuery = PeriodeUsulan::where('jenis_usulan', 'usulan-jabatan-tendik')
    ->where('status', 'Buka')
    ->whereJsonContains('status_kepegawaian', 'Tenaga Kependidikan PNS')
    ->get();
echo "   Found: " . $tendikQuery->count() . " records\n";
foreach ($tendikQuery as $periode) {
    echo "   - " . $periode->nama_periode . "\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Run the debug script: php debug_periode_usulan.php\n";
echo "2. Test the application\n";
echo "3. Check if periode usulan appears in the index page\n";

echo "\n✅ Sample data creation completed!\n";
?>
