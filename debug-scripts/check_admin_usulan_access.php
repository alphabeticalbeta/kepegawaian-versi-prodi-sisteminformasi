<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check current user
$user = \Illuminate\Support\Facades\Auth::guard('pegawai')->user();

if (!$user) {
    echo "❌ User tidak login\n";
    exit;
}

echo "✅ User logged in: " . $user->nama_lengkap . "\n";
echo "📧 Email: " . $user->email . "\n";

// Check roles using Spatie Permission
if (method_exists($user, 'getRoleNames')) {
    $roles = $user->getRoleNames();
    echo "🎭 Roles: " . $roles->implode(', ') . "\n";

    // Check if user has Admin Universitas Usulan role
    if ($roles->contains('Admin Universitas Usulan')) {
        echo "✅ User memiliki role 'Admin Universitas Usulan'\n";
    } else {
        echo "❌ User TIDAK memiliki role 'Admin Universitas Usulan'\n";
        echo "Available roles: " . $roles->implode(', ') . "\n";
    }

    // Check permissions
    if (method_exists($user, 'getAllPermissions')) {
        $permissions = $user->getAllPermissions()->pluck('name');
        echo "🔐 Permissions: " . $permissions->implode(', ') . "\n";
    }
} else {
    echo "❌ User model tidak memiliki method getRoleNames()\n";
}

// Test route access
try {
    $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('backend.admin-univ-usulan.unitkerja.index');
    if ($route) {
        echo "✅ Route 'backend.admin-univ-usulan.unitkerja.index' exists\n";
        echo "📍 Route URI: " . $route->uri() . "\n";
        echo "🔧 Route Method: " . implode('|', $route->methods()) . "\n";
    } else {
        echo "❌ Route 'backend.admin-univ-usulan.unitkerja.index' not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking route: " . $e->getMessage() . "\n";
}

// Check middleware
try {
    if (isset($route)) {
        $middleware = $route->middleware();
        echo "🛡️ Route Middleware: " . implode(', ', $middleware) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking middleware: " . $e->getMessage() . "\n";
}

echo "\n🎯 Testing complete!\n";
