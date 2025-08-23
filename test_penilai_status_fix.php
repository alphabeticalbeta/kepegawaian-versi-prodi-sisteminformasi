<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PENILAI STATUS FIX ===\n\n";

try {
    // Test 1: Check usulan with penilai assignment
    echo "1. Testing usulan with penilai assignment...\n";
    
    $usulan = Usulan::with(['penilais'])->first();
    if (!$usulan) {
        echo "   ❌ No usulan found\n";
        exit;
    }
    
    echo "   ✅ Usulan found: ID {$usulan->id}, Status: {$usulan->status_usulan}\n";
    echo "   📊 Total penilai assigned: " . $usulan->penilais->count() . "\n";
    
    // Test 2: Check penilai individual status
    echo "\n2. Testing penilai individual status...\n";
    
    foreach ($usulan->penilais as $penilai) {
        echo "   📋 Penilai: {$penilai->name} (ID: {$penilai->id})\n";
        echo "      Status: " . ($penilai->pivot->status_penilaian ?? 'Belum Dinilai') . "\n";
        echo "      Catatan: " . ($penilai->pivot->catatan_penilaian ?? '-') . "\n";
        echo "      Updated: " . ($penilai->pivot->updated_at ?? '-') . "\n";
    }
    
    // Test 3: Test determinePenilaiFinalStatus method
    echo "\n3. Testing determinePenilaiFinalStatus method...\n";
    
    $finalStatus = $usulan->determinePenilaiFinalStatus();
    echo "   🎯 Final Status: " . ($finalStatus ?? 'null') . "\n";
    
    // Test 4: Test autoUpdateStatusBasedOnPenilaiProgress method
    echo "\n4. Testing autoUpdateStatusBasedOnPenilaiProgress method...\n";
    
    $oldStatus = $usulan->status_usulan;
    $statusWasUpdated = $usulan->autoUpdateStatusBasedOnPenilaiProgress();
    
    echo "   📈 Status was updated: " . ($statusWasUpdated ? 'YES' : 'NO') . "\n";
    if ($statusWasUpdated) {
        echo "   🔄 Status changed from '{$oldStatus}' to '{$usulan->status_usulan}'\n";
    }
    
    // Test 5: Test getPenilaiAssessmentProgress method
    echo "\n5. Testing getPenilaiAssessmentProgress method...\n";
    
    $progress = $usulan->getPenilaiAssessmentProgress();
    echo "   📊 Progress Info:\n";
    echo "      Total Penilai: {$progress['total_penilai']}\n";
    echo "      Completed: {$progress['completed_penilai']}\n";
    echo "      Remaining: {$progress['remaining_penilai']}\n";
    echo "      Progress %: {$progress['progress_percentage']}%\n";
    echo "      Is Complete: " . ($progress['is_complete'] ? 'YES' : 'NO') . "\n";
    echo "      Is Intermediate: " . ($progress['is_intermediate'] ? 'YES' : 'NO') . "\n";
    
    // Test 6: Test PenilaiService methods
    echo "\n6. Testing PenilaiService methods...\n";
    
    $penilaiService = app(PenilaiService::class);
    $firstPenilai = $usulan->penilais->first();
    
    if ($firstPenilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $firstPenilai->id);
        echo "   👤 Individual Status for Penilai {$firstPenilai->name}:\n";
        echo "      Status: {$individualStatus['status']}\n";
        echo "      Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Catatan: {$individualStatus['catatan']}\n";
    }
    
    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
