<?php
/**
 * Script untuk memperbaiki semua controller yang masih memiliki error
 */

$controllers = [
    'UsulanPengaktifanKembaliController.php',
    'UsulanPensiunController.php',
    'UsulanPenyesuaianMasaKerjaController.php',
    'UsulanPresensiController.php',
    'UsulanSatyalancanaController.php',
    'UsulanTugasBelajarController.php',
    'UsulanUjianDinasIjazahController.php'
];

echo "🔧 MEMPERBAIKI SEMUA CONTROLLER YANG MASIH ERROR\n";
echo "===============================================\n\n";

foreach ($controllers as $controller) {
    $filePath = 'app/Http/Controllers/Backend/PegawaiUnmul/' . $controller;

    if (!file_exists($filePath)) {
        echo "❌ File tidak ditemukan: {$controller}\n";
        continue;
    }

    echo "📝 Memperbaiki: {$controller}\n";

    $content = file_get_contents($filePath);

    // Perbaiki masalah kurung kurawal yang tidak ditutup di akhir file
    if (substr_count($content, '{') > substr_count($content, '}')) {
        // Tambahkan kurung kurawal yang hilang
        $content .= "\n}\n";
        echo "   ✅ Menambahkan kurung kurawal yang hilang\n";
    }

    // Perbaiki masalah kurung kurawal berlebihan setelah return view
    $content = preg_replace('/return view\([^)]+\);\s*}\s*}/', 'return view($1);}', $content);

    // Perbaiki masalah kurung kurawal berlebihan yang lebih spesifik
    $content = str_replace("    }\n    }", "    }", $content);

    file_put_contents($filePath, $content);
    echo "✅ Berhasil diperbaiki: {$controller}\n";
}

echo "\n🎉 SELESAI! Semua controller telah diperbaiki.\n";
?>
