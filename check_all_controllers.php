<?php
/**
 * Script untuk mengecek syntax semua controller
 */

$controllers = [
    'UsulanJabatanController.php',
    'UsulanKepangkatanController.php',
    'UsulanLaporanLkdController.php',
    'UsulanLaporanSerdosController.php',
    'UsulanIdSintaSisterController.php',
    'UsulanNuptkController.php',
    'UsulanPencantumanGelarController.php',
    'UsulanPengaktifanKembaliController.php',
    'UsulanPensiunController.php',
    'UsulanPenyesuaianMasaKerjaController.php',
    'UsulanPresensiController.php',
    'UsulanSatyalancanaController.php',
    'UsulanTugasBelajarController.php',
    'UsulanUjianDinasIjazahController.php'
];

echo "🔍 MENGE CEK SYNTAX SEMUA CONTROLLER\n";
echo "===================================\n\n";

$allGood = true;

foreach ($controllers as $controller) {
    $filePath = 'app/Http/Controllers/Backend/PegawaiUnmul/' . $controller;

    if (!file_exists($filePath)) {
        echo "❌ File tidak ditemukan: {$controller}\n";
        $allGood = false;
        continue;
    }

    $output = shell_exec("php -l {$filePath} 2>&1");

    if (strpos($output, 'No syntax errors detected') !== false) {
        echo "✅ {$controller}\n";
    } else {
        echo "❌ {$controller} - Ada syntax error\n";
        echo "   {$output}\n";
        $allGood = false;
    }
}

echo "\n";
if ($allGood) {
    echo "🎉 SEMUA CONTROLLER BEBAS DARI SYNTAX ERROR!\n";
} else {
    echo "⚠️  ADA CONTROLLER YANG MASIH MEMILIKI SYNTAX ERROR\n";
}
?>
