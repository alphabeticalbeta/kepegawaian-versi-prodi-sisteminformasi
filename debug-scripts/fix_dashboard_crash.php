<?php

// Script untuk mendiagnosis dan memperbaiki masalah crash pada dashboard admin universitas usulan

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DIAGNOSIS DASHBOARD CRASH ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";

    // Check if tables exist
    echo "2. Checking required tables...\n";
    $requiredTables = [
        'pegawais',
        'usulans',
        'periode_usulans',
        'jabatans',
        'pangkats'
    ];

    foreach ($requiredTables as $table) {
        if (Schema::hasTable($table)) {
            echo "✅ Table {$table} exists\n";
        } else {
            echo "❌ Table {$table} missing\n";
        }
    }
    echo "\n";

    // Test basic queries
    echo "3. Testing basic queries...\n";

    try {
        $pegawaiCount = DB::table('pegawais')->count();
        echo "✅ Pegawai count: {$pegawaiCount}\n";
    } catch (Exception $e) {
        echo "❌ Error counting pegawai: " . $e->getMessage() . "\n";
    }

    try {
        $usulanCount = DB::table('usulans')->count();
        echo "✅ Usulan count: {$usulanCount}\n";
    } catch (Exception $e) {
        echo "❌ Error counting usulan: " . $e->getMessage() . "\n";
    }

    try {
        $periodeCount = DB::table('periode_usulans')->count();
        echo "✅ Periode count: {$periodeCount}\n";
    } catch (Exception $e) {
        echo "❌ Error counting periode: " . $e->getMessage() . "\n";
    }

    try {
        $jabatanCount = DB::table('jabatans')->count();
        echo "✅ Jabatan count: {$jabatanCount}\n";
    } catch (Exception $e) {
        echo "❌ Error counting jabatan: " . $e->getMessage() . "\n";
    }

    try {
        $pangkatCount = DB::table('pangkats')->count();
        echo "✅ Pangkat count: {$pangkatCount}\n";
    } catch (Exception $e) {
        echo "❌ Error counting pangkat: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // Test complex queries
    echo "4. Testing complex queries...\n";

    try {
        $monthlyData = DB::table('usulans')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        echo "✅ Monthly usulan query successful\n";
    } catch (Exception $e) {
        echo "❌ Error in monthly usulan query: " . $e->getMessage() . "\n";
    }

    try {
        $statusData = DB::table('usulans')
            ->selectRaw('status_usulan, COUNT(*) as count')
            ->groupBy('status_usulan')
            ->get();
        echo "✅ Status distribution query successful\n";
    } catch (Exception $e) {
        echo "❌ Error in status distribution query: " . $e->getMessage() . "\n";
    }

    try {
        $recentUsulans = DB::table('usulans')
            ->join('pegawais', 'usulans.pegawai_id', '=', 'pegawais.id')
            ->join('periode_usulans', 'usulans.periode_usulan_id', '=', 'periode_usulans.id')
            ->join('jabatans', 'usulans.jabatan_tujuan_id', '=', 'jabatans.id')
            ->select('usulans.*', 'pegawais.nama_lengkap', 'periode_usulans.nama_periode', 'jabatans.jabatan')
            ->latest('usulans.created_at')
            ->limit(10)
            ->get();
        echo "✅ Recent usulans query successful\n";
    } catch (Exception $e) {
        echo "❌ Error in recent usulans query: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // Check for missing columns
    echo "5. Checking for missing columns...\n";

    $tableColumns = [
        'pegawais' => ['id', 'nama_lengkap', 'created_at'],
        'usulans' => ['id', 'pegawai_id', 'periode_usulan_id', 'jabatan_tujuan_id', 'status_usulan', 'jenis_usulan', 'created_at'],
        'periode_usulans' => ['id', 'nama_periode', 'status', 'created_at'],
        'jabatans' => ['id', 'jabatan', 'created_at'],
        'pangkats' => ['id', 'pangkat', 'created_at']
    ];

    foreach ($tableColumns as $table => $columns) {
        if (Schema::hasTable($table)) {
            $existingColumns = Schema::getColumnListing($table);
            foreach ($columns as $column) {
                if (in_array($column, $existingColumns)) {
                    echo "✅ {$table}.{$column}\n";
                } else {
                    echo "❌ {$table}.{$column} missing\n";
                }
            }
        }
    }
    echo "\n";

    // Test route accessibility
    echo "6. Testing route accessibility...\n";

    $routes = [
        'backend.admin-univ-usulan.dashboard',
        'backend.admin-univ-usulan.data-pegawai.index',
        'backend.admin-univ-usulan.pusat-usulan.index',
        'backend.admin-univ-usulan.jabatan.index',
        'backend.admin-univ-usulan.pangkat.index'
    ];

    foreach ($routes as $route) {
        try {
            $url = route($route);
            echo "✅ Route {$route}: {$url}\n";
        } catch (Exception $e) {
            echo "❌ Route {$route}: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";

    echo "=== DIAGNOSIS COMPLETE ===\n";
    echo "If you see any ❌ errors above, those are likely causing the crash.\n";
    echo "Check the Laravel logs for more detailed error information.\n\n";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== SUGGESTED FIXES ===\n";
echo "1. Clear Laravel cache: php artisan cache:clear\n";
echo "2. Clear config cache: php artisan config:clear\n";
echo "3. Clear route cache: php artisan route:clear\n";
echo "4. Clear view cache: php artisan view:clear\n";
echo "5. Regenerate autoload: composer dump-autoload\n";
echo "6. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "7. Check database connection in .env file\n";
echo "8. Ensure all required tables exist and have correct structure\n\n";

echo "=== QUICK FIX COMMANDS ===\n";
echo "Run these commands to clear all caches:\n";
echo "php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear && composer dump-autoload\n\n";
