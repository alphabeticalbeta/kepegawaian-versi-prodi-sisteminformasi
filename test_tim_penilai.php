<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== TEST TIM PENILAI FUNCTIONALITY ===\n\n";

// Cek usulan dengan status 'Sedang Direview'
$usulan = Usulan::where('status_usulan', 'Sedang Direview')
    ->with(['pegawai', 'periodeUsulan'])
    ->first();

if ($usulan) {
    echo "✅ Usulan ditemukan untuk Tim Penilai:\n";
    echo "- ID: {$usulan->id}\n";
    echo "- Jenis: {$usulan->jenis_usulan}\n";
    echo "- Status: {$usulan->status_usulan}\n";
    echo "- Pegawai: " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . "\n";
    echo "- NIP: " . ($usulan->pegawai->nip ?? 'N/A') . "\n";
    echo "- Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n";

    // Cek route
    echo "\n📋 Route yang tersedia:\n";
    echo "- Index: /tim-penilai/usulan\n";
    echo "- Detail: /tim-penilai/usulan/{$usulan->id}\n";
    echo "- Save Validation: /tim-penilai/usulan/{$usulan->id}/save-validation\n";

    // Cek validasi data
    $validasiData = $usulan->getValidasiByRole('tim_penilai');
    echo "\n📊 Validasi data Tim Penilai:\n";
    if ($validasiData) {
        echo "- Ada data validasi: " . (count($validasiData) > 0 ? 'Ya' : 'Tidak') . "\n";
    } else {
        echo "- Belum ada data validasi\n";
    }

    // Cek action buttons yang tersedia
    echo "\n🔘 Action buttons yang tersedia:\n";
    echo "- Perbaikan Usulan (return_to_pegawai)\n";
    echo "- Rekomendasikan (rekomendasikan)\n";

} else {
    echo "❌ Tidak ada usulan dengan status 'Sedang Direview'\n\n";

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
