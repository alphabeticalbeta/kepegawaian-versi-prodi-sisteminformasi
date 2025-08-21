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
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

$id = (int)($argv[1] ?? 0);
if (!$id) {
    echo "Usage: php debug-scripts/check_usulan_owner.php <usulan_id>\n";
    exit(1);
}

$usulan = Usulan::with('pegawai')->find($id);
if (!$usulan) {
    echo "Usulan ID $id not found\n";
    exit(1);
}

$current = Pegawai::first();
if ($current) {
    Auth::login($current);
}

echo "Usulan ID: {$usulan->id}\n";
echo "Owner ID: {$usulan->pegawai_id}\n";
$ownerName = $usulan->pegawai->nama_lengkap ?? '-';
$ownerNip = $usulan->pegawai->nip ?? '-';

echo "Owner Name: {$ownerName}\n";

echo "Currently Authenticated (for test): ".(Auth::id() ?: 'none')."\n";
