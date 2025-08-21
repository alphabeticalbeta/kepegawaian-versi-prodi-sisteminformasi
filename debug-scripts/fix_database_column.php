<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATABASE COLUMN FIXER ===\n\n";

try {
    echo "Testing database connection...\n";

    // Test connection
    DB::connection()->getPdo();
    echo "✅ Database connection successful!\n\n";

    echo "Checking if periode_usulans table exists...\n";

    // Check if table exists
    $tableExists = Schema::hasTable('periode_usulans');
    echo "Table 'periode_usulans' exists: " . ($tableExists ? 'YES' : 'NO') . "\n\n";

    if (!$tableExists) {
        echo "❌ Table 'periode_usulans' does not exist!\n";
        echo "Please run migrations first: php artisan migrate\n";
        exit(1);
    }

    echo "Checking if status_kepegawaian column exists...\n";

    // Check if column exists
    $hasColumn = Schema::hasColumn('periode_usulans', 'status_kepegawaian');
    echo "Column 'status_kepegawaian' exists: " . ($hasColumn ? 'YES' : 'NO') . "\n\n";

    if (!$hasColumn) {
        echo "Adding status_kepegawaian column...\n";

        // Add column using raw SQL
        DB::statement("ALTER TABLE periode_usulans ADD COLUMN status_kepegawaian JSON NULL AFTER jenis_usulan COMMENT 'Status kepegawaian yang diizinkan untuk mengakses periode ini'");

        echo "✅ Column 'status_kepegawaian' added successfully!\n\n";
    } else {
        echo "✅ Column 'status_kepegawaian' already exists!\n\n";
    }

    // Verify the column was added
    $hasColumnAfter = Schema::hasColumn('periode_usulans', 'status_kepegawaian');
    echo "Verification - Column 'status_kepegawaian' exists: " . ($hasColumnAfter ? 'YES' : 'NO') . "\n";

    if ($hasColumnAfter) {
        echo "✅ Database fix completed successfully!\n";
        echo "✅ You can now create periode usulan with status_kepegawaian field.\n\n";

        // Show table structure
        echo "Current table structure:\n";
        $columns = DB::select("SHOW COLUMNS FROM periode_usulans");
        foreach ($columns as $column) {
            echo "- {$column->Field}: {$column->Type} " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
        }
    } else {
        echo "❌ Database fix failed!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIX COMPLETE ===\n";
