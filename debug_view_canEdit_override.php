<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG VIEW CANEDIT OVERRIDE ===\n\n";

try {
    // Test 1: Check usulan ID 18
    echo "1. Testing usulan ID 18...\n";
    
    $usulan = Usulan::with(['penilais'])->find(18);
    if (!$usulan) {
        echo "   ❌ Usulan ID 18 not found\n";
        exit;
    }
    
    echo "   ✅ Usulan found: ID {$usulan->id}, Status: {$usulan->status_usulan}\n";
    
    // Test 2: Check current user
    echo "\n2. Testing current user...\n";
    
    $currentUser = Auth::user();
    if ($currentUser) {
        echo "   👤 Current user: {$currentUser->name} (ID: {$currentUser->id})\n";
    } else {
        echo "   ⚠️ No user logged in\n";
        echo "   🔍 Testing with penilai ID 1 (from database screenshot)\n";
        $currentUser = (object) ['id' => 1, 'name' => 'Penilai 1'];
    }
    
    // Test 3: Check PenilaiService getPenilaiIndividualStatus
    echo "\n3. Testing PenilaiService getPenilaiIndividualStatus...\n";
    
    $penilaiService = app(PenilaiService::class);
    $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $currentUser->id);
    
    echo "   👤 Individual Status for {$currentUser->name}:\n";
    echo "      Status: {$individualStatus['status']}\n";
    echo "      Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
    echo "      Catatan: {$individualStatus['catatan']}\n";
    echo "      Updated: {$individualStatus['updated_at']}\n";
    
    // Test 4: Check canEdit logic (EXACTLY as in controller)
    echo "\n4. Testing canEdit logic (EXACTLY as in controller)...\n";
    
    $allowedStatuses = [
        'Diusulkan ke Universitas',
        'Sedang Direview',
        'Usulan dikirim ke Tim Penilai',
        'Menunggu Hasil Penilaian Tim Penilai'
    ];
    
    $statusCheck = in_array($usulan->status_usulan, $allowedStatuses);
    $completionCheck = !$individualStatus['is_completed'];
    $canEdit = $statusCheck && $completionCheck;
    
    echo "   📋 Controller canEdit calculation:\n";
    echo "      Current status: '{$usulan->status_usulan}'\n";
    echo "      Status check: " . ($statusCheck ? 'TRUE' : 'FALSE') . "\n";
    echo "      Individual status: '{$individualStatus['status']}'\n";
    echo "      Is completed: " . ($individualStatus['is_completed'] ? 'TRUE' : 'FALSE') . "\n";
    echo "      Completion check: " . ($completionCheck ? 'TRUE' : 'FALSE') . "\n";
    echo "      Final canEdit: " . ($canEdit ? 'TRUE' : 'FALSE') . "\n";
    
    // Test 5: Check if there are any other canEdit calculations in view
    echo "\n5. Testing potential view canEdit overrides...\n";
    
    // Check if there might be other logic that could override canEdit
    $currentRole = 'Penilai Universitas';
    $roleSlug = 'penilai_universitas';
    
    echo "   🎭 View variables:\n";
    echo "      Current Role: {$currentRole}\n";
    echo "      Role Slug: {$roleSlug}\n";
    echo "      Can Edit (from controller): " . ($canEdit ? 'TRUE' : 'FALSE') . "\n";
    
    // Test 6: Check if there are any additional conditions in view
    echo "\n6. Testing additional view conditions...\n";
    
    // Check if there might be additional conditions that could affect button display
    $hasAccess = $usulan->isAssignedToPenilai($currentUser->id);
    $isInCorrectStatus = in_array($usulan->status_usulan, $allowedStatuses);
    
    echo "   🔍 Additional conditions:\n";
    echo "      Has access to usulan: " . ($hasAccess ? 'TRUE' : 'FALSE') . "\n";
    echo "      Is in correct status: " . ($isInCorrectStatus ? 'TRUE' : 'FALSE') . "\n";
    echo "      Individual not completed: " . ($completionCheck ? 'TRUE' : 'FALSE') . "\n";
    
    // Test 7: Check what should happen in view based on all conditions
    echo "\n7. Testing view behavior based on all conditions...\n";
    
    if ($currentRole === 'Penilai Universitas') {
        if ($canEdit) {
            echo "   ✅ View should show: Action buttons section\n";
            echo "      - @if(\$canEdit) = TRUE\n";
            echo "      - Show all validation buttons\n";
        } else {
            echo "   ❌ View should show: Read-only mode section\n";
            echo "      - @if(\$canEdit) = FALSE\n";
            echo "      - Show only 'Kembali' button\n";
            echo "      - Show completion message\n";
        }
    }
    
    // Test 8: Check if there might be JavaScript interference
    echo "\n8. Testing potential JavaScript interference...\n";
    
    echo "   🔍 Potential issues:\n";
    echo "      1. JavaScript might be hiding/showing buttons\n";
    echo "      2. AJAX calls might be updating button visibility\n";
    echo "      3. Browser cache might be showing old content\n";
    echo "      4. Session data might be outdated\n";
    
    // Test 9: Check if the issue is in the view logic itself
    echo "\n9. Testing view logic simulation...\n";
    
    // Simulate the exact view logic
    $viewCanEdit = $canEdit; // This should be the same as controller canEdit
    
    echo "   🎭 View logic simulation:\n";
    echo "      Controller canEdit: " . ($canEdit ? 'TRUE' : 'FALSE') . "\n";
    echo "      View canEdit: " . ($viewCanEdit ? 'TRUE' : 'FALSE') . "\n";
    
    if ($viewCanEdit === $canEdit) {
        echo "      ✅ View canEdit matches controller canEdit\n";
    } else {
        echo "      ❌ View canEdit does NOT match controller canEdit\n";
        echo "      🔍 This indicates a view logic override\n";
    }
    
    // Test 10: Check if there are any other variables that might affect button display
    echo "\n10. Testing other variables that might affect button display...\n";
    
    // Check if there are any other variables that might be used in view
    $config = [
        'canReturn' => false,
        'canForward' => false,
        'nextStatus' => null
    ];
    
    echo "   🔍 Other view variables:\n";
    echo "      Config canReturn: " . ($config['canReturn'] ? 'TRUE' : 'FALSE') . "\n";
    echo "      Config canForward: " . ($config['canForward'] ? 'TRUE' : 'FALSE') . "\n";
    echo "      Config nextStatus: " . ($config['nextStatus'] ?? 'NULL') . "\n";
    
    echo "\n=== DEBUG COMPLETED ===\n";
    echo "\n📝 SUMMARY:\n";
    echo "🔧 Controller canEdit: " . ($canEdit ? 'TRUE' : 'FALSE') . "\n";
    echo "👤 Individual Status: {$individualStatus['status']}\n";
    echo "✅ Is Completed: " . ($individualStatus['is_completed'] ? 'TRUE' : 'FALSE') . "\n";
    echo "📋 Usulan Status: {$usulan->status_usulan}\n";
    
    if (!$canEdit) {
        echo "\n🎯 EXPECTED BEHAVIOR:\n";
        echo "   - Buttons should be HIDDEN\n";
        echo "   - Only 'Kembali' button should be shown\n";
        echo "   - Status message should show completion\n";
        echo "\n🔍 IF BUTTONS ARE STILL SHOWING:\n";
        echo "   1. Check browser cache and cookies\n";
        echo "   2. Check browser console for JavaScript errors\n";
        echo "   3. Check if there are any AJAX calls updating the page\n";
        echo "   4. Check if there are any session issues\n";
    } else {
        echo "\n🎯 EXPECTED BEHAVIOR:\n";
        echo "   - All validation buttons should be SHOWN\n";
        echo "   - Status should show current progress\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
