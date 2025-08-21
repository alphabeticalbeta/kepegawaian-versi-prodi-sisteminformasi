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

echo "=== SIMPLE VIEW TEST ===\n\n";

try {
    // Test 1: Check if view exists
    echo "1. Testing view existence...\n";
    $viewPath = 'backend.layouts.views.admin-univ-usulan.data-pegawai.form-datapegawai';

    if (view()->exists($viewPath)) {
        echo "   ✅ View exists: {$viewPath}\n";
    } else {
        echo "   ❌ View not found: {$viewPath}\n";
        exit(1);
    }

    // Test 2: Try to render with minimal data
    echo "\n2. Testing view rendering with minimal data...\n";
    try {
        // Create a minimal pegawai object with all required properties
        $dummyPegawai = new stdClass();
        $dummyPegawai->id = 1;
        $dummyPegawai->nama_lengkap = 'Test Pegawai';
        $dummyPegawai->gelar_depan = '';
        $dummyPegawai->gelar_belakang = 'S.Kom';
        $dummyPegawai->unit_kerja_terakhir_id = null;
        $dummyPegawai->unit_kerja_id = null;
        $dummyPegawai->sub_unit_kerja_id = null;
        $dummyPegawai->sub_sub_unit_kerja_id = null;
        $dummyPegawai->pangkat_terakhir_id = null;
        $dummyPegawai->jabatan_terakhir_id = null;
        $dummyPegawai->jenis_pegawai = null;
        $dummyPegawai->status_kepegawaian = null;
        $dummyPegawai->jenis_jabatan = null;
        $dummyPegawai->mata_kuliah_diampu = null;
        $dummyPegawai->ranting_ilmu_kepakaran = null;
        $dummyPegawai->url_profil_sinta = null;

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

        echo "   Attempting to render view...\n";
        $rendered = view($viewPath, $dummyData)->render();
        echo "   ✅ View rendered successfully!\n";
        echo "   Content length: " . strlen($rendered) . " characters\n";

        // Check if content contains expected elements
        if (strpos($rendered, 'form-datapegawai') !== false) {
            echo "   ✅ Content contains expected elements\n";
        } else {
            echo "   ⚠️  Content might be incomplete\n";
        }

    } catch (Exception $e) {
        echo "   ❌ View rendering failed: " . $e->getMessage() . "\n";
        echo "   Error type: " . get_class($e) . "\n";
        echo "   File: " . $e->getFile() . "\n";
        echo "   Line: " . $e->getLine() . "\n";

        // Show the first few lines of the stack trace
        $trace = $e->getTraceAsString();
        $lines = explode("\n", $trace);
        echo "   Stack trace (first 5 lines):\n";
        for ($i = 0; $i < min(5, count($lines)); $i++) {
            echo "   " . $lines[$i] . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
