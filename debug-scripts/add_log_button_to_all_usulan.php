<?php
/**
 * Script untuk menambahkan tombol log ke semua halaman index usulan
 * Jalankan script ini untuk menambahkan fitur log ke semua jenis usulan
 */

$usulanTypes = [
    'usulan-kepangkatan',
    'usulan-id-sinta-sister',
    'usulan-laporan-lkd',
    'usulan-laporan-serdos',
    'usulan-nuptk',
    'usulan-pencantuman-gelar',
    'usulan-pengaktifan-kembali',
    'usulan-pensiun',
    'usulan-penyesuaian-masa-kerja',
    'usulan-presensi',
    'usulan-satyalancana',
    'usulan-tugas-belajar',
    'usulan-ujian-dinas-ijazah'
];

$basePath = 'resources/views/backend/layouts/views/pegawai-unmul/';

echo "🚀 MENAMBAHKAN TOMBOL LOG KE SEMUA USULAN\n";
echo "========================================\n\n";

foreach ($usulanTypes as $usulanType) {
    $filePath = $basePath . $usulanType . '/index.blade.php';

    if (!file_exists($filePath)) {
        echo "❌ File tidak ditemukan: {$filePath}\n";
        continue;
    }

    echo "📝 Memproses: {$usulanType}\n";

    $content = file_get_contents($filePath);

    // 1. Tambahkan tombol Log di bagian aksi
    $oldActionPattern = '/<div class="flex items-center justify-center space-x-2">\s*<a href="\{\{ route\(\'pegawai-unmul\.' . str_replace('-', '-', $usulanType) . '\.show\', \$usulan->id\) \}\}"\s*class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">\s*Detail\s*<\/a>/s';

    $newActionContent = '<div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route(\'pegawai-unmul.' . str_replace('-', '-', $usulanType) . '.show\', $usulan->id) }}"
                                           class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                            Detail
                                        </a>
                                        <button type="button"
                                                data-usulan-id="{{ $usulan->id }}"
                                                onclick="showLogs({{ $usulan->id }})"
                                                class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700">
                                            <i data-lucide="activity" class="w-3 h-3 mr-1"></i>
                                            Log
                                        </button>';

    $content = preg_replace($oldActionPattern, $newActionContent, $content);

    // 2. Tambahkan CSS untuk animasi tombol
    $cssToAdd = '<style>
    /* Custom CSS untuk animasi tombol */
    .btn-animate {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .btn-animate:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-animate:active {
        transform: scale(0.98);
    }

    /* Memastikan hover berfungsi */
    .btn-animate:hover {
        opacity: 0.9;
    }
</style>';

    if (strpos($content, '.btn-animate') === false) {
        $content = str_replace('@section(\'content\')', '@section(\'content\')' . "\n" . $cssToAdd, $content);
    }

    // 3. Tambahkan modal log
    $modalToAdd = '

{{-- Log Modal --}}
<div id="logModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i data-lucide="activity" class="w-5 h-5 mr-2 text-green-600"></i>
                Log Aktivitas Usulan
            </h3>
            <button type="button" onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="logModalContent" class="max-h-96 overflow-y-auto">
            <div class="text-center py-8">
                <i data-lucide="loader" class="w-8 h-8 text-gray-400 mx-auto animate-spin"></i>
                <p class="text-sm text-gray-500 mt-2">Memuat log aktivitas...</p>
            </div>
        </div>
    </div>
</div>';

    if (strpos($content, 'logModal') === false) {
        $content = str_replace('</div>', $modalToAdd . "\n</div>", $content);
    }

    // 4. Tambahkan JavaScript functions
    $jsToAdd = '
<script>
function showLogs(usulanId) {
    const modal = document.getElementById(\'logModal\');
    const content = document.getElementById(\'logModalContent\');

    // Show modal with loading state
    modal.classList.remove(\'hidden\');
    content.innerHTML = `
        <div class="text-center py-8">
            <i data-lucide="loader" class="w-8 h-8 text-gray-400 mx-auto animate-spin"></i>
            <p class="text-sm text-gray-500 mt-2">Memuat log aktivitas...</p>
        </div>
    `;

    // Fetch logs
    fetch(`/pegawai-unmul/' . $usulanType . '/${usulanId}/logs`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logs && data.logs.length > 0) {
                let html = \'<div class="space-y-4">\';
                data.logs.forEach(log => {
                    const statusClass = log.status_badge_class || \'bg-gray-100 text-gray-800 border-gray-300\';
                    const statusIcon = log.status_icon || \'help-circle\';

                    html += `
                        <div class="border-l-4 border-green-400 pl-4 py-3 bg-gray-50 rounded-r-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                            <i data-lucide="${statusIcon}" class="w-3 h-3 mr-1"></i>
                                            ${log.status_baru || log.status_sebelumnya || \'N/A\'}
                                        </span>
                                        <span class="text-xs text-gray-500">${log.formatted_date || log.created_at}</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">${log.action || log.keterangan || \'Aktivitas usulan\'}</p>
                                    ${log.catatan ? `<p class="text-xs text-gray-600">${log.catatan}</p>` : \'\'}
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">${log.user_name || \'System\'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += \'</div>\';
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-sm text-gray-500">Belum ada log aktivitas untuk usulan ini</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error(\'Error loading logs:\', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <i data-lucide="alert-triangle" class="w-12 h-12 text-red-400 mx-auto mb-4"></i>
                    <p class="text-sm text-red-500">Gagal memuat log aktivitas</p>
                    <p class="text-xs text-gray-500 mt-1">Silakan coba lagi</p>
                </div>
            `;
        });
}

function closeLogModal() {
    const modal = document.getElementById(\'logModal\');
    modal.classList.add(\'hidden\');
}

// Close modal when clicking outside
document.addEventListener(\'DOMContentLoaded\', function() {
    const logModal = document.getElementById(\'logModal\');
    if (logModal) {
        logModal.addEventListener(\'click\', function(e) {
            if (e.target === this) {
                closeLogModal();
            }
        });
    }
});
</script>';

    if (strpos($content, 'showLogs') === false) {
        $content = str_replace('@endsection', $jsToAdd . "\n@endsection", $content);
    }

    // 5. Simpan file
    file_put_contents($filePath, $content);

    echo "✅ Berhasil menambahkan tombol log ke: {$usulanType}\n";
}

echo "\n🎉 SELESAI! Semua halaman usulan telah ditambahkan tombol log.\n";
echo "📋 Pastikan route untuk logs sudah tersedia di routes/backend.php\n";
echo "🔧 Jika ada error, periksa route dan controller untuk masing-masing usulan\n";
?>
