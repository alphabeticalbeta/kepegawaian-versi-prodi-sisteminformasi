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
    echo "=== TESTING DASHBOARD ACTIONS ===\n\n";

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

    // Test each usulan's action route
    echo "\n3. Testing action routes for each usulan...\n";

    foreach ($usulans as $usulan) {
        echo "\n--- Testing Usulan ID: " . $usulan->id . " ---\n";
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

        // Determine if can edit or just view
        $canEdit = in_array($usulan->status_usulan, ['Draft', 'Perlu Perbaikan', 'Dikembalikan ke Pegawai']);
        $actionRoute = $canEdit ? $routeName . '.edit' : $routeName . '.show';

        echo "Route Name: " . $routeName . "\n";
        echo "Action Route: " . $actionRoute . "\n";
        echo "Can Edit: " . ($canEdit ? 'Yes' : 'No') . "\n";

        // Test the route
        try {
            $routeUrl = route($actionRoute, $usulan->id);
            echo "Route URL: " . $routeUrl . "\n";

            // Test if route is accessible
            $request = \Illuminate\Http\Request::create($routeUrl, 'GET');
            $response = app()->handle($request);

            echo "Response Status: " . $response->getStatusCode() . "\n";

            if ($response->getStatusCode() === 200) {
                echo "✅ Route accessible\n";
            } else {
                echo "❌ Route not accessible\n";
            }

        } catch (Exception $e) {
            echo "❌ Route error: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
