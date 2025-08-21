<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== CHECK PENILAI DASHBOARD DATA ===\n\n";

// Cek semua status usulan yang ada
echo "📊 Status usulan yang tersedia:\n";
$allStatuses = Usulan::select('status_usulan')
    ->distinct()
    ->pluck('status_usulan');

foreach ($allStatuses as $status) {
    $count = Usulan::where('status_usulan', $status)->count();
    echo "- {$status}: {$count} usulan\n";
}

echo "\n📋 Detail usulan per status:\n";
foreach ($allStatuses as $status) {
    $usulans = Usulan::where('status_usulan', $status)
        ->with(['pegawai', 'periodeUsulan'])
        ->get();

    echo "\n--- {$status} ({$usulans->count()} usulan) ---\n";
    foreach ($usulans as $usulan) {
        echo "  ID: {$usulan->id} | {$usulan->jenis_usulan} | " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . " | " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n";
    }
}

// Cek khusus untuk status yang seharusnya muncul di dashboard penilai
echo "\n🔍 Cek untuk dashboard penilai:\n";
$relevantStatuses = ['Sedang Direview', 'Direkomendasikan', 'Diusulkan ke Universitas'];

foreach ($relevantStatuses as $status) {
    $count = Usulan::where('status_usulan', $status)->count();
    echo "- {$status}: {$count} usulan\n";

    if ($count > 0) {
        $usulans = Usulan::where('status_usulan', $status)
            ->with(['pegawai', 'periodeUsulan'])
            ->get();

        foreach ($usulans as $usulan) {
            echo "  → ID: {$usulan->id} | {$usulan->jenis_usulan} | " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . "\n";
        }
    }
}

echo "\n=== SELESAI ===\n";
