<?php
/**
 * Fix Login Issue Without Changing .env Configuration
 * Script ini tidak mengubah konfigurasi database yang sudah bekerja
 */

echo "=== Fix Login Issue (Keep Current .env) ===\n\n";

// Check if we're in Laravel project
if (!file_exists('artisan')) {
    echo "❌ This script must be run from Laravel project root directory\n";
    exit(1);
}

echo "✅ Laravel project detected\n";
echo "🔒 Keeping current .env configuration unchanged\n\n";

// Commands to run (only cache clearing, no config changes)
$commands = [
    'config:clear' => 'Clear configuration cache',
    'cache:clear' => 'Clear application cache',
    'route:clear' => 'Clear route cache',
    'view:clear' => 'Clear view cache',
    'session:table' => 'Clear session cache',
    'queue:clear' => 'Clear queue cache'
];

echo "Running Laravel cache clearing commands...\n";
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
        echo "⚠️  Warning: " . implode("\n", $output) . "\n";
    }
}

echo "\n================================\n";
echo "✅ Cache clearing completed!\n\n";

// Test current configuration
echo "Testing current database configuration...\n";
echo "================================\n";

try {
    // Load Laravel environment
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    // Get current database configuration
    $config = config('database.connections.mysql');
    echo "Current database configuration:\n";
    echo "- Host: " . $config['host'] . "\n";
    echo "- Port: " . $config['port'] . "\n";
    echo "- Database: " . $config['database'] . "\n";
    echo "- Username: " . $config['username'] . "\n";
    echo "- Connection: " . config('database.default') . "\n\n";

    // Test database connection
    $connection = DB::connection();
    $pdo = $connection->getPdo();

    echo "✅ Database connection successful!\n";

    // Test the specific query that was failing
    $user = DB::table('pegawais')->where('nip', '199405242024061001')->first();

    if ($user) {
        echo "✅ User found: {$user->nama_lengkap} (NIP: {$user->nip})\n";
        echo "✅ Database query working correctly!\n";
    } else {
        echo "❌ User not found - check if NIP is correct\n";
    }

    // Test authentication system
    echo "\nTesting authentication system...\n";
    try {
        // Check if auth configuration is correct
        $authConfig = config('auth');
        echo "✅ Auth configuration loaded\n";
        echo "- Default guard: " . $authConfig['defaults']['guard'] . "\n";
        echo "- User provider: " . $authConfig['guards']['web']['provider'] . "\n";

    } catch (Exception $e) {
        echo "⚠️  Auth configuration issue: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "💡 This might indicate a configuration issue\n";
}

echo "\n================================\n";
echo "🎯 Next Steps (Without Changing .env):\n";
echo "1. Restart web server (Apache/Nginx)\n";
echo "2. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "3. Try logging in again\n";
echo "4. If still fails, check browser console (F12)\n";

echo "\n=== Alternative Solutions ===\n";
echo "If login still fails:\n";
echo "1. Check if user has correct password\n";
echo "2. Verify user is active in database\n";
echo "3. Check Laravel logs: storage/logs/laravel.log\n";
echo "4. Restart MySQL service\n";
echo "5. Check if there are any middleware issues\n";

echo "\n=== Manual Commands (if needed) ===\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";

echo "\n=== Check Laravel Logs ===\n";
echo "tail -f storage/logs/laravel.log\n";
?>
