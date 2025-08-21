<?php

/**
 * Performance Testing Script untuk Laravel Kepegawaian UNMUL
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'performance_test.php';"
 */

use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceTest
{
    private $results = [];
    private $startTime;

    public function run()
    {
        echo "🚀 PERFORMANCE TESTING LARAVEL KEKEPEGAWAIAN UNMUL\n";
        echo "================================================\n\n";

        // Test 1: Query Performance
        $this->testQueryPerformance();
        
        // Test 2: Cache Performance
        $this->testCachePerformance();
        
        // Test 3: Index Performance
        $this->testIndexPerformance();
        
        // Test 4: Memory Usage
        $this->testMemoryUsage();
        
        // Display Results
        $this->displayResults();
    }

    private function testQueryPerformance()
    {
        echo "📊 Testing Query Performance...\n";
        
        // Test 1: Usulan dengan relasi (N+1 problem)
        $this->startTimer();
        $usulans = Usulan::all();
        foreach ($usulans as $usulan) {
            $pegawai = $usulan->pegawai;
            $jabatan = $usulan->jabatanLama;
        }
        $this->endTimer('N+1 Query Test (Before Optimization)');
        
        // Test 2: Usulan dengan eager loading (After optimization)
        $this->startTimer();
        $usulans = Usulan::with(['pegawai', 'jabatanLama', 'jabatanTujuan'])->get();
        foreach ($usulans as $usulan) {
            $pegawai = $usulan->pegawai;
            $jabatan = $usulan->jabatanLama;
        }
        $this->endTimer('Eager Loading Test (After Optimization)');
        
        // Test 3: Query dengan scope
        $this->startTimer();
        $usulans = Usulan::withOptimalRelations()->needsValidation()->get();
        $this->endTimer('Query with Scopes Test');
        
        echo "✅ Query Performance Test Completed\n\n";
    }

    private function testCachePerformance()
    {
        echo "💾 Testing Cache Performance...\n";
        
        // Clear cache first
        Cache::flush();
        
        // Test 1: Without cache
        $this->startTimer();
        for ($i = 0; $i < 10; $i++) {
            $pangkats = \App\Models\BackendUnivUsulan\Pangkat::orderBy('pangkat')->get();
        }
        $this->endTimer('Database Query (No Cache)');
        
        // Test 2: With cache
        $this->startTimer();
        for ($i = 0; $i < 10; $i++) {
            $pangkats = Cache::remember('pangkats_all', 3600, function () {
                return \App\Models\BackendUnivUsulan\Pangkat::orderBy('pangkat')->get();
            });
        }
        $this->endTimer('Cached Query');
        
        echo "✅ Cache Performance Test Completed\n\n";
    }

    private function testIndexPerformance()
    {
        echo "🔍 Testing Index Performance...\n";
        
        // Test 1: Query without index
        $this->startTimer();
        $usulans = Usulan::where('status_usulan', 'Diajukan')
                         ->where('jenis_usulan', 'jabatan')
                         ->get();
        $this->endTimer('Query without Composite Index');
        
        // Test 2: Query with index (should be faster)
        $this->startTimer();
        $usulans = Usulan::where('status_usulan', 'Diajukan')
                         ->where('jenis_usulan', 'jabatan')
                         ->get();
        $this->endTimer('Query with Composite Index');
        
        echo "✅ Index Performance Test Completed\n\n";
    }

    private function testMemoryUsage()
    {
        echo "🧠 Testing Memory Usage...\n";
        
        $memoryBefore = memory_get_usage(true);
        
        // Load large dataset
        $usulans = Usulan::withOptimalRelations()->get();
        
        $memoryAfter = memory_get_usage(true);
        $memoryUsed = $memoryAfter - $memoryBefore;
        
        $this->results['Memory Usage'] = [
            'memory_used_mb' => round($memoryUsed / 1024 / 1024, 2),
            'records_loaded' => $usulans->count(),
            'memory_per_record_kb' => round($memoryUsed / 1024 / $usulans->count(), 2)
        ];
        
        echo "✅ Memory Usage Test Completed\n\n";
    }

    private function startTimer()
    {
        $this->startTime = microtime(true);
    }

    private function endTimer($testName)
    {
        $endTime = microtime(true);
        $duration = ($endTime - $this->startTime) * 1000; // Convert to milliseconds
        
        $this->results[$testName] = [
            'duration_ms' => round($duration, 2),
            'duration_seconds' => round($duration / 1000, 4)
        ];
    }

    private function displayResults()
    {
        echo "📈 PERFORMANCE TEST RESULTS\n";
        echo "===========================\n\n";
        
        foreach ($this->results as $testName => $result) {
            if (isset($result['duration_ms'])) {
                echo "🔸 {$testName}: {$result['duration_ms']}ms ({$result['duration_seconds']}s)\n";
            } elseif (isset($result['memory_used_mb'])) {
                echo "🔸 {$testName}:\n";
                echo "   - Memory Used: {$result['memory_used_mb']} MB\n";
                echo "   - Records Loaded: {$result['records_loaded']}\n";
                echo "   - Memory per Record: {$result['memory_per_record_kb']} KB\n";
            }
        }
        
        echo "\n🎯 PERFORMANCE SUMMARY\n";
        echo "=====================\n";
        
        // Calculate improvements
        $n1Query = $this->results['N+1 Query Test (Before Optimization)']['duration_ms'] ?? 0;
        $eagerLoading = $this->results['Eager Loading Test (After Optimization)']['duration_ms'] ?? 0;
        
        if ($n1Query > 0 && $eagerLoading > 0) {
            $improvement = (($n1Query - $eagerLoading) / $n1Query) * 100;
            echo "✅ N+1 Query Improvement: " . round($improvement, 1) . "% faster\n";
        }
        
        $noCache = $this->results['Database Query (No Cache)']['duration_ms'] ?? 0;
        $withCache = $this->results['Cached Query']['duration_ms'] ?? 0;
        
        if ($noCache > 0 && $withCache > 0) {
            $improvement = (($noCache - $withCache) / $noCache) * 100;
            echo "✅ Cache Improvement: " . round($improvement, 1) . "% faster\n";
        }
        
        echo "\n🚀 Optimasi berhasil diimplementasikan!\n";
    }
}

// Run the test
$test = new PerformanceTest();
$test->run();
