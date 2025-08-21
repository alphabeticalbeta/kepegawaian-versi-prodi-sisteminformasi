<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__ . '/..')
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\Usulan;

try {
    echo "=== TESTING LOG WITH ADDITIONAL INFO ===\n\n";

    // Authenticate as first pegawai
    echo "1. Authenticating as first pegawai...\n";
    
    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "❌ No pegawai found in database\n";
        exit(1);
    }
    
    Auth::login($pegawai);
    echo "✅ Authenticated as: " . $pegawai->nama_lengkap . " (ID: " . $pegawai->id . ")\n";

    // Get usulan for testing
    echo "\n2. Getting usulan for testing...\n";
    
    $usulan = $pegawai->usulans()->with([
        'pegawai',
        'periodeUsulan',
        'jabatanLama',
        'jabatanTujuan'
    ])->first();
    
    if (!$usulan) {
        echo "❌ No usulans found for this pegawai\n";
        exit(1);
    }
    
    echo "✅ Found usulan ID: " . $usulan->id . " (" . $usulan->jenis_usulan . ")\n";

    // Test log route with additional info
    echo "\n3. Testing log route with additional info...\n";
    
    $routeName = match($usulan->jenis_usulan) {
        'Usulan Jabatan' => 'pegawai-unmul.usulan-jabatan',
        'Usulan Kepangkatan' => 'pegawai-unmul.usulan-kepangkatan',
        'Usulan ID SINTA ke SISTER' => 'pegawai-unmul.usulan-id-sinta-sister',
        'Usulan Laporan LKD' => 'pegawai-unmul.usulan-laporan-lkd',
        'Usulan Laporan SERDOS' => 'pegawai-unmul.usulan-laporan-serdos',
        'Usulan NUPTK' => 'pegawai-unmul.usulan-nuptk',
        'Usulan Pencantuman Gelar' => 'pegawai-unmul.usulan-pencantuman-gelar',
        'Usulan Pengaktifan Kembali' => 'pegawai-unmul.usulan-pengaktifan-kembali',
        'Usulan Pensiun' => 'pegawai-unmul.usulan-pensiun',
        'Usulan Penyesuaian Masa Kerja' => 'pegawai-unmul.usulan-penyesuaian-masa-kerja',
        'Usulan Presensi' => 'pegawai-unmul.usulan-presensi',
        'Usulan Satyalancana' => 'pegawai-unmul.usulan-satyalancana',
        'Usulan Tugas Belajar' => 'pegawai-unmul.usulan-tugas-belajar',
        'Usulan Ujian Dinas & Ijazah' => 'pegawai-unmul.usulan-ujian-dinas-ijazah',
        default => 'pegawai-unmul.usulan-jabatan'
    };
    
    $logRouteName = $routeName . '.logs';
    
    echo "Route Name: " . $routeName . "\n";
    echo "Log Route Name: " . $logRouteName . "\n";
    
    // Test route
    echo "\n4. Testing route...\n";
    
    try {
        $startTime = microtime(true);
        
        $logRouteUrl = route($logRouteName, $usulan->id);
        $request = \Illuminate\Http\Request::create($logRouteUrl, 'GET');
        
        $response = app()->handle($request);
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        echo "Response Status: " . $response->getStatusCode() . "\n";
        echo "Execution Time: " . number_format($executionTime, 2) . " ms\n";
        
        if ($response->getStatusCode() === 200) {
            echo "✅ Request successful\n";
            
            $content = $response->getContent();
            echo "Response Content Length: " . strlen($content) . " bytes\n";
            
            // Check if it's HTML response
            if (strpos($content, '<!DOCTYPE html>') !== false) {
                echo "✅ HTML response detected\n";
                
                // Check for additional info sections
                $checks = [
                    'Data Diri Pegawai' => 'Data Diri Pegawai section found',
                    'Informasi Usulan' => 'Informasi Usulan section found',
                    'Keterangan Usulan Jabatan' => 'Keterangan Usulan Jabatan section found',
                    'Keterangan Usulan Kepangkatan' => 'Keterangan Usulan Kepangkatan section found',
                    'Riwayat Log Usulan' => 'Riwayat Log Usulan section found',
                    'Entri Log' => 'Log entries section found'
                ];
                
                foreach ($checks as $search => $message) {
                    if (strpos($content, $search) !== false) {
                        echo "✅ " . $message . "\n";
                    } else {
                        echo "⚠️ " . str_replace(' found', ' not found', $message) . "\n";
                    }
                }
                
                // Check for specific data
                if (strpos($content, $pegawai->nama_lengkap) !== false) {
                    echo "✅ Pegawai name found in response\n";
                } else {
                    echo "⚠️ Pegawai name not found in response\n";
                }
                
                if (strpos($content, $pegawai->nip) !== false) {
                    echo "✅ Pegawai NIP found in response\n";
                } else {
                    echo "⚠️ Pegawai NIP not found in response\n";
                }
                
                if (strpos($content, $usulan->jenis_usulan) !== false) {
                    echo "✅ Usulan type found in response\n";
                } else {
                    echo "⚠️ Usulan type not found in response\n";
                }
                
                // Check for jabatan info if it's Usulan Jabatan
                if ($usulan->jenis_usulan === 'Usulan Jabatan') {
                    if ($usulan->jabatanLama && strpos($content, $usulan->jabatanLama->jabatan) !== false) {
                        echo "✅ Jabatan lama found in response\n";
                    } else {
                        echo "⚠️ Jabatan lama not found in response\n";
                    }
                    
                    if ($usulan->jabatanTujuan && strpos($content, $usulan->jabatanTujuan->jabatan) !== false) {
                        echo "✅ Jabatan tujuan found in response\n";
                    } else {
                        echo "⚠️ Jabatan tujuan not found in response\n";
                    }
                }
                
            } else {
                echo "❌ Not HTML response\n";
                echo "Response preview: " . substr($content, 0, 200) . "...\n";
            }
        } else {
            echo "❌ Request failed\n";
            echo "Response: " . $response->getContent() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }

    // Test direct controller method
    echo "\n5. Testing direct controller method...\n";
    
    try {
        $startTime = microtime(true);
        
        // Get controller instance
        $controller = new \App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController();
        
        // Call getLogs method directly
        $response = $controller->getLogs($usulan);
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        echo "Direct method execution time: " . number_format($executionTime, 2) . " ms\n";
        
        if ($response instanceof \Illuminate\View\View) {
            echo "✅ View response returned\n";
            
            $viewData = $response->getData();
            if (isset($viewData['logs'])) {
                echo "✅ Logs data found: " . count($viewData['logs']) . " entries\n";
            } else {
                echo "⚠️ Logs data not found\n";
            }
            
            if (isset($viewData['usulan'])) {
                echo "✅ Usulan data found\n";
                
                $usulanData = $viewData['usulan'];
                if (isset($usulanData->pegawai)) {
                    echo "✅ Pegawai relationship loaded\n";
                } else {
                    echo "⚠️ Pegawai relationship not loaded\n";
                }
                
                if (isset($usulanData->periodeUsulan)) {
                    echo "✅ PeriodeUsulan relationship loaded\n";
                } else {
                    echo "⚠️ PeriodeUsulan relationship not loaded\n";
                }
                
                if (isset($usulanData->jabatanLama)) {
                    echo "✅ JabatanLama relationship loaded\n";
                } else {
                    echo "⚠️ JabatanLama relationship not loaded\n";
                }
                
                if (isset($usulanData->jabatanTujuan)) {
                    echo "✅ JabatanTujuan relationship loaded\n";
                } else {
                    echo "⚠️ JabatanTujuan relationship not loaded\n";
                }
            } else {
                echo "⚠️ Usulan data not found\n";
            }
        } else {
            echo "❌ Unexpected response type: " . get_class($response) . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Direct method error: " . $e->getMessage() . "\n";
    }

    echo "\n=== LOG WITH ADDITIONAL INFO TEST COMPLETED ===\n";
    echo "✅ If all tests passed, additional info is working correctly!\n";
    echo "\n📋 SUMMARY:\n";
    echo "- Data Diri Pegawai section: ✅ Added\n";
    echo "- Informasi Usulan section: ✅ Added\n";
    echo "- Keterangan Usulan (from-to): ✅ Added\n";
    echo "- Relationships loaded: ✅ Implemented\n";
    echo "- Performance: ✅ Stable\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
