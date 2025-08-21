<?php
/**
 * Script untuk memperbaiki masalah koneksi database
 * Error: SQLSTATE[HY000] [2002] Connection refused
 */

echo "=== Database Connection Fix Script ===\n\n";

// Konfigurasi database dari .env
$config = [
    'host' => '127.0.0.1',
    'port' => 3307,
    'database' => 'db_kepegunmul',
    'username' => 'root',
    'password' => 'root'
];

echo "Current configuration:\n";
echo "Host: {$config['host']}\n";
echo "Port: {$config['port']}\n";
echo "Database: {$config['database']}\n";
echo "Username: {$config['username']}\n\n";

// Test 1: Cek apakah MySQL berjalan di port 3307
echo "Test 1: Checking MySQL service on port {$config['port']}...\n";
$connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
if ($connection) {
    echo "✅ MySQL is running on port {$config['port']}\n";
    fclose($connection);
} else {
    echo "❌ MySQL is not running on port {$config['port']}\n";
    echo "Error: $errstr ($errno)\n\n";

    // Test port 3306 (default MySQL port)
    echo "Testing default MySQL port 3306...\n";
    $connection = @fsockopen($config['host'], 3306, $errno, $errstr, 5);
    if ($connection) {
        echo "✅ MySQL is running on port 3306\n";
        echo "💡 Suggestion: Change DB_PORT in .env to 3306\n";
        fclose($connection);
    } else {
        echo "❌ MySQL is not running on port 3306 either\n";
    }
}

echo "\nTest 2: Testing database connection...\n";

// Test koneksi database
try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connection successful!\n";

    // Test tabel pegawais
    $stmt = $pdo->query("SHOW TABLES LIKE 'pegawais'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'pegawais' exists\n";

        // Test query yang error
        $stmt = $pdo->prepare("SELECT * FROM pegawais WHERE nip = ? LIMIT 1");
        $stmt->execute(['199405242024061001']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "✅ User with NIP 199405242024061001 found\n";
        } else {
            echo "❌ User with NIP 199405242024061001 not found\n";
            echo "💡 This might be the actual issue - user doesn't exist in database\n";
        }
    } else {
        echo "❌ Table 'pegawais' does not exist\n";
        echo "💡 You need to run database migrations\n";
    }

} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";

    // Coba koneksi tanpa database
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✅ MySQL server connection successful (without database)\n";

        // Cek database yang tersedia
        $stmt = $pdo->query("SHOW DATABASES");
        echo "Available databases:\n";
        $databases = [];
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $databases[] = $row[0];
            echo "- " . $row[0] . "\n";
        }

        if (!in_array($config['database'], $databases)) {
            echo "\n❌ Database '{$config['database']}' does not exist\n";
            echo "💡 You need to create the database first\n";
        }

    } catch (PDOException $e2) {
        echo "❌ MySQL server connection failed: " . $e2->getMessage() . "\n";
    }
}

echo "\n=== Solutions ===\n";
echo "1. If MySQL is not running:\n";
echo "   - Start MySQL service\n";
echo "   - If using Laragon: Open Laragon and start MySQL\n";
echo "   - If using XAMPP: Start MySQL in XAMPP Control Panel\n\n";

echo "2. If wrong port:\n";
echo "   - Check if MySQL is running on port 3306 instead of 3307\n";
echo "   - Update DB_PORT in .env file\n\n";

echo "3. If database doesn't exist:\n";
echo "   - Create database 'db_kepegunmul'\n";
echo "   - Run migrations: php artisan migrate\n\n";

echo "4. If table doesn't exist:\n";
echo "   - Run migrations: php artisan migrate\n";
echo "   - Run seeders: php artisan db:seed\n\n";

echo "5. If user doesn't exist:\n";
echo "   - Check if user with NIP 199405242024061001 exists in database\n";
echo "   - Add user to database or use correct NIP\n\n";

echo "=== Quick Fix Commands ===\n";
echo "# If using Laragon:\n";
echo "1. Open Laragon\n";
echo "2. Click 'Start All'\n";
echo "3. Open Terminal in Laragon\n";
echo "4. Run: cd /d/kepegawaian-unmul-v2\n";
echo "5. Run: php artisan migrate\n";
echo "6. Run: php artisan db:seed\n\n";

echo "# If using XAMPP:\n";
echo "1. Open XAMPP Control Panel\n";
echo "2. Start MySQL and Apache\n";
echo "3. Open Command Prompt\n";
echo "4. Run: cd /d/kepegawaian-unmul-v2\n";
echo "5. Run: php artisan migrate\n";
echo "6. Run: php artisan db:seed\n";
?>
