<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATABASE HOST FIXER ===\n\n";

echo "Current database configuration:\n";
echo "- DB_HOST: " . env('DB_HOST') . "\n";
echo "- DB_PORT: " . env('DB_PORT') . "\n";
echo "- DB_DATABASE: " . env('DB_DATABASE') . "\n\n";

echo "Testing different host configurations...\n\n";

$hosts = [
    '127.0.0.1',
    'localhost',
    'mysql',
    'db',
    'database'
];

foreach ($hosts as $host) {
    echo "Testing host: {$host}\n";

    try {
        $pdo = new PDO(
            "mysql:host={$host};port=3306;dbname=db_kepegunmul;charset=utf8mb4",
            'root',
            '', // Assuming no password for local development
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
            ]
        );

        echo "✅ Connection successful with host: {$host}\n\n";

        // Check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'periode_usulans'");
        $tableExists = $stmt->rowCount() > 0;
        echo "Table 'periode_usulans' exists: " . ($tableExists ? 'YES' : 'NO') . "\n";

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
                    echo "✅ Working host: {$host}\n";
                    echo "✅ You can now create periode usulan with status_kepegawaian field.\n\n";

                    echo "To fix permanently, update your .env file:\n";
                    echo "DB_HOST={$host}\n\n";

                    break;
                }
            } else {
                echo "✅ Column 'status_kepegawaian' already exists!\n";
                echo "✅ Working host: {$host}\n";
                echo "✅ Database is ready for periode usulan creation.\n\n";

                echo "To fix permanently, update your .env file:\n";
                echo "DB_HOST={$host}\n\n";

                break;
            }
        } else {
            echo "❌ Table 'periode_usulans' does not exist!\n\n";
        }

    } catch (PDOException $e) {
        echo "❌ Connection failed: " . $e->getMessage() . "\n\n";
    }
}

echo "=== FIX COMPLETE ===\n";
