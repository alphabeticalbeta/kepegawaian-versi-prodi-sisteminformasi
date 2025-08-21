<?php
/**
 * Laravel Cache & Configuration Fix
 * Jalankan dengan: php fix_laravel_cache.php
 */

echo "=== Laravel Cache & Configuration Fix ===\n\n";

// Check if we're in Laravel project
if (!file_exists('artisan')) {
    echo "❌ This script must be run from Laravel project root directory\n";
    exit(1);
}

echo "✅ Laravel project detected\n\n";

// Commands to run
$commands = [
    'config:clear' => 'Clear configuration cache',
    'cache:clear' => 'Clear application cache',
    'route:clear' => 'Clear route cache',
    'view:clear' => 'Clear view cache',
    'config:cache' => 'Cache configuration files',
    'route:cache' => 'Cache routes',
    'view:cache' => 'Cache views'
];

echo "Running Laravel commands...\n";
echo "================================\n";

foreach ($commands as $command => $description) {
    echo "\n🔄 $description...\n";
    echo "Command: php artisan $command\n";

    // Execute command
    $output = [];
    $returnCode = 0;
    exec("php artisan $command 2>&1", $output, $returnCode);

    if ($returnCode === 0) {
        echo "✅ Success: " . implode("\n", $output) . "\n";
    } else {
        echo "❌ Error: " . implode("\n", $output) . "\n";
    }
}

echo "\n================================\n";
echo "✅ Cache clearing completed!\n\n";

// Test database connection
echo "Testing database connection after cache clear...\n";
echo "================================\n";

try {
    // Load Laravel environment
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    // Test database connection
    $connection = DB::connection();
    $pdo = $connection->getPdo();

    echo "✅ Database connection successful!\n";

    // Test the specific query that was failing
    $user = DB::table('pegawais')->where('nip', '199405242024061001')->first();

    if ($user) {
        echo "✅ User found: {$user->nama_lengkap} (NIP: {$user->nip})\n";
        echo "✅ Login should work now!\n";
    } else {
        echo "❌ User not found - check database\n";
    }

} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n================================\n";
echo "🎯 Next Steps:\n";
echo "1. Try logging in again\n";
echo "2. If still fails, check browser console for errors\n";
echo "3. Check Laravel logs at storage/logs/laravel.log\n";
echo "4. Restart web server if needed\n";

echo "\n=== Manual Commands (if script fails) ===\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
echo "php artisan config:cache\n";
?>
