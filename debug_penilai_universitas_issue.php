<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG PENILAI UNIVERSITAS ISSUE ===\n\n";

try {
    // Test 1: Check usulan with ID 18 specifically
    echo "1. Testing usulan ID 18...\n";
    
    $usulan = Usulan::with(['penilais'])->find(18);
    if (!$usulan) {
        echo "   ❌ Usulan ID 18 not found\n";
        exit;
    }
    
    echo "   ✅ Usulan found: ID {$usulan->id}, Status: {$usulan->status_usulan}\n";
    echo "   📊 Total penilai assigned: " . $usulan->penilais->count() . "\n";
    
    // Test 2: Check individual penilai status from database
    echo "\n2. Testing individual penilai status from database...\n";
    
    foreach ($usulan->penilais as $penilai) {
        echo "   📋 Penilai: {$penilai->name} (ID: {$penilai->id})\n";
        echo "      Status: " . ($penilai->pivot->status_penilaian ?? 'Belum Dinilai') . "\n";
        echo "      Catatan: " . ($penilai->pivot->catatan_penilaian ?? '-') . "\n";
        echo "      Updated: " . ($penilai->pivot->updated_at ?? '-') . "\n";
        echo "      Created: " . ($penilai->pivot->created_at ?? '-') . "\n";
    }
    
    // Test 3: Check PenilaiService getPenilaiIndividualStatus method
    echo "\n3. Testing PenilaiService getPenilaiIndividualStatus method...\n";
    
    $penilaiService = app(PenilaiService::class);
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        echo "   👤 Penilai {$penilai->name} (ID: {$penilai->id}):\n";
        echo "      Status: {$individualStatus['status']}\n";
        echo "      Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Catatan: {$individualStatus['catatan']}\n";
        echo "      Updated: {$individualStatus['updated_at']}\n";
    }
    
    // Test 4: Check canEdit logic step by step
    echo "\n4. Testing canEdit logic step by step...\n";
    
    $allowedStatuses = [
        'Diusulkan ke Universitas',
        'Sedang Direview',
        'Usulan dikirim ke Tim Penilai',
        'Menunggu Hasil Penilaian Tim Penilai'
    ];
    
    $statusCheck = in_array($usulan->status_usulan, $allowedStatuses);
    echo "   📋 Current status: {$usulan->status_usulan}\n";
    echo "   ✅ Status in allowed list: " . ($statusCheck ? 'YES' : 'NO') . "\n";
    
    // Test for each penilai
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        $completionCheck = !$individualStatus['is_completed'];
        $canEdit = $statusCheck && $completionCheck;
        
        echo "   👤 Penilai {$penilai->name}:\n";
        echo "      Individual status: {$individualStatus['status']}\n";
        echo "      Is completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Completion check (not completed): " . ($completionCheck ? 'YES' : 'NO') . "\n";
        echo "      Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
    }
    
    // Test 5: Check determinePenilaiFinalStatus method
    echo "\n5. Testing determinePenilaiFinalStatus method...\n";
    
    $finalStatus = $usulan->determinePenilaiFinalStatus();
    echo "   🎯 Final Status: " . ($finalStatus ?? 'null') . "\n";
    
    // Test 6: Check autoUpdateStatusBasedOnPenilaiProgress method
    echo "\n6. Testing autoUpdateStatusBasedOnPenilaiProgress method...\n";
    
    $oldStatus = $usulan->status_usulan;
    $statusWasUpdated = $usulan->autoUpdateStatusBasedOnPenilaiProgress();
    
    echo "   📈 Status was updated: " . ($statusWasUpdated ? 'YES' : 'NO') . "\n";
    if ($statusWasUpdated) {
        echo "   🔄 Status changed from '{$oldStatus}' to '{$usulan->status_usulan}'\n";
    }
    
    // Test 7: Check getPenilaiAssessmentProgress method
    echo "\n7. Testing getPenilaiAssessmentProgress method...\n";
    
    $progress = $usulan->getPenilaiAssessmentProgress();
    echo "   📊 Progress Info:\n";
    echo "      Total Penilai: {$progress['total_penilai']}\n";
    echo "      Completed: {$progress['completed_penilai']}\n";
    echo "      Remaining: {$progress['remaining_penilai']}\n";
    echo "      Progress %: {$progress['progress_percentage']}%\n";
    echo "      Is Complete: " . ($progress['is_complete'] ? 'YES' : 'NO') . "\n";
    echo "      Is Intermediate: " . ($progress['is_intermediate'] ? 'YES' : 'NO') . "\n";
    
    // Test 8: Check raw database query
    echo "\n8. Testing raw database query...\n";
    
    $rawData = DB::table('usulan_penilai')
        ->where('usulan_id', 18)
        ->get();
    
    echo "   📋 Raw database data for usulan_id 18:\n";
    foreach ($rawData as $row) {
        echo "      ID: {$row->id}, Penilai ID: {$row->penilai_id}, Status: {$row->status_penilaian}, Catatan: {$row->catatan_penilaian}\n";
    }
    
    // Test 9: Check what should happen in view
    echo "\n9. Testing what should happen in view...\n";
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        $canEdit = $statusCheck && !$individualStatus['is_completed'];
        
        echo "   👤 For Penilai {$penilai->name}:\n";
        echo "      Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
        
        if ($canEdit) {
            echo "      Expected: Show all validation buttons\n";
        } else {
            echo "      Expected: Hide validation buttons, show only 'Kembali' button\n";
            echo "      Expected message: 'Anda telah menyelesaikan penilaian'\n";
        }
    }
    
    echo "\n=== DEBUG COMPLETED ===\n";
    echo "\n📝 ANALYSIS:\n";
    echo "🔍 The issue might be:\n";
    echo "1. canEdit logic not working correctly\n";
    echo "2. View not using the correct canEdit value\n";
    echo "3. Individual status not being passed correctly to view\n";
    echo "4. Button display logic in view not working\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
