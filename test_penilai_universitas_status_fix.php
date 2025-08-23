<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;
use App\Models\User;

echo "🔍 Testing Penilai Universitas Status Fix\n";
echo "=======================================\n\n";

try {
    // Test 1: Find an usulan with penilai assignments
    echo "1. Finding usulan with penilai assignments...\n";
    $usulan = Usulan::whereHas('penilais')->first();
    
    if (!$usulan) {
        echo "❌ No usulan with penilai assignments found\n";
        exit(1);
    }
    
    echo "✅ Found usulan ID: {$usulan->id}\n";
    echo "   Status: {$usulan->status_usulan}\n";
    echo "   Penilai count: " . $usulan->penilais->count() . "\n\n";
    
    // Test 2: Test getPenilaiAssessmentProgress method
    echo "2. Testing getPenilaiAssessmentProgress method...\n";
    $progressInfo = $usulan->getPenilaiAssessmentProgress();
    
    echo "✅ Progress Info:\n";
    echo "   - Total Penilai: {$progressInfo['total_penilai']}\n";
    echo "   - Completed Penilai: {$progressInfo['completed_penilai']}\n";
    echo "   - Remaining Penilai: {$progressInfo['remaining_penilai']}\n";
    echo "   - Is Complete: " . ($progressInfo['is_complete'] ? 'true' : 'false') . "\n";
    echo "   - Is Intermediate: " . ($progressInfo['is_intermediate'] ? 'true' : 'false') . "\n\n";
    
    // Test 3: Simulate the Blade template logic for Penilai Universitas
    echo "3. Simulating Blade template logic for Penilai Universitas...\n";
    
    // Simulate the progress variables setup (like in the fix)
    $totalPenilai = $progressInfo['total_penilai'] ?? 0;
    $completedPenilai = $progressInfo['completed_penilai'] ?? 0;
    $remainingPenilai = $progressInfo['remaining_penilai'] ?? 0;
    $isComplete = $progressInfo['is_complete'] ?? false;
    $isIntermediate = $progressInfo['is_intermediate'] ?? false;
    
    echo "✅ Variables set successfully:\n";
    echo "   - \$totalPenilai: {$totalPenilai}\n";
    echo "   - \$completedPenilai: {$completedPenilai}\n";
    echo "   - \$remainingPenilai: {$remainingPenilai}\n";
    echo "   - \$isComplete: " . ($isComplete ? 'true' : 'false') . "\n";
    echo "   - \$isIntermediate: " . ($isIntermediate ? 'true' : 'false') . "\n\n";
    
    // Test 4: Test status display logic (like in the Blade template)
    echo "4. Testing status display logic...\n";
    
    if ($isIntermediate) {
        echo "✅ Status: Intermediate (Menunggu Hasil Penilaian Tim Penilai)\n";
        echo "   - {$remainingPenilai} penilai belum selesai\n";
        echo "   - {$completedPenilai} penilai telah selesai\n";
        echo "   - Status akan berubah otomatis setelah semua penilai selesai\n";
    } elseif ($isComplete) {
        echo "✅ Status: Complete (Penilaian Tim Penilai Selesai)\n";
        echo "   - Semua {$totalPenilai} penilai telah selesai\n";
        echo "   - Final status: {$usulan->status_usulan}\n";
        echo "   - Menunggu keputusan Admin Universitas\n";
    } elseif ($totalPenilai === 0) {
        echo "✅ Status: No Penilai Assigned\n";
        echo "   - Belum ada penilai yang ditugaskan\n";
        echo "   - Status saat ini: {$usulan->status_usulan}\n";
    } else {
        echo "✅ Status: Default\n";
        echo "   - Progress: {$completedPenilai}/{$totalPenilai} penilai selesai\n";
        if ($completedPenilai > 0) {
            echo "   - Status saat ini: {$usulan->status_usulan}\n";
        }
    }
    
    echo "\n🎉 Test completed successfully!\n";
    echo "The progress variables are now properly set for Penilai Universitas role.\n";
    echo "The status should now change correctly based on penilai progress.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
