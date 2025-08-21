<?php
/**
 * Script untuk menambahkan route logs ke semua jenis usulan
 * Jalankan script ini untuk menambahkan route logs ke routes/backend.php
 */

$usulanTypes = [
    'usulan-kepangkatan' => 'UsulanKepangkatanController',
    'usulan-id-sinta-sister' => 'UsulanIdSintaSisterController',
    'usulan-laporan-lkd' => 'UsulanLaporanLkdController',
    'usulan-laporan-serdos' => 'UsulanLaporanSerdosController',
    'usulan-nuptk' => 'UsulanNuptkController',
    'usulan-pencantuman-gelar' => 'UsulanPencantumanGelarController',
    'usulan-pengaktifan-kembali' => 'UsulanPengaktifanKembaliController',
    'usulan-pensiun' => 'UsulanPensiunController',
    'usulan-penyesuaian-masa-kerja' => 'UsulanPenyesuaianMasaKerjaController',
    'usulan-presensi' => 'UsulanPresensiController',
    'usulan-satyalancana' => 'UsulanSatyalancanaController',
    'usulan-tugas-belajar' => 'UsulanTugasBelajarController',
    'usulan-ujian-dinas-ijazah' => 'UsulanUjianDinasIjazahController'
];

$routesFile = 'routes/backend.php';

echo "🚀 MENAMBAHKAN ROUTE LOGS KE SEMUA USULAN\n";
echo "========================================\n\n";

if (!file_exists($routesFile)) {
    echo "❌ File routes tidak ditemukan: {$routesFile}\n";
    exit;
}

$content = file_get_contents($routesFile);

// Cek apakah route logs sudah ada
$existingLogRoutes = [];
foreach ($usulanTypes as $usulanType => $controller) {
    $routePattern = "Route::get('/{$usulanType}/\{usulan\}/logs'";
    if (strpos($content, $routePattern) !== false) {
        $existingLogRoutes[] = $usulanType;
    }
}

if (!empty($existingLogRoutes)) {
    echo "⚠️  Route logs sudah ada untuk:\n";
    foreach ($existingLogRoutes as $type) {
        echo "   - {$type}\n";
    }
    echo "\n";
}

// Tambahkan route logs untuk usulan yang belum ada
$routesToAdd = "\n    // Log routes untuk semua jenis usulan\n";
$addedCount = 0;

foreach ($usulanTypes as $usulanType => $controller) {
    $routePattern = "Route::get('/{$usulanType}/\{usulan\}/logs'";
    if (strpos($content, $routePattern) === false) {
        $routesToAdd .= "    Route::get('/{$usulanType}/{{usulan}}/logs', [App\\Http\\Controllers\\Backend\\PegawaiUnmul\\{$controller}::class, 'getLogs'])->name('{$usulanType}.logs');\n";
        $addedCount++;
    }
}

if ($addedCount > 0) {
    // Cari posisi untuk menambahkan route logs
    $insertPosition = strrpos($content, '});');
    if ($insertPosition !== false) {
        $content = substr($content, 0, $insertPosition) . $routesToAdd . substr($content, $insertPosition);
        file_put_contents($routesFile, $content);
        echo "✅ Berhasil menambahkan {$addedCount} route logs\n";
    } else {
        echo "❌ Tidak dapat menemukan posisi untuk menambahkan route\n";
    }
} else {
    echo "✅ Semua route logs sudah tersedia\n";
}

echo "\n📋 Route yang ditambahkan:\n";
foreach ($usulanTypes as $usulanType => $controller) {
    echo "   - GET /{$usulanType}/{{usulan}}/logs → {$controller}@getLogs\n";
}

echo "\n🔧 Langkah selanjutnya:\n";
echo "1. Pastikan semua controller memiliki method getLogs()\n";
echo "2. Jalankan script add_log_button_to_all_usulan.php\n";
echo "3. Test fitur log di setiap jenis usulan\n";
?>
