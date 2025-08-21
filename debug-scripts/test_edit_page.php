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
    echo "=== TESTING EDIT PAGE ===\n\n";

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

    // Test edit page access
    echo "\n3. Testing edit page access...\n";

    $editRequest = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/' . $existingUsulan->id . '/edit', 'GET');
    $editResponse = app()->handle($editRequest);

    echo "Edit page status: " . $editResponse->getStatusCode() . "\n";

    if ($editResponse->getStatusCode() === 200) {
        echo "✅ Edit page accessible\n";

        // Check if form is present in response
        $content = $editResponse->getContent();
        if (strpos($content, 'usulan-form') !== false) {
            echo "✅ Form found in response\n";
        } else {
            echo "⚠️ Form not found in response\n";
        }

        if (strpos($content, 'Edit Usulan Jabatan') !== false) {
            echo "✅ Edit mode detected\n";
        } else {
            echo "⚠️ Edit mode not detected\n";
        }

    } else {
        echo "❌ Edit page not accessible\n";
        echo "Response content: " . $editResponse->getContent() . "\n";
    }

    echo "\n=== TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
