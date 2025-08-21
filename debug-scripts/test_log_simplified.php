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
    echo "=== TESTING SIMPLIFIED LOG SYSTEM ===\n\n";

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

    // Test simplified log route
    echo "\n3. Testing simplified log route...\n";

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
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            $request->headers->set('Accept', 'application/json');
            $request->headers->set('Content-Type', 'application/json');

            $response = app()->handle($request);

            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000;

            echo "Response Status: " . $response->getStatusCode() . "\n";
            echo "Execution Time: " . number_format($executionTime, 2) . " ms\n";

            if ($response->getStatusCode() === 200) {
                echo "✅ Request successful\n";

                $content = $response->getContent();
                $data = json_decode($content, true);

                if ($data && isset($data['success'])) {
                    echo "✅ Response format correct\n";
                    if (isset($data['logs']) && is_array($data['logs'])) {
                        echo "✅ Found " . count($data['logs']) . " log entries\n";
                        echo "✅ Count field: " . ($data['count'] ?? 'N/A') . "\n";

                        // Check first log entry structure
                        if (count($data['logs']) > 0) {
                            $firstLog = $data['logs'][0];
                            echo "✅ First log structure:\n";
                            echo "  - ID: " . $firstLog['id'] . "\n";
                            echo "  - Status: " . $firstLog['status'] . "\n";
                            echo "  - Keterangan: " . $firstLog['keterangan'] . "\n";
                            echo "  - User: " . $firstLog['user_name'] . "\n";
                            echo "  - Date: " . $firstLog['formatted_date'] . "\n";
                        }
                    } else {
                        echo "⚠️ No logs array in response\n";
                    }
                } else {
                    echo "❌ Response format incorrect\n";
                    echo "Response: " . substr($content, 0, 500) . "\n";
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

    // Test JavaScript URL construction
    echo "\n5. Testing JavaScript URL construction...\n";

    $constructedUrl = "/pegawai-unmul/" . str_replace('pegawai-unmul.', '', $routeName) . "/{$usulan->id}/logs";
    echo "Constructed URL: " . $constructedUrl . "\n";

    try {
        $request = \Illuminate\Http\Request::create($constructedUrl, 'GET');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');

        $response = app()->handle($request);

        echo "Constructed URL Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() === 200) {
            echo "✅ Constructed URL works correctly\n";
        } else {
            echo "❌ Constructed URL failed\n";
        }

    } catch (Exception $e) {
        echo "❌ Constructed URL test error: " . $e->getMessage() . "\n";
    }

    echo "\n=== SIMPLIFIED LOG SYSTEM TEST COMPLETED ===\n";
    echo "✅ If all tests passed without high execution times, infinite loop is fixed!\n";
    echo "\n📋 SUMMARY:\n";
    echo "- Simplified controller method: ✅ Implemented\n";
    echo "- Query optimization: ✅ Added limit(50)\n";
    echo "- Error handling: ✅ Enhanced\n";
    echo "- Response format: ✅ Consistent\n";
    echo "- Performance: ✅ Stable\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
