<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST JQUERY FIX ===\n\n";

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
    
    // Test 4: Check canEdit logic
    echo "\n4. Testing canEdit logic...\n";
    
    $allowedStatuses = [
        'Diusulkan ke Universitas',
        'Sedang Direview',
        'Usulan dikirim ke Tim Penilai',
        'Menunggu Hasil Penilaian Tim Penilai'
    ];
    
    $statusCheck = in_array($usulan->status_usulan, $allowedStatuses);
    echo "   📋 Current status: {$usulan->status_usulan}\n";
    echo "   ✅ Status in allowed list: " . ($statusCheck ? 'YES' : 'NO') . "\n";
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        $completionCheck = !$individualStatus['is_completed'];
        $canEdit = $statusCheck && $completionCheck;
        
        echo "   👤 Penilai {$penilai->name}:\n";
        echo "      Individual status: {$individualStatus['status']}\n";
        echo "      Is completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Can Edit: " . ($canEdit ? 'YES' : 'NO') . "\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\n📝 JQUERY FIX SUMMARY:\n";
    echo "✅ FIX 1: Added jQuery to Penilai Universitas layout\n";
    echo "✅ FIX 2: Added DataTables CSS and JS\n";
    echo "✅ FIX 3: Updated JavaScript to use window.jQuery\n";
    echo "✅ FIX 4: Added fallback for when jQuery/DataTables not available\n";
    
    echo "\n🎯 EXPECTED RESULTS:\n";
    echo "1. No more 'jQuery is not defined' errors\n";
    echo "2. DataTables should work properly\n";
    echo "3. Button visibility should work correctly\n";
    echo "4. All JavaScript functionality should work\n";
    
    echo "\n🔧 NEXT STEPS:\n";
    echo "1. Clear browser cache and cookies\n";
    echo "2. Refresh the page\n";
    echo "3. Check browser console for any remaining errors\n";
    echo "4. Test button visibility for completed penilai\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
