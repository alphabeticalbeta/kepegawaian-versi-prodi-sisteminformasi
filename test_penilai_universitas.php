<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== TEST PENILAI UNIVERSITAS FUNCTIONALITY ===\n\n";

// Cek route yang tersedia
echo "📋 Route yang tersedia:\n";
echo "- Dashboard: /penilai-universitas/dashboard\n";
echo "- Pusat Usulan: /penilai-universitas/pusat-usulan\n";
echo "- Detail Usulan: /penilai-universitas/pusat-usulan/{id}\n\n";

// Cek usulan dengan status 'Sedang Direview'
$usulans = Usulan::where('status_usulan', 'Sedang Direview')
    ->with(['pegawai', 'periodeUsulan'])
    ->get();

echo "📊 Data Usulan untuk Penilai Universitas:\n";
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

// Cek controller methods
echo "\n🔧 Controller Methods:\n";
echo "- DashboardController::index() - Menampilkan dashboard\n";
echo "- PusatUsulanController::index() - Menampilkan daftar usulan\n";
echo "- PusatUsulanController::show() - Menampilkan detail usulan\n";

// Cek view files
echo "\n📄 View Files:\n";
echo "- Dashboard: resources/views/backend/layouts/views/penilai-universitas/dashboard.blade.php\n";
echo "- Pusat Usulan Index: resources/views/backend/layouts/views/penilai-universitas/pusat-usulan/index.blade.php\n";
echo "- Pusat Usulan Detail: resources/views/backend/layouts/views/penilai-universitas/pusat-usulan/detail-usulan.blade.php\n";

echo "\n=== SELESAI ===\n";
