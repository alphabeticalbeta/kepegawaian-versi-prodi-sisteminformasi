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
    echo "=== TESTING LOG INFINITE LOOP FIX ===\n\n";

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

    // Test multiple log requests to check for infinite loop
    echo "\n3. Testing multiple log requests...\n";

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

    // Test multiple requests
    for ($i = 1; $i <= 5; $i++) {
        echo "\n--- Test Request #{$i} ---\n";

        try {
            $startTime = microtime(true);

            $logRouteUrl = route($logRouteName, $usulan->id);
            $request = \Illuminate\Http\Request::create($logRouteUrl, 'GET');
            $response = app()->handle($request);

            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

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

            // Check if execution time is reasonable (should be under 1 second)
            if ($executionTime > 1000) {
                echo "⚠️ WARNING: Execution time is high (" . number_format($executionTime, 2) . " ms)\n";
            }

        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }

        // Small delay between requests
        usleep(100000); // 0.1 second
    }

    echo "\n=== INFINITE LOOP TEST COMPLETED ===\n";
    echo "✅ If all requests completed successfully without high execution times, infinite loop is fixed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
