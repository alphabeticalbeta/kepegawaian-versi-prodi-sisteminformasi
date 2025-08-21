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

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VIEW TEST ===\n\n";

try {
    // Test 1: Check if view exists
    echo "1. Testing view existence...\n";
    $viewPath = 'backend.layouts.views.admin-univ-usulan.data-pegawai.form-datapegawai';

    if (view()->exists($viewPath)) {
        echo "   ✅ View exists: {$viewPath}\n";
    } else {
        echo "   ❌ View not found: {$viewPath}\n";
    }

    // Test 2: Check actual file path
    echo "\n2. Testing file path...\n";
    $filePath = resource_path("views/{$viewPath}.blade.php");
    echo "   Expected file path: {$filePath}\n";

    if (file_exists($filePath)) {
        echo "   ✅ File exists at: {$filePath}\n";
    } else {
        echo "   ❌ File not found at: {$filePath}\n";
    }

    // Test 3: Check view resolver
    echo "\n3. Testing view resolver...\n";
    $resolver = app('view.engine.resolver');
    echo "   View resolver: " . get_class($resolver) . "\n";

    // Test 4: Check view paths
    echo "\n4. Testing view paths...\n";
    $finder = app('view.finder');
    $paths = $finder->getPaths();
    echo "   View paths:\n";
    foreach ($paths as $path) {
        echo "   - {$path}\n";
    }

    // Test 5: Try to render view with proper data structure
    echo "\n5. Testing view rendering...\n";
    try {
        // Create a proper dummy pegawai object
        $dummyPegawai = new stdClass();
        $dummyPegawai->id = 1;
        $dummyPegawai->nama_lengkap = 'Test Pegawai';
        $dummyPegawai->gelar_depan = '';
        $dummyPegawai->gelar_belakang = 'S.Kom';
        $dummyPegawai->unit_kerja_terakhir_id = null;

        $dummyData = [
            'pegawai' => $dummyPegawai,
            'pangkats' => collect(),
            'jabatans' => collect(),
            'unitKerjas' => collect(),
            'subUnitKerjas' => collect(),
            'subSubUnitKerjas' => collect(),
            'unitKerjaOptions' => [],
            'subUnitKerjaOptions' => [],
            'subSubUnitKerjaOptions' => [],
            'selectedUnitKerjaId' => null,
            'selectedSubUnitKerjaId' => null,
            'selectedSubSubUnitKerjaId' => null,
        ];

        $rendered = view($viewPath, $dummyData)->render();
        echo "   ✅ View rendered successfully\n";
        echo "   Content length: " . strlen($rendered) . " characters\n";
    } catch (Exception $e) {
        echo "   ❌ View rendering failed: " . $e->getMessage() . "\n";
        echo "   Error type: " . get_class($e) . "\n";

        // Show more details about the error
        if (strpos($e->getMessage(), 'Property') !== false) {
            echo "   This looks like a missing property error. The view expects certain properties.\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
