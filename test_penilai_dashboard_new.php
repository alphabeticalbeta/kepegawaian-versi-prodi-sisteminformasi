<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

echo "=== TEST PENILAI DASHBOARD NEW ===\n\n";

// Test active periods
echo "🔍 Test Active Periods:\n";
$activePeriods = PeriodeUsulan::where('status', 'Buka')
    ->with(['usulans' => function($query) {
        $query->with('pegawai:id,nama_lengkap,nip')
              ->latest()
              ->limit(5);
    }])
    ->get();

echo "Total active periods: " . $activePeriods->count() . "\n";
foreach ($activePeriods as $periode) {
    echo "- {$periode->nama_periode} ({$periode->jenis_usulan})\n";
    echo "  Status: {$periode->status}\n";
    echo "  Usulans count: " . $periode->usulans->count() . "\n";

    // Count usulans for assessment
    $usulansForAssessment = $periode->usulans->where('status_usulan', 'Sedang Direview');
    $jumlahPenilaian = $usulansForAssessment->count();
    echo "  Usulans for assessment (Sedang Direview): {$jumlahPenilaian}\n";

    if ($jumlahPenilaian > 0) {
        foreach ($usulansForAssessment as $usulan) {
            echo "    → ID: {$usulan->id} | {$usulan->jenis_usulan} | " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . "\n";
        }
    }
    echo "  ---\n";
}

// Test recent usulans
echo "\n🔍 Test Recent Usulans:\n";
$recentUsulans = Usulan::where('status_usulan', 'Sedang Direview')
    ->with(['pegawai:id,nama_lengkap,nip', 'periodeUsulan'])
    ->latest()
    ->limit(10)
    ->get();

echo "Total recent usulans (Sedang Direview): " . $recentUsulans->count() . "\n";
foreach ($recentUsulans as $usulan) {
    echo "- ID: {$usulan->id} | {$usulan->jenis_usulan} | " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . " | " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n";
}

// Test statistics
echo "\n🔍 Test Statistics:\n";
$stats = [
    'total_periods' => $activePeriods->count(),
    'total_usulans_pending' => $recentUsulans->count(),
    'total_usulans_all' => Usulan::count(),
    'usulans_by_status' => [
        'Diajukan' => Usulan::where('status_usulan', 'Diajukan')->count(),
        'Diusulkan ke Universitas' => Usulan::where('status_usulan', 'Diusulkan ke Universitas')->count(),
        'Sedang Direview' => Usulan::where('status_usulan', 'Sedang Direview')->count(),
        'Direkomendasikan' => Usulan::where('status_usulan', 'Direkomendasikan')->count(),
        'Disetujui' => Usulan::where('status_usulan', 'Disetujui')->count(),
        'Ditolak' => Usulan::where('status_usulan', 'Ditolak')->count(),
    ]
];

echo "Total periods: {$stats['total_periods']}\n";
echo "Total usulans pending: {$stats['total_usulans_pending']}\n";
echo "Total usulans all: {$stats['total_usulans_all']}\n";
echo "Usulans by status:\n";
foreach ($stats['usulans_by_status'] as $status => $count) {
    echo "  - {$status}: {$count}\n";
}

echo "\n=== SELESAI ===\n";
