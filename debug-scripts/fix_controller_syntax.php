<?php
/**
 * Script untuk memperbaiki syntax error di semua controller
 */

$controllers = [
    'UsulanPencantumanGelarController.php',
    'UsulanPengaktifanKembaliController.php',
    'UsulanPensiunController.php',
    'UsulanPenyesuaianMasaKerjaController.php',
    'UsulanPresensiController.php',
    'UsulanSatyalancanaController.php',
    'UsulanTugasBelajarController.php',
    'UsulanUjianDinasIjazahController.php'
];

echo "🔧 MEMPERBAIKI SYNTAX ERROR DI CONTROLLER\n";
echo "=======================================\n\n";

foreach ($controllers as $controller) {
    $filePath = 'app/Http/Controllers/Backend/PegawaiUnmul/' . $controller;
    
    if (!file_exists($filePath)) {
        echo "❌ File tidak ditemukan: {$controller}\n";
        continue;
    }
    
    echo "📝 Memperbaiki: {$controller}\n";
    
    $content = file_get_contents($filePath);
    
    // Perbaiki masalah kurung kurawal berlebihan setelah return view
    $pattern = '/return view\([^)]+\);\s*}\s*}/';
    $replacement = 'return view($1);}';
    
    $content = preg_replace($pattern, $replacement, $content);
    
    // Perbaiki masalah kurung kurawal berlebihan yang lebih spesifik
    $content = str_replace("    }\n    }", "    }", $content);
    
    file_put_contents($filePath, $content);
    echo "✅ Berhasil diperbaiki: {$controller}\n";
}

echo "\n🎉 SELESAI! Semua controller telah diperbaiki.\n";
?>
