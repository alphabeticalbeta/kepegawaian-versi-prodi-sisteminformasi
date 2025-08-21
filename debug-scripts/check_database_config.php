<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATABASE CONFIGURATION CHECKER ===\n\n";

try {
    echo "Checking database configuration...\n";

    // Get database config
    $config = config('database.connections.mysql');

    echo "Database Configuration:\n";
    echo "- Host: " . ($config['host'] ?? 'NOT SET') . "\n";
    echo "- Port: " . ($config['port'] ?? 'NOT SET') . "\n";
    echo "- Database: " . ($config['database'] ?? 'NOT SET') . "\n";
    echo "- Username: " . ($config['username'] ?? 'NOT SET') . "\n";
    echo "- Password: " . (empty($config['password']) ? 'EMPTY' : 'SET') . "\n";
    echo "- Charset: " . ($config['charset'] ?? 'NOT SET') . "\n";
    echo "- Collation: " . ($config['collation'] ?? 'NOT SET') . "\n\n";

    echo "Environment Variables:\n";
    echo "- DB_HOST: " . (env('DB_HOST') ?? 'NOT SET') . "\n";
    echo "- DB_PORT: " . (env('DB_PORT') ?? 'NOT SET') . "\n";
    echo "- DB_DATABASE: " . (env('DB_DATABASE') ?? 'NOT SET') . "\n";
    echo "- DB_USERNAME: " . (env('DB_USERNAME') ?? 'NOT SET') . "\n";
    echo "- DB_PASSWORD: " . (env('DB_PASSWORD') ? 'SET' : 'NOT SET') . "\n\n";

    echo "Testing connection...\n";

    // Try to connect
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10,
        ]
    );

    echo "✅ Database connection successful!\n\n";

    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'periode_usulans'");
    $tableExists = $stmt->rowCount() > 0;
    echo "Table 'periode_usulans' exists: " . ($tableExists ? 'YES' : 'NO') . "\n\n";

    if ($tableExists) {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM periode_usulans LIKE 'status_kepegawaian'");
        $columnExists = $stmt->rowCount() > 0;
        echo "Column 'status_kepegawaian' exists: " . ($columnExists ? 'YES' : 'NO') . "\n\n";

        if (!$columnExists) {
            echo "Adding status_kepegawaian column...\n";

            $sql = "ALTER TABLE periode_usulans ADD COLUMN status_kepegawaian JSON NULL AFTER jenis_usulan COMMENT 'Status kepegawaian yang diizinkan untuk mengakses periode ini'";
            $pdo->exec($sql);

            echo "✅ Column 'status_kepegawaian' added successfully!\n\n";

            // Verify
            $stmt = $pdo->query("SHOW COLUMNS FROM periode_usulans LIKE 'status_kepegawaian'");
            $columnExistsAfter = $stmt->rowCount() > 0;
            echo "Verification - Column 'status_kepegawaian' exists: " . ($columnExistsAfter ? 'YES' : 'NO') . "\n";

            if ($columnExistsAfter) {
                echo "✅ Database fix completed successfully!\n";
                echo "✅ You can now create periode usulan with status_kepegawaian field.\n";
            }
        } else {
            echo "✅ Column 'status_kepegawaian' already exists!\n";
            echo "✅ Database is ready for periode usulan creation.\n";
        }
    } else {
        echo "❌ Table 'periode_usulans' does not exist!\n";
        echo "Please run migrations first: php artisan migrate\n";
    }

} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";

    echo "Troubleshooting tips:\n";
    echo "1. Make sure MySQL/MariaDB is running\n";
    echo "2. Check if the host and port are correct\n";
    echo "3. Verify database credentials\n";
    echo "4. Check if the database exists\n";
    echo "5. Try connecting with a database client first\n\n";

    echo "Common solutions:\n";
    echo "- For XAMPP: Start MySQL service\n";
    echo "- For Laragon: Start Laragon and MySQL\n";
    echo "- For Docker: Start MySQL container\n";
    echo "- Check firewall settings\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
