<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== TEST PENILAI DASHBOARD ===\n\n";

// Test getPendingAssessments method
echo "🔍 Test getPendingAssessments:\n";
$pendingAssessments = Usulan::with(['pegawai', 'periodeUsulan', 'jabatanLama', 'jabatanTujuan'])
    ->where('status_usulan', 'Sedang Direview')
    ->orderBy('created_at', 'asc')
    ->take(5)
    ->get();

echo "Total pending assessments: " . $pendingAssessments->count() . "\n";
foreach ($pendingAssessments as $assessment) {
    echo "- ID: {$assessment->id} | {$assessment->jenis_usulan} | " . ($assessment->pegawai->nama_lengkap ?? 'N/A') . " | " . ($assessment->jabatanTujuan->jabatan ?? 'N/A') . "\n";
}

// Test getRecentAssessments method
echo "\n🔍 Test getRecentAssessments:\n";
$recentAssessments = Usulan::with(['pegawai', 'periodeUsulan', 'jabatanLama', 'jabatanTujuan'])
    ->whereIn('status_usulan', ['Direkomendasikan', 'Sedang Direview'])
    ->latest()
    ->take(10)
    ->get();

echo "Total recent assessments: " . $recentAssessments->count() . "\n";
foreach ($recentAssessments as $assessment) {
    echo "- ID: {$assessment->id} | {$assessment->jenis_usulan} | " . ($assessment->pegawai->nama_lengkap ?? 'N/A') . " | " . ($assessment->jabatanTujuan->jabatan ?? 'N/A') . " | Status: {$assessment->status_usulan}\n";
}

// Test getAssessmentStatistics method
echo "\n🔍 Test getAssessmentStatistics:\n";
$totalAssessments = Usulan::where('status_usulan', 'Sedang Direview')->count();
$completedAssessments = Usulan::where('status_usulan', 'Direkomendasikan')->count();
$pendingAssessments = Usulan::where('status_usulan', 'Sedang Direview')->count();

echo "Total assessments: {$totalAssessments}\n";
echo "Completed assessments: {$completedAssessments}\n";
echo "Pending assessments: {$pendingAssessments}\n";

echo "\n=== SELESAI ===\n";
