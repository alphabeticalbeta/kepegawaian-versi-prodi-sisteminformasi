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
    echo "=== TESTING SHOW PAGE ===\n\n";

    // Authenticate as first pegawai
    echo "1. Authenticating as first pegawai...\n";

    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "❌ No pegawai found in database\n";
        exit(1);
    }

    Auth::login($pegawai);
    echo "✅ Authenticated as: " . $pegawai->nama_lengkap . " (ID: " . $pegawai->id . ")\n";

    // Find existing usulan
    echo "\n2. Finding existing usulan...\n";

    $existingUsulan = Usulan::where('pegawai_id', $pegawai->id)
        ->where('jenis_usulan', 'Usulan Jabatan')
        ->first();

    if (!$existingUsulan) {
        echo "❌ No existing usulan found\n";
        exit(1);
    }

    echo "✅ Found usulan with ID: " . $existingUsulan->id . "\n";
    echo "Status: " . $existingUsulan->status_usulan . "\n";

    // Test show page access
    echo "\n3. Testing show page access...\n";

    $showRequest = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/' . $existingUsulan->id, 'GET');
    $showResponse = app()->handle($showRequest);

    echo "Show page status: " . $showResponse->getStatusCode() . "\n";

    if ($showResponse->getStatusCode() === 200) {
        echo "✅ Show page accessible\n";

        // Check if content is present in response
        $content = $showResponse->getContent();
        if (strpos($content, 'Detail Usulan Jabatan') !== false) {
            echo "✅ Detail page title found\n";
        } else {
            echo "⚠️ Detail page title not found\n";
        }

        if (strpos($content, 'Kembali ke Daftar') !== false) {
            echo "✅ Back button found\n";
        } else {
            echo "⚠️ Back button not found\n";
        }

        if (strpos($content, 'Status:') !== false) {
            echo "✅ Status badge found\n";
        } else {
            echo "⚠️ Status badge not found\n";
        }

        // Check if form elements are NOT present (should be read-only)
        if (strpos($content, 'type="submit"') === false) {
            echo "✅ No submit buttons found (read-only mode)\n";
        } else {
            echo "⚠️ Submit buttons found (should be read-only)\n";
        }

        if (strpos($content, 'cursor-not-allowed') !== false) {
            echo "✅ Disabled inputs found (read-only mode)\n";
        } else {
            echo "⚠️ Disabled inputs not found\n";
        }

    } else {
        echo "❌ Show page not accessible\n";
        echo "Response content: " . $showResponse->getContent() . "\n";
    }

    echo "\n=== TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
