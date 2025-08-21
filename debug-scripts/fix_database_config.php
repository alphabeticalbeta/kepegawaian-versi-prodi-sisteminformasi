<?php
/**
 * Script untuk memperbaiki konfigurasi database di file .env
 */

echo "=== Database Configuration Fix ===\n\n";

$envFile = '.env';
$backupFile = '.env.backup.' . date('Y-m-d-H-i-s');

// Backup file .env
if (file_exists($envFile)) {
    copy($envFile, $backupFile);
    echo "✅ Backup created: $backupFile\n";
}

// Baca file .env
$envContent = file_get_contents($envFile);
if (!$envContent) {
    echo "❌ Cannot read .env file\n";
    exit(1);
}

echo "Current .env content:\n";
echo "DB_CONNECTION: " . (preg_match('/DB_CONNECTION=(\w+)/', $envContent, $matches) ? $matches[1] : 'not found') . "\n";
echo "DB_HOST: " . (preg_match('/DB_HOST=([^\s]+)/', $envContent, $matches) ? $matches[1] : 'not found') . "\n";
echo "DB_PORT: " . (preg_match('/DB_PORT=(\d+)/', $envContent, $matches) ? $matches[1] : 'not found') . "\n";
echo "DB_DATABASE: " . (preg_match('/DB_DATABASE=([^\s]+)/', $envContent, $matches) ? $matches[1] : 'not found') . "\n";
echo "DB_USERNAME: " . (preg_match('/DB_USERNAME=([^\s]+)/', $envContent, $matches) ? $matches[1] : 'not found') . "\n\n";

// Test koneksi dengan konfigurasi saat ini
$currentPort = preg_match('/DB_PORT=(\d+)/', $envContent, $matches) ? $matches[1] : '3306';
$currentHost = preg_match('/DB_HOST=([^\s]+)/', $envContent, $matches) ? $matches[1] : '127.0.0.1';

echo "Testing current configuration...\n";
$connection = @fsockopen($currentHost, $currentPort, $errno, $errstr, 5);
if ($connection) {
    echo "✅ MySQL is running on current configuration\n";
    fclose($connection);
} else {
    echo "❌ MySQL is not running on current configuration\n";

    // Test port 3306
    echo "Testing port 3306...\n";
    $connection = @fsockopen($currentHost, 3306, $errno, $errstr, 5);
    if ($connection) {
        echo "✅ MySQL is running on port 3306\n";
        fclose($connection);

        // Update port to 3306
        $envContent = preg_replace('/DB_PORT=\d+/', 'DB_PORT=3306', $envContent);
        echo "💡 Updated DB_PORT to 3306\n";
    } else {
        echo "❌ MySQL is not running on port 3306 either\n";

        // Test port 3307
        echo "Testing port 3307...\n";
        $connection = @fsockopen($currentHost, 3307, $errno, $errstr, 5);
        if ($connection) {
            echo "✅ MySQL is running on port 3307\n";
            fclose($connection);

            // Update port to 3307
            $envContent = preg_replace('/DB_PORT=\d+/', 'DB_PORT=3307', $envContent);
            echo "💡 Updated DB_PORT to 3307\n";
        } else {
            echo "❌ MySQL is not running on any common ports\n";
            echo "💡 Please start MySQL service first\n";
        }
    }
}

// Test database connection
echo "\nTesting database connection...\n";
$dbName = preg_match('/DB_DATABASE=([^\s]+)/', $envContent, $matches) ? $matches[1] : 'db_kepegunmul';
$username = preg_match('/DB_USERNAME=([^\s]+)/', $envContent, $matches) ? $matches[1] : 'root';
$password = preg_match('/DB_PASSWORD=([^\s]+)/', $envContent, $matches) ? $matches[1] : 'root';

try {
    $dsn = "mysql:host=$currentHost;port=$currentPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connection successful!\n";

    // Test tabel pegawais
    $stmt = $pdo->query("SHOW TABLES LIKE 'pegawais'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'pegawais' exists\n";
    } else {
        echo "❌ Table 'pegawais' does not exist\n";
        echo "💡 You need to run migrations\n";
    }

} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";

    // Coba tanpa database
    try {
        $dsn = "mysql:host=$currentHost;port=$currentPort;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✅ MySQL server connection successful\n";

        // Cek database
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = [];
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $databases[] = $row[0];
        }

        if (!in_array($dbName, $databases)) {
            echo "❌ Database '$dbName' does not exist\n";
            echo "💡 Creating database...\n";

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✅ Database '$dbName' created\n";
        }

    } catch (PDOException $e2) {
        echo "❌ MySQL server connection failed: " . $e2->getMessage() . "\n";
    }
}

// Simpan perubahan ke file .env
if (file_put_contents($envFile, $envContent)) {
    echo "\n✅ .env file updated successfully\n";
} else {
    echo "\n❌ Failed to update .env file\n";
}

echo "\n=== Next Steps ===\n";
echo "1. If MySQL is not running, start it first\n";
echo "2. Run migrations: php artisan migrate\n";
echo "3. Run seeders: php artisan db:seed\n";
echo "4. Clear cache: php artisan config:clear\n";
echo "5. Try logging in again\n\n";

echo "=== Common Solutions ===\n";
echo "If using Laragon:\n";
echo "- Open Laragon\n";
echo "- Click 'Start All'\n";
echo "- Open Terminal in Laragon\n";
echo "- Navigate to project directory\n";
echo "- Run: php artisan migrate\n\n";

echo "If using XAMPP:\n";
echo "- Open XAMPP Control Panel\n";
echo "- Start MySQL and Apache\n";
echo "- Open Command Prompt\n";
echo "- Navigate to project directory\n";
echo "- Run: php artisan migrate\n";
?>
