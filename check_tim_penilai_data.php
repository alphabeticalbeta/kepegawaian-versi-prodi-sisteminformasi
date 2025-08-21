<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== DATA USULAN UNTUK TIM PENILAI ===\n\n";

// Cek usulan dengan status 'Sedang Direview'
$usulans = Usulan::where('status_usulan', 'Sedang Direview')
    ->with(['pegawai', 'periodeUsulan'])
    ->get();

echo "Total usulan 'Sedang Direview': " . $usulans->count() . "\n\n";

if ($usulans->count() > 0) {
    echo "Daftar usulan:\n";
    foreach ($usulans as $usulan) {
        echo "- ID: {$usulan->id}\n";
        echo "  Jenis: {$usulan->jenis_usulan}\n";
        echo "  Status: {$usulan->status_usulan}\n";
        echo "  Pegawai: " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . "\n";
        echo "  NIP: " . ($usulan->pegawai->nip ?? 'N/A') . "\n";
        echo "  Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n";
        echo "  Created: " . $usulan->created_at->format('d/m/Y H:i') . "\n";
        echo "  ---\n";
    }
} else {
    echo "Tidak ada usulan dengan status 'Sedang Direview'\n\n";

    // Cek semua status usulan yang ada
    echo "Status usulan yang tersedia:\n";
    $allStatuses = Usulan::select('status_usulan')
        ->distinct()
        ->pluck('status_usulan');

    foreach ($allStatuses as $status) {
        $count = Usulan::where('status_usulan', $status)->count();
        echo "- {$status}: {$count} usulan\n";
    }
}

echo "\n=== SELESAI ===\n";
