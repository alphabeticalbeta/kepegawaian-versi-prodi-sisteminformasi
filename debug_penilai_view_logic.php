<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG PENILAI VIEW LOGIC ===\n\n";

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
    
    // Test 2: Check current user (simulate login)
    echo "\n2. Testing current user...\n";
    
    // Try to get current user
    $currentUser = Auth::user();
    if ($currentUser) {
        echo "   👤 Current user: {$currentUser->name} (ID: {$currentUser->id})\n";
    } else {
        echo "   ⚠️ No user logged in, will test with first penilai\n";
        $currentUser = $usulan->penilais->first();
        if ($currentUser) {
            echo "   👤 Using first penilai: {$currentUser->name} (ID: {$currentUser->id})\n";
        } else {
            echo "   ❌ No penilai found\n";
            exit;
        }
    }
    
    // Test 3: Check if current user is assigned to this usulan
    echo "\n3. Testing user assignment...\n";
    
    $isAssigned = $usulan->isAssignedToPenilai($currentUser->id);
    echo "   🔗 Is assigned to usulan: " . ($isAssigned ? 'YES' : 'NO') . "\n";
    
    if (!$isAssigned) {
        echo "   ❌ User is not assigned to this usulan\n";
        exit;
    }
    
    // Test 4: Check PenilaiService getPenilaiIndividualStatus
    echo "\n4. Testing PenilaiService getPenilaiIndividualStatus...\n";
    
    $penilaiService = app(PenilaiService::class);
    $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $currentUser->id);
    
    echo "   👤 Individual Status for {$currentUser->name}:\n";
    echo "      Status: {$individualStatus['status']}\n";
    echo "      Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
    echo "      Catatan: {$individualStatus['catatan']}\n";
    echo "      Updated: {$individualStatus['updated_at']}\n";
    
    // Test 5: Check canEdit logic (same as controller)
    echo "\n5. Testing canEdit logic (same as controller)...\n";
    
    $allowedStatuses = [
        'Diusulkan ke Universitas',
        'Sedang Direview',
        'Usulan dikirim ke Tim Penilai',
        'Menunggu Hasil Penilaian Tim Penilai'
    ];
    
    $statusCheck = in_array($usulan->status_usulan, $allowedStatuses);
    $completionCheck = !$individualStatus['is_completed'];
    $canEdit = $statusCheck && $completionCheck;
    
    echo "   📋 Current status: {$usulan->status_usulan}\n";
    echo "   ✅ Status in allowed list: " . ($statusCheck ? 'YES' : 'NO') . "\n";
    echo "   ✅ Not completed: " . ($completionCheck ? 'YES' : 'NO') . "\n";
    echo "   🔧 Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
    
    // Test 6: Check what should be displayed in view
    echo "\n6. Testing what should be displayed in view...\n";
    
    if ($canEdit) {
        echo "   📋 Expected view behavior:\n";
        echo "      - Show all validation buttons (Simpan Validasi, Rekomendasikan, Perbaikan, Kembali)\n";
        echo "      - Individual status: Show current status\n";
        echo "      - Message: 'Anda belum menyelesaikan penilaian'\n";
    } else {
        echo "   📋 Expected view behavior:\n";
        echo "      - Hide all validation buttons\n";
        echo "      - Show only 'Kembali' button\n";
        echo "      - Individual status: Show completion status\n";
        echo "      - Message: 'Anda telah menyelesaikan penilaian'\n";
    }
    
    // Test 7: Check view logic simulation
    echo "\n7. Testing view logic simulation...\n";
    
    // Simulate the view logic
    $currentRole = 'Penilai Universitas';
    $roleSlug = 'penilai_universitas';
    
    echo "   🎭 Simulating view logic:\n";
    echo "      Current Role: {$currentRole}\n";
    echo "      Role Slug: {$roleSlug}\n";
    echo "      Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
    
    if ($currentRole === 'Penilai Universitas') {
        if ($canEdit) {
            echo "      → Should show: Action buttons section\n";
        } else {
            echo "      → Should show: Read-only mode section\n";
        }
    }
    
    // Test 8: Check if there's a mismatch between controller and view
    echo "\n8. Testing for controller-view mismatch...\n";
    
    // Check if the issue might be in the view logic
    echo "   🔍 Potential issues:\n";
    echo "      1. View might not be using \$canEdit correctly\n";
    echo "      2. View might have additional conditions\n";
    echo "      3. JavaScript might be interfering\n";
    echo "      4. Cache might be causing issues\n";
    
    // Test 9: Check all penilai statuses for comparison
    echo "\n9. Testing all penilai statuses for comparison...\n";
    
    foreach ($usulan->penilais as $penilai) {
        $penilaiIndividualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        $penilaiCanEdit = $statusCheck && !$penilaiIndividualStatus['is_completed'];
        
        echo "   👤 Penilai {$penilai->name} (ID: {$penilai->id}):\n";
        echo "      Status: {$penilaiIndividualStatus['status']}\n";
        echo "      Is Completed: " . ($penilaiIndividualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Can Edit: " . ($penilaiCanEdit ? 'YES' : 'NO') . "\n";
        
        if ($penilai->id === $currentUser->id) {
            echo "      → THIS IS CURRENT USER\n";
        }
    }
    
    echo "\n=== DEBUG COMPLETED ===\n";
    echo "\n📝 RECOMMENDATIONS:\n";
    echo "1. Check if view is using \$canEdit correctly\n";
    echo "2. Check if there are any additional conditions in view\n";
    echo "3. Clear cache and test again\n";
    echo "4. Check browser console for JavaScript errors\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
