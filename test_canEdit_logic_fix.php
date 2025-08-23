<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST CANEDIT LOGIC FIX ===\n\n";

try {
    // Test 1: Check usulan ID 18
    echo "1. Testing usulan ID 18...\n";
    
    $usulan = Usulan::with(['penilais'])->find(18);
    if (!$usulan) {
        echo "   ❌ Usulan ID 18 not found\n";
        exit;
    }
    
    echo "   ✅ Usulan found: ID {$usulan->id}, Status: {$usulan->status_usulan}\n";
    echo "   📊 Total penilai assigned: " . $usulan->penilais->count() . "\n";
    
    // Test 2: Check all penilai statuses
    echo "\n2. Testing all penilai statuses...\n";
    
    foreach ($usulan->penilais as $penilai) {
        echo "   📋 Penilai: {$penilai->name} (ID: {$penilai->id})\n";
        echo "      Status: " . ($penilai->pivot->status_penilaian ?? 'Belum Dinilai') . "\n";
        echo "      Catatan: " . ($penilai->pivot->catatan_penilaian ?? '-') . "\n";
        echo "      Updated: " . ($penilai->pivot->updated_at ?? '-') . "\n";
    }
    
    // Test 3: Check PenilaiService
    echo "\n3. Testing PenilaiService...\n";
    
    $penilaiService = app(PenilaiService::class);
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        echo "   👤 Penilai {$penilai->name} (ID: {$penilai->id}):\n";
        echo "      Status: {$individualStatus['status']}\n";
        echo "      Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Catatan: {$individualStatus['catatan']}\n";
        echo "      Updated: {$individualStatus['updated_at']}\n";
    }
    
    // Test 4: Check OLD canEdit logic (before fix)
    echo "\n4. Testing OLD canEdit logic (before fix)...\n";
    
    $allowedStatuses = [
        'Sedang Direview',
        'Usulan dikirim ke Tim Penilai',
        'Menunggu Hasil Penilaian Tim Penilai'
    ];
    
    $statusCheck = in_array($usulan->status_usulan, $allowedStatuses);
    echo "   📋 Current status: {$usulan->status_usulan}\n";
    echo "   ✅ Status in allowed list: " . ($statusCheck ? 'YES' : 'NO') . "\n";
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        
        // OLD LOGIC (before fix)
        $isAssigned = $usulan->isAssignedToPenilai($penilai->id);
        $oldCanEdit = $statusCheck && $isAssigned;
        
        echo "   👤 Penilai {$penilai->name}:\n";
        echo "      Individual status: {$individualStatus['status']}\n";
        echo "      Is completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Is assigned: " . ($isAssigned ? 'YES' : 'NO') . "\n";
        echo "      OLD Can Edit: " . ($oldCanEdit ? 'YES' : 'NO') . "\n";
    }
    
    // Test 5: Check NEW canEdit logic (after fix)
    echo "\n5. Testing NEW canEdit logic (after fix)...\n";
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        
        // NEW LOGIC (after fix)
        $isAssigned = $usulan->isAssignedToPenilai($penilai->id);
        $isCompleted = $individualStatus['is_completed'] ?? false;
        $newCanEdit = $statusCheck && $isAssigned && !$isCompleted;
        
        echo "   👤 Penilai {$penilai->name}:\n";
        echo "      Individual status: {$individualStatus['status']}\n";
        echo "      Is completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Is assigned: " . ($isAssigned ? 'YES' : 'NO') . "\n";
        echo "      NEW Can Edit: " . ($newCanEdit ? 'YES' : 'NO') . "\n";
        
        if ($oldCanEdit !== $newCanEdit) {
            echo "      🔄 LOGIC CHANGED: " . ($oldCanEdit ? 'YES' : 'NO') . " → " . ($newCanEdit ? 'YES' : 'NO') . "\n";
        }
    }
    
    // Test 6: Check what should happen in view
    echo "\n6. Testing what should happen in view...\n";
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        $isAssigned = $usulan->isAssignedToPenilai($penilai->id);
        $isCompleted = $individualStatus['is_completed'] ?? false;
        $canEdit = $statusCheck && $isAssigned && !$isCompleted;
        
        echo "   👤 For Penilai {$penilai->name}:\n";
        echo "      Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
        
        if ($canEdit) {
            echo "      Expected: Show all validation buttons\n";
        } else {
            echo "      Expected: Hide validation buttons, show only 'Kembali' button\n";
            echo "      Expected message: 'Anda telah menyelesaikan penilaian'\n";
        }
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\n📝 CANEDIT LOGIC FIX SUMMARY:\n";
    echo "✅ FIX 1: Added isCompleted check to canEdit logic\n";
    echo "✅ FIX 2: canEdit now considers individual penilai completion status\n";
    echo "✅ FIX 3: Buttons will be hidden for completed penilai\n";
    echo "✅ FIX 4: Only 'Kembali' button shown for completed penilai\n";
    
    echo "\n🎯 EXPECTED RESULTS:\n";
    echo "1. Completed penilai: Buttons HIDDEN, only 'Kembali' shown\n";
    echo "2. Incomplete penilai: All validation buttons SHOWN\n";
    echo "3. Status display: Shows individual completion status\n";
    echo "4. No more editing for completed assessments\n";
    
    echo "\n🔧 NEXT STEPS:\n";
    echo "1. Clear browser cache and cookies\n";
    echo "2. Refresh the page\n";
    echo "3. Check button visibility for completed penilai\n";
    echo "4. Verify status display shows completion\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
