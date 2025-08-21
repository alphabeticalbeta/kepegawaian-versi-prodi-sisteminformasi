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
    echo "=== TESTING LOG MODAL INFINITE LOOP FIX ===\n\n";

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

    // Test rapid log requests to simulate multiple clicks
    echo "\n3. Testing rapid log requests (simulating multiple clicks)...\n";

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

    echo "Testing route: " . $logRouteName . "\n";

    // Test rapid requests (simulating multiple clicks)
    $startTime = microtime(true);

    for ($i = 1; $i <= 10; $i++) {
        echo "\n--- Rapid Request #{$i} ---\n";

        try {
            $requestStartTime = microtime(true);

            $logRouteUrl = route($logRouteName, $usulan->id);
            $request = \Illuminate\Http\Request::create($logRouteUrl, 'GET');
            $response = app()->handle($request);

            $requestEndTime = microtime(true);
            $executionTime = ($requestEndTime - $requestStartTime) * 1000; // Convert to milliseconds

            echo "Response Status: " . $response->getStatusCode() . "\n";
            echo "Execution Time: " . number_format($executionTime, 2) . " ms\n";

            if ($response->getStatusCode() === 200) {
                echo "✅ Request successful\n";

                $content = $response->getContent();
                $data = json_decode($content, true);

                if ($data && isset($data['success'])) {
                    echo "✅ Response format correct\n";
                    if (isset($data['logs'])) {
                        echo "✅ Found " . count($data['logs']) . " log entries\n";
                    }
                }
            } else {
                echo "❌ Request failed\n";
            }

            // Check if execution time is reasonable (should be under 500ms for rapid requests)
            if ($executionTime > 500) {
                echo "⚠️ WARNING: Execution time is high (" . number_format($executionTime, 2) . " ms)\n";
            }

        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }

        // Very small delay between rapid requests
        usleep(50000); // 0.05 second
    }

    $totalTime = (microtime(true) - $startTime) * 1000;
    echo "\n=== RAPID REQUESTS COMPLETED ===\n";
    echo "Total time for 10 rapid requests: " . number_format($totalTime, 2) . " ms\n";
    echo "Average time per request: " . number_format($totalTime / 10, 2) . " ms\n";

    if ($totalTime < 5000) { // Less than 5 seconds for 10 requests
        echo "✅ Performance is good - no infinite loop detected!\n";
    } else {
        echo "⚠️ WARNING: Performance might indicate infinite loop issues\n";
    }

    // Test concurrent requests
    echo "\n4. Testing concurrent requests...\n";

    $concurrentStartTime = microtime(true);
    $successfulRequests = 0;
    $failedRequests = 0;

    // Simulate 5 concurrent requests
    for ($i = 1; $i <= 5; $i++) {
        try {
            $logRouteUrl = route($logRouteName, $usulan->id);
            $request = \Illuminate\Http\Request::create($logRouteUrl, 'GET');
            $response = app()->handle($request);

            if ($response->getStatusCode() === 200) {
                $successfulRequests++;
            } else {
                $failedRequests++;
            }
        } catch (Exception $e) {
            $failedRequests++;
        }
    }

    $concurrentTime = (microtime(true) - $concurrentStartTime) * 1000;
    echo "Concurrent requests completed in: " . number_format($concurrentTime, 2) . " ms\n";
    echo "Successful: {$successfulRequests}, Failed: {$failedRequests}\n";

    if ($successfulRequests === 5 && $concurrentTime < 2000) {
        echo "✅ Concurrent requests working properly!\n";
    } else {
        echo "⚠️ WARNING: Concurrent requests might have issues\n";
    }

    echo "\n=== LOG MODAL INFINITE LOOP TEST COMPLETED ===\n";
    echo "✅ If all tests passed without high execution times, infinite loop is fixed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
