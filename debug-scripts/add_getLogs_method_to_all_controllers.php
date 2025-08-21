<?php
/**
 * Script untuk menambahkan method getLogs ke semua controller usulan
 * Jalankan script ini untuk menambahkan method getLogs ke semua controller
 */

$usulanControllers = [
    'UsulanKepangkatanController',
    'UsulanIdSintaSisterController',
    'UsulanLaporanLkdController',
    'UsulanLaporanSerdosController',
    'UsulanNuptkController',
    'UsulanPencantumanGelarController',
    'UsulanPengaktifanKembaliController',
    'UsulanPensiunController',
    'UsulanPenyesuaianMasaKerjaController',
    'UsulanPresensiController',
    'UsulanSatyalancanaController',
    'UsulanTugasBelajarController',
    'UsulanUjianDinasIjazahController'
];

$basePath = 'app/Http/Controllers/Backend/PegawaiUnmul/';

echo "🚀 MENAMBAHKAN METHOD GETLOGS KE SEMUA CONTROLLER USULAN\n";
echo "====================================================\n\n";

foreach ($usulanControllers as $controller) {
    $filePath = $basePath . $controller . '.php';

    if (!file_exists($filePath)) {
        echo "❌ File tidak ditemukan: {$filePath}\n";
        continue;
    }

    echo "📝 Memproses: {$controller}\n";

    $content = file_get_contents($filePath);

    // Cek apakah method getLogs sudah ada
    if (strpos($content, 'public function getLogs') !== false) {
        echo "⚠️  Method getLogs sudah ada di: {$controller}\n";
        continue;
    }

    // Cek apakah import UsulanLog sudah ada
    if (strpos($content, 'use App\\Models\\BackendUnivUsulan\\UsulanLog;') === false) {
        // Tambahkan import UsulanLog
        $importPattern = 'use App\\Models\\BackendUnivUsulan\\Usulan;';
        $importToAdd = 'use App\\Models\\BackendUnivUsulan\\UsulanLog;';

        if (strpos($content, $importPattern) !== false) {
            $content = str_replace($importPattern, $importPattern . "\n" . $importToAdd, $content);
        } else {
            // Jika tidak ada import Usulan, tambahkan di bagian atas
            $namespacePattern = 'namespace App\\Http\\Controllers\\Backend\\PegawaiUnmul;';
            $content = str_replace($namespacePattern, $namespacePattern . "\n\n" . $importToAdd, $content);
        }
    }

    // Tambahkan method getLogs di akhir class sebelum penutup
    $methodToAdd = '
    /**
     * Get logs for a specific usulan
     */
    public function getLogs(Usulan $usulan)
    {
        // Authorization check
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, \'AKSES DITOLAK: Anda tidak memiliki akses untuk melihat log usulan ini.\');
        }

        try {
            // Get logs for this usulan, ordered by latest first
            $logs = UsulanLog::where(\'usulan_id\', $usulan->id)
                ->with([\'dilakukanOleh\'])
                ->orderBy(\'created_at\', \'desc\')
                ->get()
                ->map(function ($log) {
                    return [
                        \'id\' => $log->id,
                        \'action\' => $log->getActionDescription(),
                        \'status_sebelumnya\' => $log->status_sebelumnya,
                        \'status_baru\' => $log->status_baru,
                        \'catatan\' => $log->catatan,
                        \'user_name\' => $log->user_name,
                        \'created_at\' => $log->formatted_date,
                        \'relative_time\' => $log->relative_time,
                        \'status_badge_class\' => $log->status_badge_class,
                        \'status_icon\' => $log->status_icon,
                        \'is_status_change\' => $log->isStatusChange(),
                        \'is_initial_log\' => $log->isInitialLog(),
                    ];
                });

            Log::info(\'Logs retrieved successfully\', [
                \'usulan_id\' => $usulan->id,
                \'total_logs\' => $logs->count(),
                \'user_id\' => Auth::id()
            ]);

            return response()->json([
                \'success\' => true,
                \'logs\' => $logs,
                \'total\' => $logs->count()
            ]);

        } catch (\\Throwable $e) {
            Log::error(\'Failed to retrieve logs\', [
                \'usulan_id\' => $usulan->id,
                \'user_id\' => Auth::id(),
                \'error\' => $e->getMessage()
            ]);

            return response()->json([
                \'success\' => false,
                \'message\' => \'Gagal memuat log aktivitas\',
                \'error\' => $e->getMessage()
            ], 500);
        }
    }';

    // Cari posisi untuk menambahkan method (sebelum penutup class)
    $insertPosition = strrpos($content, '}');
    if ($insertPosition !== false) {
        $content = substr($content, 0, $insertPosition) . $methodToAdd . "\n" . substr($content, $insertPosition);
        file_put_contents($filePath, $content);
        echo "✅ Berhasil menambahkan method getLogs ke: {$controller}\n";
    } else {
        echo "❌ Tidak dapat menemukan posisi untuk menambahkan method di: {$controller}\n";
    }
}

echo "\n🎉 SELESAI! Semua controller usulan telah ditambahkan method getLogs.\n";
echo "📋 Pastikan semua controller extend BaseUsulanController\n";
echo "🔧 Jika ada error, periksa namespace dan import di masing-masing controller\n";
?>
