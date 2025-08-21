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
    echo "=== DEBUGGING LOG RESPONSE STRUCTURE ===\n\n";

    // Authenticate as first pegawai
    $pegawai = Pegawai::first();
    Auth::login($pegawai);

    // Get usulan for testing
    $usulan = $pegawai->usulans()->first();

    // Test route
    $logRouteUrl = route('pegawai-unmul.usulan-jabatan.logs', $usulan->id);

    $request = \Illuminate\Http\Request::create($logRouteUrl, 'GET');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    $request->headers->set('Accept', 'application/json');

    $response = app()->handle($request);

    echo "Response Status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() === 200) {
        $content = $response->getContent();
        $data = json_decode($content, true);

        echo "\n=== FULL RESPONSE STRUCTURE ===\n";
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        echo "\n\n=== LOGS ARRAY STRUCTURE ===\n";
        if (isset($data['logs']) && is_array($data['logs'])) {
            foreach ($data['logs'] as $index => $log) {
                echo "\n--- Log Entry #" . ($index + 1) . " ---\n";
                echo "Keys: " . implode(', ', array_keys($log)) . "\n";
                foreach ($log as $key => $value) {
                    echo "  {$key}: " . (is_string($value) ? $value : json_encode($value)) . "\n";
                }
            }
        } else {
            echo "No logs array found or not an array\n";
        }
    } else {
        echo "Response failed: " . $response->getContent() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
