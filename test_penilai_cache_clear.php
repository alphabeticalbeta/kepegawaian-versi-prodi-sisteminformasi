<?php

require_once 'vendor/autoload.php';

use App\Models\BackendUnivUsulan\Usulan;
use App\Services\PenilaiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PENILAI CACHE CLEAR ===\n\n";

try {
    // Test 1: Clear all caches
    echo "1. Clearing all caches...\n";
    
    Cache::flush();
    echo "   ✅ Cache cleared\n";
    
    // Clear route cache
    Artisan::call('route:clear');
    echo "   ✅ Route cache cleared\n";
    
    // Clear config cache
    Artisan::call('config:clear');
    echo "   ✅ Config cache cleared\n";
    
    // Clear view cache
    Artisan::call('view:clear');
    echo "   ✅ View cache cleared\n";
    
    // Test 2: Check usulan ID 18 after cache clear
    echo "\n2. Testing usulan ID 18 after cache clear...\n";
    
    $usulan = Usulan::with(['penilais'])->find(18);
    if (!$usulan) {
        echo "   ❌ Usulan ID 18 not found\n";
        exit;
    }
    
    echo "   ✅ Usulan found: ID {$usulan->id}, Status: {$usulan->status_usulan}\n";
    echo "   📊 Total penilai assigned: " . $usulan->penilais->count() . "\n";
    
    // Test 3: Check all penilai statuses
    echo "\n3. Testing all penilai statuses...\n";
    
    foreach ($usulan->penilais as $penilai) {
        echo "   📋 Penilai: {$penilai->name} (ID: {$penilai->id})\n";
        echo "      Status: " . ($penilai->pivot->status_penilaian ?? 'Belum Dinilai') . "\n";
        echo "      Catatan: " . ($penilai->pivot->catatan_penilaian ?? '-') . "\n";
        echo "      Updated: " . ($penilai->pivot->updated_at ?? '-') . "\n";
    }
    
    // Test 4: Check PenilaiService after cache clear
    echo "\n4. Testing PenilaiService after cache clear...\n";
    
    $penilaiService = app(PenilaiService::class);
    
    foreach ($usulan->penilais as $penilai) {
        $individualStatus = $penilaiService->getPenilaiIndividualStatus($usulan, $penilai->id);
        echo "   👤 Penilai {$penilai->name} (ID: {$penilai->id}):\n";
        echo "      Status: {$individualStatus['status']}\n";
        echo "      Is Completed: " . ($individualStatus['is_completed'] ? 'YES' : 'NO') . "\n";
        echo "      Catatan: {$individualStatus['catatan']}\n";
        echo "      Updated: {$individualStatus['updated_at']}\n";
    }
    
    // Test 5: Check canEdit logic after cache clear
    echo "\n5. Testing canEdit logic after cache clear...\n";
    
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
    
    // Test 6: Check determinePenilaiFinalStatus after cache clear
    echo "\n6. Testing determinePenilaiFinalStatus after cache clear...\n";
    
    $finalStatus = $usulan->determinePenilaiFinalStatus();
    echo "   🎯 Final Status: " . ($finalStatus ?? 'null') . "\n";
    
    // Test 7: Check autoUpdateStatusBasedOnPenilaiProgress after cache clear
    echo "\n7. Testing autoUpdateStatusBasedOnPenilaiProgress after cache clear...\n";
    
    $oldStatus = $usulan->status_usulan;
    $statusWasUpdated = $usulan->autoUpdateStatusBasedOnPenilaiProgress();
    
    echo "   📈 Status was updated: " . ($statusWasUpdated ? 'YES' : 'NO') . "\n";
    if ($statusWasUpdated) {
        echo "   🔄 Status changed from '{$oldStatus}' to '{$usulan->status_usulan}'\n";
    }
    
    // Test 8: Check getPenilaiAssessmentProgress after cache clear
    echo "\n8. Testing getPenilaiAssessmentProgress after cache clear...\n";
    
    $progress = $usulan->getPenilaiAssessmentProgress();
    echo "   📊 Progress Info:\n";
    echo "      Total Penilai: {$progress['total_penilai']}\n";
    echo "      Completed: {$progress['completed_penilai']}\n";
    echo "      Remaining: {$progress['remaining_penilai']}\n";
    echo "      Progress %: {$progress['progress_percentage']}%\n";
    echo "      Is Complete: " . ($progress['is_complete'] ? 'YES' : 'NO') . "\n";
    echo "      Is Intermediate: " . ($progress['is_intermediate'] ? 'YES' : 'NO') . "\n";
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\n📝 RECOMMENDATIONS:\n";
    echo "1. Clear browser cache and cookies\n";
    echo "2. Try accessing the page again\n";
    echo "3. Check if the issue persists\n";
    echo "4. If issue persists, check browser console for errors\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
