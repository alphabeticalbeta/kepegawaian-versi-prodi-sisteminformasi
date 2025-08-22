<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

// Test Dashboard Controller Logic
echo "=== TEST TIM SENAT DASHBOARD ===\n\n";

try {
    // Test 1: Get usulans for Tim Senat
    echo "1. Testing Usulan Query for Tim Senat...\n";
    
    $usulans = Usulan::with([
        'pegawai:id,nama_lengkap,nip,unit_kerja_id',
        'pegawai.unitKerja:id,nama',
        'jabatanLama:id,jabatan',
        'jabatanTujuan:id,jabatan',
        'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai'
    ])
    ->whereIn('status_usulan', [
        'Direkomendasikan',
        'Disetujui',
        'Ditolak',
        'Diusulkan ke Sister',
        'Perbaikan dari Tim Sister'
    ])
    ->latest()
    ->get();

    echo "   Total usulans found: " . $usulans->count() . "\n";
    
    // Test 2: Check statistics
    echo "\n2. Testing Statistics...\n";
    echo "   Menunggu Keputusan (Direkomendasikan): " . $usulans->where('status_usulan', 'Direkomendasikan')->count() . "\n";
    echo "   Disetujui: " . $usulans->where('status_usulan', 'Disetujui')->count() . "\n";
    echo "   Ditolak: " . $usulans->where('status_usulan', 'Ditolak')->count() . "\n";
    echo "   Total Diproses: " . $usulans->count() . "\n";

    // Test 3: Check periode access
    echo "\n3. Testing Periode Access...\n";
    foreach ($usulans->take(3) as $usulan) {
        $periodeInfo = $usulan->getPeriodeInfo('Tim Senat');
        echo "   Usulan ID {$usulan->id}: Status = {$usulan->status_usulan}, Periode Access = {$periodeInfo['status']}\n";
    }

    // Test 4: Check if usulans have required relationships
    echo "\n4. Testing Relationships...\n";
    $usulanWithRelations = $usulans->first();
    if ($usulanWithRelations) {
        echo "   First usulan has pegawai: " . ($usulanWithRelations->pegawai ? 'Yes' : 'No') . "\n";
        echo "   First usulan has periodeUsulan: " . ($usulanWithRelations->periodeUsulan ? 'Yes' : 'No') . "\n";
        echo "   First usulan has jabatanLama: " . ($usulanWithRelations->jabatanLama ? 'Yes' : 'No') . "\n";
        echo "   First usulan has jabatanTujuan: " . ($usulanWithRelations->jabatanTujuan ? 'Yes' : 'No') . "\n";
    }

    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
