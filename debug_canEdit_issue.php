<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG CANEDIT ISSUE ===\n\n";

try {
    // Test 1: Check usulan ID 18
    echo "1. Testing usulan ID 18...\n";
    
    $usulan = Usulan::with(['penilais'])->find(18);
    if (!$usulan) {
        echo "   ❌ Usulan ID 18 not found\n";
        exit;
    }
    
    echo "   ✅ Usulan found: ID {$usulan->id}, Status: {$usulan->status_usulan}\n";
    
    // Test 2: Check all penilai for this usulan
    echo "\n2. Testing all penilai for usulan 18...\n";
    
    foreach ($usulan->penilais as $penilai) {
        echo "   📋 Penilai: {$penilai->name} (ID: {$penilai->id})\n";
        echo "      Status: " . ($penilai->pivot->status_penilaian ?? 'Belum Dinilai') . "\n";
        echo "      Catatan: " . ($penilai->pivot->catatan_penilaian ?? '-') . "\n";
        echo "      Updated: " . ($penilai->pivot->updated_at ?? '-') . "\n";
    }
    
    // Test 3: Check current user
    echo "\n3. Testing current user...\n";
    
    $currentUser = Auth::user();
    if ($currentUser) {
        echo "   👤 Current user: {$currentUser->name} (ID: {$currentUser->id})\n";
    } else {
        echo "   ⚠️ No user logged in\n";
        echo "   🔍 Testing with penilai ID 1 (from database screenshot)\n";
        $currentUser = (object) ['id' => 1, 'name' => 'Penilai 1'];
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
    
    // Test 5: Check canEdit logic step by step (EXACTLY as in controller)
    echo "\n5. Testing canEdit logic step by step (EXACTLY as in controller)...\n";
    
    $allowedStatuses = [
        'Diusulkan ke Universitas',
        'Sedang Direview',
        'Usulan dikirim ke Tim Penilai',
        'Menunggu Hasil Penilaian Tim Penilai'
    ];
    
    $statusCheck = in_array($usulan->status_usulan, $allowedStatuses);
    $completionCheck = !$individualStatus['is_completed'];
    $canEdit = $statusCheck && $completionCheck;
    
    echo "   📋 Step-by-step analysis:\n";
    echo "      Current status: '{$usulan->status_usulan}'\n";
    echo "      Allowed statuses: " . implode(', ', $allowedStatuses) . "\n";
    echo "      Status check (in_array): " . ($statusCheck ? 'TRUE' : 'FALSE') . "\n";
    echo "      Individual status: '{$individualStatus['status']}'\n";
    echo "      Is completed: " . ($individualStatus['is_completed'] ? 'TRUE' : 'FALSE') . "\n";
    echo "      Completion check (!is_completed): " . ($completionCheck ? 'TRUE' : 'FALSE') . "\n";
    echo "      Final canEdit (statusCheck && completionCheck): " . ($canEdit ? 'TRUE' : 'FALSE') . "\n";
    
    // Test 6: Check what should happen in view
    echo "\n6. Testing what should happen in view...\n";
    
    if ($canEdit) {
        echo "   ✅ View should show: Action buttons section\n";
        echo "      - Simpan Validasi button\n";
        echo "      - Rekomendasikan button\n";
        echo "      - Perbaikan button\n";
        echo "      - Kembali button\n";
    } else {
        echo "   ❌ View should show: Read-only mode section\n";
        echo "      - Status message: 'Anda telah menyelesaikan penilaian'\n";
        echo "      - Only Kembali button\n";
    }
    
    // Test 7: Check if there's a mismatch
    echo "\n7. Testing for potential issues...\n";
    
    // Check if the issue might be in the logic
    if ($individualStatus['status'] === 'Perlu Perbaikan' && $individualStatus['is_completed']) {
        echo "   🔍 ISSUE FOUND: Penilai has 'Perlu Perbaikan' status and is marked as completed\n";
        echo "      This means canEdit should be FALSE\n";
        echo "      But buttons might still be showing\n";
    }
    
    // Test 8: Check raw database data
    echo "\n8. Testing raw database data...\n";
    
    $rawData = DB::table('usulan_penilai')
        ->where('usulan_id', 18)
        ->where('penilai_id', $currentUser->id)
        ->first();
    
    if ($rawData) {
        echo "   📋 Raw database data for current user:\n";
        echo "      ID: {$rawData->id}\n";
        echo "      Usulan ID: {$rawData->usulan_id}\n";
        echo "      Penilai ID: {$rawData->penilai_id}\n";
        echo "      Status: {$rawData->status_penilaian}\n";
        echo "      Catatan: {$rawData->catatan_penilaian}\n";
        echo "      Created: {$rawData->created_at}\n";
        echo "      Updated: {$rawData->updated_at}\n";
    } else {
        echo "   ❌ No raw data found for current user\n";
    }
    
    // Test 9: Check if the issue is in the view logic
    echo "\n9. Testing view logic simulation...\n";
    
    // Simulate the exact view logic
    $currentRole = 'Penilai Universitas';
    
    echo "   🎭 View logic simulation:\n";
    echo "      Current Role: {$currentRole}\n";
    echo "      Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
    
    if ($currentRole === 'Penilai Universitas') {
        if ($canEdit) {
            echo "      → @if(\$canEdit) = TRUE\n";
            echo "      → Should show action buttons\n";
        } else {
            echo "      → @if(\$canEdit) = FALSE\n";
            echo "      → Should show read-only mode\n";
        }
    }
    
    echo "\n=== DEBUG COMPLETED ===\n";
    echo "\n📝 SUMMARY:\n";
    echo "🔧 Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
    echo "👤 Individual Status: {$individualStatus['status']}\n";
    echo "✅ Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
    echo "📋 Usulan Status: {$usulan->status_usulan}\n";
    
    if (!$canEdit) {
        echo "\n🎯 EXPECTED BEHAVIOR:\n";
        echo "   - Buttons should be HIDDEN\n";
        echo "   - Only 'Kembali' button should be shown\n";
        echo "   - Status message should show completion\n";
    } else {
        echo "\n🎯 EXPECTED BEHAVIOR:\n";
        echo "   - All validation buttons should be SHOWN\n";
        echo "   - Status should show current progress\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
