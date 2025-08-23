<?php

/**
 * Real-time Performance Monitoring Script
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'monitor_performance.php';"
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    public function monitorUsulanDetail($usulanId = 7)
    {
        echo "🔍 MONITORING PERFORMANCE UNTUK USULAN ID: {$usulanId}\n";
        echo "================================================\n\n";

        // Enable query logging
        DB::enableQueryLog();
        
        $startTime = microtime(true);
        $memoryBefore = memory_get_usage(true);

        try {
            // Simulate the exact query from AdminFakultasController
            $usulan = \App\Models\KepegawaianUniversitas\Usulan::find($usulanId);
            
            if (!$usulan) {
                echo "❌ Usulan dengan ID {$usulanId} tidak ditemukan\n";
                return;
            }

            // Load relationships with optimized eager loading
            $usulan->load([
                'pegawai:id,nama_lengkap,email,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_id',
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

            // Simulate validation data processing
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

            echo "✅ PERFORMANCE METRICS:\n";
            echo "   - Execution Time: " . round($executionTime, 2) . "ms\n";
            echo "   - Memory Used: " . round($memoryUsed / 1024 / 1024, 2) . "MB\n";
            echo "   - Total Queries: {$queryCount}\n";
            echo "   - Slow Queries (>100ms): " . count($slowQueries) . "\n\n";

            if (count($slowQueries) > 0) {
                echo "⚠️  SLOW QUERIES DETECTED:\n";
                foreach ($slowQueries as $index => $query) {
                    echo "   Query " . ($index + 1) . ": " . $query['time'] . "ms\n";
                    echo "   SQL: " . $query['sql'] . "\n\n";
                }
            }

            // Cache hit analysis
            $cacheHits = 0;
            $cacheMisses = 0;
            
            $cacheKeys = [
                "validation_fields_{$usulan->id}",
                "bkd_labels_{$usulan->id}",
                "existing_validation_{$usulan->id}_admin_fakultas",
                "dokumen_data_{$usulan->id}"
            ];

            foreach ($cacheKeys as $key) {
                if (Cache::has($key)) {
                    $cacheHits++;
                } else {
                    $cacheMisses++;
                }
            }

            echo "💾 CACHE ANALYSIS:\n";
            echo "   - Cache Hits: {$cacheHits}\n";
            echo "   - Cache Misses: {$cacheMisses}\n";
            echo "   - Hit Rate: " . round(($cacheHits / ($cacheHits + $cacheMisses)) * 100, 1) . "%\n\n";

            // Performance recommendations
            echo "🎯 RECOMMENDATIONS:\n";
            if ($executionTime > 1000) {
                echo "   - ⚠️  Execution time is high (>1s). Consider additional caching.\n";
            }
            if (count($slowQueries) > 0) {
                echo "   - ⚠️  Slow queries detected. Review database indexes.\n";
            }
            if ($cacheMisses > $cacheHits) {
                echo "   - ⚠️  Low cache hit rate. Consider pre-warming cache.\n";
            }
            if ($executionTime < 500 && count($slowQueries) === 0) {
                echo "   - ✅ Performance is optimal!\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }

        // Disable query logging
        DB::disableQueryLog();
    }

    public function clearAllCache()
    {
        echo "🧹 CLEARING ALL CACHE...\n";
        Cache::flush();
        echo "✅ Cache cleared successfully!\n\n";
    }

    public function warmCache($usulanId = 7)
    {
        echo "🔥 WARMING CACHE FOR USULAN ID: {$usulanId}...\n";
        
        try {
            $usulan = \App\Models\KepegawaianUniversitas\Usulan::find($usulanId);
            
            if (!$usulan) {
                echo "❌ Usulan tidak ditemukan\n";
                return;
            }

            // Pre-warm cache
            Cache::remember("validation_fields_{$usulan->id}", 300, function () use ($usulan) {
                return ['test' => 'data'];
            });

            Cache::remember("bkd_labels_{$usulan->id}", 300, function () use ($usulan) {
                return ['test' => 'label'];
            });

            Cache::remember("existing_validation_{$usulan->id}_admin_fakultas", 300, function () use ($usulan) {
                return ['test' => 'validation'];
            });

            Cache::remember("dokumen_data_{$usulan->id}", 300, function () use ($usulan) {
                return ['test' => 'dokumen'];
            });

            echo "✅ Cache warmed successfully!\n\n";
        } catch (\Exception $e) {
            echo "❌ Error warming cache: " . $e->getMessage() . "\n";
        }
    }
}

// Run monitoring
$monitor = new PerformanceMonitor();

echo "🚀 PERFORMANCE MONITORING MENU\n";
echo "==============================\n";
echo "1. Monitor Usulan Detail Performance\n";
echo "2. Clear All Cache\n";
echo "3. Warm Cache\n";
echo "4. All Operations\n\n";

// Run all operations
$monitor->clearAllCache();
$monitor->warmCache(7);
$monitor->monitorUsulanDetail(7);
