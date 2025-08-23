<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PENILAI BUTTON STATUS ISSUE ===\n\n";

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
    
    // Test 2: Check individual penilai status
    echo "\n2. Testing individual penilai status...\n";
    
    foreach ($usulan->penilais as $penilai) {
        echo "   📋 Penilai: {$penilai->name} (ID: {$penilai->id})\n";
        echo "      Status: " . ($penilai->pivot->status_penilaian ?? 'Belum Dinilai') . "\n";
        echo "      Catatan: " . ($penilai->pivot->catatan_penilaian ?? '-') . "\n";
        echo "      Updated: " . ($penilai->pivot->updated_at ?? '-') . "\n";
    }
    
    // Test 3: Check canEdit logic for Penilai Universitas
    echo "\n3. Testing canEdit logic for Penilai Universitas...\n";
    
    $allowedStatuses = [
        'Diusulkan ke Universitas',
        'Sedang Direview',
        'Usulan dikirim ke Tim Penilai',
        'Menunggu Hasil Penilaian Tim Penilai'
    ];
    
    $canEdit = in_array($usulan->status_usulan, $allowedStatuses);
    echo "   📋 Current status: {$usulan->status_usulan}\n";
    echo "   ✅ Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
    
    // Test 4: Check what should happen after validation
    echo "\n4. Testing post-validation scenarios...\n";
    
    $penilaiService = app(PenilaiService::class);
    $firstPenilai = $usulan->penilais->first();
    
    if ($firstPenilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $firstPenilai->id);
        echo "   👤 Individual Status for Penilai {$firstPenilai->name}:\n";
        echo "      Status: {$individualStatus['status']}\n";
        echo "      Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        
        // Check what should happen if this penilai has completed assessment
        if ($individualStatus['is_completed']) {
            echo "   🔍 ANALYSIS: This penilai has completed assessment\n";
            echo "      Expected behavior:\n";
            echo "      - Buttons should be HIDDEN (except Kembali)\n";
            echo "      - Status should show individual completion\n";
            echo "      - Usulan status should be intermediate if other penilai haven't completed\n";
        } else {
            echo "   🔍 ANALYSIS: This penilai has NOT completed assessment\n";
            echo "      Expected behavior:\n";
            echo "      - Buttons should be SHOWN\n";
            echo "      - Status should be 'Belum Dinilai'\n";
        }
    }
    
    // Test 5: Check determinePenilaiFinalStatus logic
    echo "\n5. Testing determinePenilaiFinalStatus logic...\n";
    
    $finalStatus = $usulan->determinePenilaiFinalStatus();
    echo "   🎯 Final Status: " . ($finalStatus ?? 'null') . "\n";
    
    // Test 6: Check what status each penilai should have
    echo "\n6. Testing expected status for each penilai...\n";
    
    $completedPenilai = $usulan->penilais->whereNotIn('pivot.status_penilaian', ['Belum Dinilai'])->count();
    $totalPenilai = $usulan->penilais->count();
    
    echo "   📊 Total penilai: {$totalPenilai}\n";
    echo "   ✅ Completed penilai: {$completedPenilai}\n";
    echo "   ⏳ Remaining penilai: " . ($totalPenilai - $completedPenilai) . "\n";
    
    if ($completedPenilai > 0 && $completedPenilai < $totalPenilai) {
        echo "   🔍 ANALYSIS: Some penilai have completed, others haven't\n";
        echo "      Expected behavior:\n";
        echo "      - Completed penilai: Status should show their individual result\n";
        echo "      - Incomplete penilai: Status should be 'Belum Dinilai'\n";
        echo "      - Usulan status: Should be 'Menunggu Hasil Penilaian Tim Penilai'\n";
        echo "      - Buttons: Should be hidden for completed penilai, shown for incomplete\n";
    } elseif ($completedPenilai === $totalPenilai && $totalPenilai > 0) {
        echo "   🔍 ANALYSIS: All penilai have completed\n";
        echo "      Expected behavior:\n";
        echo "      - All buttons should be HIDDEN (except Kembali)\n";
        echo "      - Usulan status: Should be final result\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\n📝 ISSUE ANALYSIS:\n";
    echo "❌ PROBLEM 1: After validation, buttons should be hidden for completed penilai\n";
    echo "❌ PROBLEM 2: Status should show individual completion, not general status\n";
    echo "❌ PROBLEM 3: Only 'Kembali' button should be shown for completed penilai\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
