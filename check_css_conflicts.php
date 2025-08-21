<?php
/**
 * Script untuk mengecek konflik CSS di semua file blade
 */

$bladeFiles = [
    'resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/create-jabatan.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usul-jabatan/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-kepangkatan/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-laporan-lkd/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-laporan-serdos/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-id-sinta-sister/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-nuptk/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-pencantuman-gelar/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-pengaktifan-kembali/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-pensiun/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-penyesuaian-masa-kerja/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-presensi/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-satyalancana/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-tugas-belajar/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-ujian-dinas-ijazah/index.blade.php'
];

echo "🔍 MENGE CEK KONFLIK CSS DI FILE BLADE\n";
echo "=====================================\n\n";

$conflictsFound = false;

foreach ($bladeFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File tidak ditemukan: {$file}\n";
        continue;
    }

    $content = file_get_contents($file);

    // Cek pola yang bisa menyebabkan konflik CSS
    $patterns = [
        '/@if\s*\([^)]+\)\s*([^@]+?)\s*@elseif\s*\([^)]+\)\s*([^@]+?)\s*@elseif\s*\([^)]+\)\s*([^@]+?)\s*@else\s*([^@]+?)\s*@endif/s',
        '/class="[^"]*bg-[a-z]+-100[^"]*bg-[a-z]+-100[^"]*"/',
        '/class="[^"]*text-[a-z]+-700[^"]*text-[a-z]+-700[^"]*"/'
    ];

    $hasConflict = false;
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content)) {
            $hasConflict = true;
            break;
        }
    }

    if ($hasConflict) {
        echo "⚠️  {$file} - Potensi konflik CSS ditemukan\n";
        $conflictsFound = true;
    } else {
        echo "✅ {$file}\n";
    }
}

echo "\n";
if (!$conflictsFound) {
    echo "🎉 SEMUA FILE BLADE BEBAS DARI KONFLIK CSS!\n";
} else {
    echo "⚠️  ADA FILE YANG MEMILIKI POTENSI KONFLIK CSS\n";
    echo "💡 Saran: Gunakan PHP match() atau variabel untuk menghindari konflik\n";
}
?>
