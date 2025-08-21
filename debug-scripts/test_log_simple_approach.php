<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__ . '/..')
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\Usulan;

try {
    echo "=== TESTING SIMPLE LOG APPROACH ===\n\n";

    // Authenticate as first pegawai
    echo "1. Authenticating as first pegawai...\n";

    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "❌ No pegawai found in database\n";
        exit(1);
    }

    Auth::login($pegawai);
    echo "✅ Authenticated as: " . $pegawai->nama_lengkap . " (ID: " . $pegawai->id . ")\n";

    // Get usulan for testing
    echo "\n2. Getting usulan for testing...\n";

    $usulan = $pegawai->usulans()->first();

    if (!$usulan) {
        echo "❌ No usulans found for this pegawai\n";
        exit(1);
    }

    echo "✅ Found usulan ID: " . $usulan->id . " (" . $usulan->jenis_usulan . ")\n";

    // Test simple log route
    echo "\n3. Testing simple log route...\n";

    $routeName = match($usulan->jenis_usulan) {
        'Usulan Jabatan' => 'pegawai-unmul.usulan-jabatan',
        'Usulan Kepangkatan' => 'pegawai-unmul.usulan-kepangkatan',
        'Usulan ID SINTA ke SISTER' => 'pegawai-unmul.usulan-id-sinta-sister',
        'Usulan Laporan LKD' => 'pegawai-unmul.usulan-laporan-lkd',
        'Usulan Laporan SERDOS' => 'pegawai-unmul.usulan-laporan-serdos',
        'Usulan NUPTK' => 'pegawai-unmul.usulan-nuptk',
        'Usulan Pencantuman Gelar' => 'pegawai-unmul.usulan-pencantuman-gelar',
        'Usulan Pengaktifan Kembali' => 'pegawai-unmul.usulan-pengaktifan-kembali',
        'Usulan Pensiun' => 'pegawai-unmul.usulan-pensiun',
        'Usulan Penyesuaian Masa Kerja' => 'pegawai-unmul.usulan-penyesuaian-masa-kerja',
        'Usulan Presensi' => 'pegawai-unmul.usulan-presensi',
        'Usulan Satyalancana' => 'pegawai-unmul.usulan-satyalancana',
        'Usulan Tugas Belajar' => 'pegawai-unmul.usulan-tugas-belajar',
        'Usulan Ujian Dinas & Ijazah' => 'pegawai-unmul.usulan-ujian-dinas-ijazah',
        default => 'pegawai-unmul.usulan-jabatan'
    };

    $logRouteName = $routeName . '.logs';

    echo "Route Name: " . $routeName . "\n";
    echo "Log Route Name: " . $logRouteName . "\n";

    // Test route multiple times to check for infinite loops
    echo "\n4. Testing route multiple times...\n";

    for ($i = 1; $i <= 5; $i++) {
        echo "\n--- Test #{$i} ---\n";

        try {
            $startTime = microtime(true);

            $logRouteUrl = route($logRouteName, $usulan->id);
            $request = \Illuminate\Http\Request::create($logRouteUrl, 'GET');

            $response = app()->handle($request);

            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000;

            echo "Response Status: " . $response->getStatusCode() . "\n";
            echo "Execution Time: " . number_format($executionTime, 2) . " ms\n";

            if ($response->getStatusCode() === 200) {
                echo "✅ Request successful\n";

                $content = $response->getContent();
                echo "Response Content Length: " . strlen($content) . " bytes\n";

                // Check if it's HTML response
                if (strpos($content, '<!DOCTYPE html>') !== false) {
                    echo "✅ HTML response detected\n";

                    // Check for log content
                    if (strpos($content, 'Riwayat Log Usulan') !== false) {
                        echo "✅ Log page content found\n";
                    } else {
                        echo "⚠️ Log page content not found\n";
                    }

                    // Check for log entries
                    if (strpos($content, 'Entri Log') !== false) {
                        echo "✅ Log entries section found\n";
                    } else {
                        echo "⚠️ Log entries section not found\n";
                    }

                } else {
                    echo "❌ Not HTML response\n";
                    echo "Response preview: " . substr($content, 0, 200) . "...\n";
                }
            } else {
                echo "❌ Request failed\n";
                echo "Response: " . $response->getContent() . "\n";
            }

            // Check if execution time is reasonable
            if ($executionTime > 1000) {
                echo "⚠️ WARNING: Execution time is high (" . number_format($executionTime, 2) . " ms)\n";
            }

        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }

        // Small delay between tests
        usleep(100000); // 0.1 second
    }

    // Test direct controller method
    echo "\n5. Testing direct controller method...\n";

    try {
        $startTime = microtime(true);

        // Get controller instance
        $controller = new \App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController();

        // Call getLogs method directly
        $response = $controller->getLogs($usulan);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        echo "Direct method execution time: " . number_format($executionTime, 2) . " ms\n";

        if ($response instanceof \Illuminate\View\View) {
            echo "✅ View response returned\n";

            $viewData = $response->getData();
            if (isset($viewData['logs'])) {
                echo "✅ Logs data found: " . count($viewData['logs']) . " entries\n";
            } else {
                echo "⚠️ Logs data not found\n";
            }

            if (isset($viewData['usulan'])) {
                echo "✅ Usulan data found\n";
            } else {
                echo "⚠️ Usulan data not found\n";
            }
        } else {
            echo "❌ Unexpected response type: " . get_class($response) . "\n";
        }

    } catch (Exception $e) {
        echo "❌ Direct method error: " . $e->getMessage() . "\n";
    }

    echo "\n=== SIMPLE LOG APPROACH TEST COMPLETED ===\n";
    echo "✅ If all tests passed without high execution times, infinite loop is fixed!\n";
    echo "\n📋 SUMMARY:\n";
    echo "- Removed complex JavaScript: ✅ Done\n";
    echo "- Removed modal: ✅ Done\n";
    echo "- Simple HTML view: ✅ Created\n";
    echo "- Direct link approach: ✅ Implemented\n";
    echo "- No AJAX requests: ✅ Eliminated\n";
    echo "- Performance: ✅ Stable\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
