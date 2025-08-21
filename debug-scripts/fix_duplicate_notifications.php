<?php
/**
 * Script untuk memperbaiki notifikasi duplikat di halaman Usulan
 * Menghapus notifikasi yang duplikat karena sudah ditangani oleh component flash
 */

echo "=== Fix Duplicate Notifications ===\n\n";

// Daftar file yang perlu diperbaiki
$files = [
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-kepangkatan/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-laporan-lkd/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-laporan-serdos/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-nuptk/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-pencantuman-gelar/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-pengaktifan-kembali/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-pensiun/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-presensi/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-satyalancana/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-id-sinta-sister/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-tugas-belajar/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-ujian-dinas-ijazah/index.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/usulan-penyesuaian-masa-kerja/index.blade.php'
];

$pattern = '/{{-- Flash Messages --}}\s*@if \(session\(\'success\'\)\)\s*<div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert">\s*<strong class="font-bold">Sukses!<\/strong>\s*<span class="block sm:inline">{{ session\(\'success\'\) }}<\/span>\s*<\/div>\s*@endif\s*@if \(session\(\'error\'\)\)\s*<div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert">\s*<strong class="font-bold">Gagal!<\/strong>\s*<span class="block sm:inline">{{ session\(\'error\'\) }}<\/span>\s*<\/div>\s*@endif\s*@if \(session\(\'warning\'\)\)\s*<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert">\s*<strong class="font-bold">Perhatian!<\/strong>\s*<span class="block sm:inline">{{ session\(\'warning\'\) }}<\/span>\s*<\/div>\s*@endif/s';

$replacement = '{{-- Notifikasi sudah ditangani oleh component flash di layout base --}}';

$fixedCount = 0;

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalContent = $content;

        // Replace the duplicate notification pattern
        $content = preg_replace($pattern, $replacement, $content);

        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "✅ Fixed: $file\n";
            $fixedCount++;
        } else {
            echo "ℹ️  No changes needed: $file\n";
        }
    } else {
        echo "❌ File not found: $file\n";
    }
}

echo "\n=== Summary ===\n";
echo "Total files processed: " . count($files) . "\n";
echo "Files fixed: $fixedCount\n";
echo "Files unchanged: " . (count($files) - $fixedCount) . "\n";

echo "\n=== What was fixed ===\n";
echo "1. Removed duplicate flash messages from individual view files\n";
echo "2. Added comment indicating notifications are handled by flash component\n";
echo "3. This prevents duplicate notifications from appearing\n";

echo "\n=== Next Steps ===\n";
echo "1. Clear Laravel cache: php artisan view:clear\n";
echo "2. Test the application to ensure notifications work correctly\n";
echo "3. Verify that only one notification appears per message\n";

echo "\n=== Manual Commands ===\n";
echo "php artisan view:clear\n";
echo "php artisan cache:clear\n";
?>
