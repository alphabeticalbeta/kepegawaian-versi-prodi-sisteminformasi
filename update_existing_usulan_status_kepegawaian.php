<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Memulai update status_kepegawaian untuk usulan yang sudah ada...\n";
echo "==================================================\n\n";

try {
    // Ambil semua usulan yang status_kepegawaian-nya NULL
    $usulans = Usulan::whereNull('status_kepegawaian')->get();

    echo "📊 Ditemukan {$usulans->count()} usulan dengan status_kepegawaian NULL\n\n";

    if ($usulans->count() == 0) {
        echo "✅ Semua usulan sudah memiliki status_kepegawaian yang terisi.\n";
        exit(0);
    }

    $updatedCount = 0;
    $errorCount = 0;

    foreach ($usulans as $usulan) {
        try {
            // Ambil data pegawai
            $pegawai = Pegawai::find($usulan->pegawai_id);

            if (!$pegawai) {
                echo "❌ Usulan ID {$usulan->id}: Pegawai tidak ditemukan (pegawai_id: {$usulan->pegawai_id})\n";
                $errorCount++;
                continue;
            }

            // Update status_kepegawaian
            $usulan->status_kepegawaian = $pegawai->status_kepegawaian;
            $usulan->save();

            echo "✅ Usulan ID {$usulan->id}: Status kepegawaian diupdate dari NULL ke '{$pegawai->status_kepegawaian}'\n";
            $updatedCount++;

        } catch (Exception $e) {
            echo "❌ Usulan ID {$usulan->id}: Error - {$e->getMessage()}\n";
            $errorCount++;
        }
    }

    echo "\n==================================================\n";
    echo "📈 Ringkasan Update:\n";
    echo "   ✅ Berhasil diupdate: {$updatedCount} usulan\n";
    echo "   ❌ Error: {$errorCount} usulan\n";
    echo "   📊 Total diproses: " . ($updatedCount + $errorCount) . " usulan\n";

    if ($errorCount == 0) {
        echo "\n🎉 Semua usulan berhasil diupdate!\n";
    } else {
        echo "\n⚠️  Ada beberapa usulan yang gagal diupdate. Silakan cek log di atas.\n";
    }

} catch (Exception $e) {
    echo "❌ Error umum: {$e->getMessage()}\n";
    exit(1);
}

echo "\n✨ Script selesai!\n";
