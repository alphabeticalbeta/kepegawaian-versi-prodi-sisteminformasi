<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: __DIR__ . '/..')
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {})
    ->withExceptions(function (Exceptions $exceptions) {})
    ->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Pegawai;

$usulanId = (int)($argv[1] ?? 0);
if (!$usulanId) {
    echo "Usage: php debug-scripts/hit_show_route.php <usulan_id>\n";
    exit(1);
}

$pegawai = Pegawai::first();
if (!$pegawai) {
    echo "No pegawai found\n";
    exit(1);
}
Auth::login($pegawai);

$request = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/' . $usulanId, 'GET');
$response = app()->handle($request);

$code = $response->getStatusCode();
echo "Status: $code\n";
$loc = $response->headers->get('Location');
if ($loc) {
    echo "Location: $loc\n";
}
$content = $response->getContent();
echo "Content preview: ".substr(strip_tags($content),0,200)."...\n";
