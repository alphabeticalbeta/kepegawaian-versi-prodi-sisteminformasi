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
    echo "=== TESTING LOG FUNCTIONALITY ===\n\n";

    // Authenticate as first pegawai
    echo "1. Authenticating as first pegawai...\n";

    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "❌ No pegawai found in database\n";
        exit(1);
    }

    Auth::login($pegawai);
    echo "✅ Authenticated as: " . $pegawai->nama_lengkap . " (ID: " . $pegawai->id . ")\n";

    // Get all usulans for this pegawai
    echo "\n2. Getting all usulans...\n";

    $usulans = $pegawai->usulans()->with(['periodeUsulan'])->get();

    if ($usulans->isEmpty()) {
        echo "❌ No usulans found for this pegawai\n";
        exit(1);
    }

    echo "✅ Found " . $usulans->count() . " usulans\n";

    // Test log routes for each usulan
    echo "\n3. Testing log routes for each usulan...\n";

    foreach ($usulans as $usulan) {
        echo "\n--- Testing Log Route for Usulan ID: " . $usulan->id . " ---\n";
        echo "Jenis Usulan: " . $usulan->jenis_usulan . "\n";
        echo "Status: " . $usulan->status_usulan . "\n";

        // Determine route based on jenis usulan
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

        // Test the log route
        try {
            $logRouteUrl = route($logRouteName, $usulan->id);
            echo "Log Route URL: " . $logRouteUrl . "\n";

            // Test if route is accessible
            $request = \Illuminate\Http\Request::create($logRouteUrl, 'GET');
            $response = app()->handle($request);

            echo "Response Status: " . $response->getStatusCode() . "\n";

            if ($response->getStatusCode() === 200) {
                echo "✅ Log route accessible\n";

                // Try to get response content
                $content = $response->getContent();
                $data = json_decode($content, true);

                if ($data && isset($data['success'])) {
                    echo "✅ Log response format correct\n";
                    if (isset($data['logs'])) {
                        echo "✅ Found " . count($data['logs']) . " log entries\n";
                    } else {
                        echo "⚠️ No logs array in response\n";
                    }
                } else {
                    echo "⚠️ Log response format unexpected\n";
                }
            } else {
                echo "❌ Log route not accessible\n";
            }

        } catch (Exception $e) {
            echo "❌ Log route error: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== LOG FUNCTIONALITY TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
