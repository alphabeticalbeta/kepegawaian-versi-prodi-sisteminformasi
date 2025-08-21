<?php
/**
 * Database Connection Checker
 * Jalankan dengan: php check_db.php
 */

echo "=== Database Connection Checker ===\n\n";

// Konfigurasi database
$config = [
    'host' => '127.0.0.1',
    'port' => 3307,
    'database' => 'db_kepegunmul',
    'username' => 'root',
    'password' => 'root'
];

echo "Configuration:\n";
echo "Host: {$config['host']}\n";
echo "Port: {$config['port']}\n";
echo "Database: {$config['database']}\n";
echo "Username: {$config['username']}\n\n";

// Test 1: Check MySQL server
echo "Test 1: MySQL Server Connection\n";
$connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
if ($connection) {
    echo "✅ MySQL is running on port {$config['port']}\n";
    fclose($connection);
} else {
    echo "❌ MySQL is not running on port {$config['port']}\n";
    echo "Error: $errstr ($errno)\n\n";

    // Try port 3306
    echo "Trying port 3306...\n";
    $connection = @fsockopen($config['host'], 3306, $errno, $errstr, 5);
    if ($connection) {
        echo "✅ MySQL is running on port 3306\n";
        echo "💡 You might need to change DB_PORT to 3306 in .env file\n";
        fclose($connection);
    } else {
        echo "❌ MySQL is not running on port 3306 either\n";
    }
}

echo "\nTest 2: Database Connection\n";
try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connection successful!\n";

    // Check tables
    $stmt = $pdo->query("SHOW TABLES LIKE 'pegawais'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'pegawais' exists\n";

        // Check user
        $stmt = $pdo->prepare("SELECT * FROM pegawais WHERE nip = ? LIMIT 1");
        $stmt->execute(['199405242024061001']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "✅ User with NIP 199405242024061001 found\n";
            echo "User data: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "❌ User with NIP 199405242024061001 not found\n";
            echo "💡 This might be the login issue - user doesn't exist\n";

            // Show some users
            $stmt = $pdo->query("SELECT nip, nama FROM pegawais LIMIT 5");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($users) {
                echo "Available users:\n";
                foreach ($users as $user) {
                    echo "- NIP: {$user['nip']} - Nama: {$user['nama']}\n";
                }
            }
        }
    } else {
        echo "❌ Table 'pegawais' does not exist\n";
        echo "💡 You need to run migrations\n";

        // Show available tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_NUM);
        if ($tables) {
            echo "Available tables:\n";
            foreach ($tables as $table) {
                echo "- {$table[0]}\n";
            }
        }
    }

} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";

    // Try without database
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✅ MySQL server connection successful (without database)\n";

        // Show databases
        $stmt = $pdo->query("SHOW DATABASES");
        echo "Available databases:\n";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $highlight = ($row[0] === $config['database']) ? " (Target)" : "";
            echo "- {$row[0]}$highlight\n";
        }

        if (!in_array($config['database'], $stmt->fetchAll(PDO::FETCH_COLUMN))) {
            echo "\n❌ Database '{$config['database']}' does not exist\n";
            echo "💡 You need to create the database first\n";
        }

    } catch (PDOException $e2) {
        echo "❌ MySQL server connection failed: " . $e2->getMessage() . "\n";
    }
}

echo "\n=== Solutions ===\n";
echo "1. If MySQL is not running: Start MySQL service\n";
echo "2. If wrong port: Change DB_PORT in .env file\n";
echo "3. If database doesn't exist: Create database or run migrations\n";
echo "4. If table doesn't exist: Run 'php artisan migrate'\n";
echo "5. If user doesn't exist: Add user to database or use correct NIP\n";

echo "\n=== Quick Commands ===\n";
echo "php artisan migrate\n";
echo "php artisan db:seed\n";
echo "php artisan config:clear\n";
?>
