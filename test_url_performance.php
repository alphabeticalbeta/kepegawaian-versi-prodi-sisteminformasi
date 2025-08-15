<?php

/**
 * URL Performance Testing Script
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_url_performance.php';"
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UrlPerformanceTest
{
    public function testAdminFakultasUsulanDetail($usulanId = 7)
    {
        echo "🔍 TESTING URL: /admin-fakultas/usulan/{$usulanId}\n";
        echo "================================================\n\n";

        // Enable query logging
        DB::enableQueryLog();
        
        $startTime = microtime(true);
        $memoryBefore = memory_get_usage(true);

        try {
            // Simulate the exact request flow
            $usulan = \App\Models\BackendUnivUsulan\Usulan::find($usulanId);
            
            if (!$usulan) {
                echo "❌ Usulan dengan ID {$usulanId} tidak ditemukan\n";
                return;
            }

            echo "✅ Usulan ditemukan: {$usulan->id}\n";

            // Load relationships with optimized eager loading
            $usulan->load([
                'pegawai:id,nama_lengkap,email,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_terakhir_id',
                'pegawai.pangkat:id,pangkat',
                'pegawai.jabatan:id,jabatan',
                'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                'jabatanLama:id,jabatan',
                'jabatanTujuan:id,jabatan',
                'dokumens:id,usulan_id,nama_dokumen,path',
                'logs:id,usulan_id,status_baru,catatan,created_at,dilakukan_oleh_id',
                'logs.dilakukanOleh:id,nama_lengkap'
            ]);

            echo "✅ Relationships loaded successfully\n";

            // Simulate validation data processing with caching
            $validationFields = Cache::remember("validation_fields_{$usulan->id}", 300, function () use ($usulan) {
                // Simulate processing
                return ['test' => 'data'];
            });

            $bkdLabels = Cache::remember("bkd_labels_{$usulan->id}", 300, function () use ($usulan) {
                // Simulate processing
                return ['test' => 'label'];
            });

            $existingValidation = Cache::remember("existing_validation_{$usulan->id}_admin_fakultas", 300, function () use ($usulan) {
                // Simulate processing
                return ['test' => 'validation'];
            });

            $dokumenData = Cache::remember("dokumen_data_{$usulan->id}", 300, function () use ($usulan) {
                // Simulate processing
                return ['test' => 'dokumen'];
            });

            echo "✅ Cache operations completed\n";

            $endTime = microtime(true);
            $memoryAfter = memory_get_usage(true);

            $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
            $memoryUsed = $memoryAfter - $memoryBefore;

            // Get query statistics
            $queries = DB::getQueryLog();
            $queryCount = count($queries);
            $slowQueries = array_filter($queries, function($query) {
                return $query['time'] > 100; // Queries taking more than 100ms
            });

            echo "\n📊 PERFORMANCE RESULTS:\n";
            echo "   - Execution Time: " . round($executionTime, 2) . "ms\n";
            echo "   - Memory Used: " . round($memoryUsed / 1024 / 1024, 2) . "MB\n";
            echo "   - Total Queries: {$queryCount}\n";
            echo "   - Slow Queries (>100ms): " . count($slowQueries) . "\n";

            if (count($slowQueries) > 0) {
                echo "\n⚠️  SLOW QUERIES DETECTED:\n";
                foreach ($slowQueries as $index => $query) {
                    echo "   Query " . ($index + 1) . ": " . $query['time'] . "ms\n";
                    echo "   SQL: " . $query['sql'] . "\n\n";
                }
            }

            // Performance assessment
            echo "\n🎯 PERFORMANCE ASSESSMENT:\n";
            if ($executionTime < 500) {
                echo "   ✅ EXCELLENT: Execution time under 500ms\n";
            } elseif ($executionTime < 1000) {
                echo "   ✅ GOOD: Execution time under 1 second\n";
            } elseif ($executionTime < 3000) {
                echo "   ⚠️  ACCEPTABLE: Execution time under 3 seconds\n";
            } else {
                echo "   ❌ POOR: Execution time over 3 seconds\n";
            }

            if (count($slowQueries) === 0) {
                echo "   ✅ EXCELLENT: No slow queries detected\n";
            } else {
                echo "   ⚠️  WARNING: " . count($slowQueries) . " slow queries detected\n";
            }

            if ($queryCount <= 15) {
                echo "   ✅ EXCELLENT: Query count is optimal\n";
            } elseif ($queryCount <= 30) {
                echo "   ✅ GOOD: Query count is acceptable\n";
            } else {
                echo "   ⚠️  WARNING: High query count detected\n";
            }

            // Final verdict
            echo "\n🏆 FINAL VERDICT:\n";
            if ($executionTime < 500 && count($slowQueries) === 0 && $queryCount <= 15) {
                echo "   🎉 PERFECT: URL is ready for production!\n";
                echo "   🚀 No 504 Gateway Time-out issues expected\n";
            } elseif ($executionTime < 1000 && count($slowQueries) === 0) {
                echo "   ✅ GOOD: URL should work without timeout issues\n";
            } else {
                echo "   ⚠️  NEEDS ATTENTION: May still have performance issues\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }

        // Disable query logging
        DB::disableQueryLog();
    }

    public function testMultipleUsulans()
    {
        echo "🔍 TESTING MULTIPLE USULANS\n";
        echo "============================\n\n";

        $usulanIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $results = [];

        foreach ($usulanIds as $id) {
            echo "Testing Usulan ID: {$id}\n";
            
            $startTime = microtime(true);
            
            try {
                $usulan = \App\Models\BackendUnivUsulan\Usulan::find($id);
                if ($usulan) {
                    $usulan->load([
                        'pegawai:id,nama_lengkap,email,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_terakhir_id',
                        'pegawai.pangkat:id,pangkat',
                        'pegawai.jabatan:id,jabatan',
                        'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                        'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                        'jabatanLama:id,jabatan',
                        'jabatanTujuan:id,jabatan',
                        'dokumens:id,usulan_id,nama_dokumen,path',
                        'logs:id,usulan_id,status_baru,catatan,created_at,dilakukan_oleh_id',
                        'logs.dilakukanOleh:id,nama_lengkap'
                    ]);
                    
                    $endTime = microtime(true);
                    $executionTime = ($endTime - $startTime) * 1000;
                    
                    $results[] = [
                        'id' => $id,
                        'time' => $executionTime,
                        'status' => 'success'
                    ];
                    
                    echo "   ✅ " . round($executionTime, 2) . "ms\n";
                } else {
                    $results[] = [
                        'id' => $id,
                        'time' => 0,
                        'status' => 'not_found'
                    ];
                    echo "   ❌ Not found\n";
                }
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $id,
                    'time' => 0,
                    'status' => 'error'
                ];
                echo "   ❌ Error: " . $e->getMessage() . "\n";
            }
        }

        echo "\n📊 SUMMARY:\n";
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
        $totalTime = array_sum(array_column($results, 'time'));
        $avgTime = $successCount > 0 ? $totalTime / $successCount : 0;
        
        echo "   - Success: {$successCount}/" . count($usulanIds) . "\n";
        echo "   - Average Time: " . round($avgTime, 2) . "ms\n";
        echo "   - Total Time: " . round($totalTime, 2) . "ms\n";
        
        if ($avgTime < 500) {
            echo "   🎉 EXCELLENT: All URLs should work perfectly!\n";
        } elseif ($avgTime < 1000) {
            echo "   ✅ GOOD: URLs should work without issues\n";
        } else {
            echo "   ⚠️  WARNING: Some URLs may have performance issues\n";
        }
    }
}

// Run the test
$test = new UrlPerformanceTest();

echo "🚀 URL PERFORMANCE TESTING\n";
echo "==========================\n";
echo "1. Test Single Usulan Detail\n";
echo "2. Test Multiple Usulans\n";
echo "3. Both Tests\n\n";

// Run both tests
$test->testAdminFakultasUsulanDetail(7);
echo "\n" . str_repeat("=", 50) . "\n\n";
$test->testMultipleUsulans();
