<?php
/**
 * Script untuk memverifikasi koneksi antara tabel pegawais dan periode_usulans
 * Jalankan script ini untuk memastikan data periode usulan dapat ditemukan berdasarkan status kepegawaian pegawai
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🔍 VERIFIKASI KONEKSI PEGAWAI - PERIODE USULAN\n";
echo "==============================================\n\n";

// 1. Cek data pegawai
echo "1. MENGECEK DATA PEGAWAI:\n";
echo "-------------------------\n";

$pegawais = Pegawai::whereIn('status_kepegawaian', ['Dosen PNS', 'Tenaga Kependidikan PNS'])->get();

if ($pegawais->count() == 0) {
    echo "❌ Tidak ada pegawai dengan status kepegawaian yang sesuai!\n";
    echo "   Status yang dicari: Dosen PNS, Tenaga Kependidikan PNS\n\n";
} else {
    echo "✅ Ditemukan " . $pegawais->count() . " pegawai:\n";
    foreach ($pegawais as $pegawai) {
        echo "   - ID: {$pegawai->id}, NIP: {$pegawai->nip}, Status: {$pegawai->status_kepegawaian}\n";
    }
    echo "\n";
}

// 2. Cek data periode usulan
echo "2. MENGECEK DATA PERIODE USULAN:\n";
echo "--------------------------------\n";

$periodeUsulans = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')->get();

if ($periodeUsulans->count() == 0) {
    echo "❌ Tidak ada periode usulan dengan jenis 'Usulan Jabatan'!\n\n";
} else {
    echo "✅ Ditemukan " . $periodeUsulans->count() . " periode usulan:\n";
    foreach ($periodeUsulans as $periode) {
        echo "   - ID: {$periode->id}, Nama: {$periode->nama_periode}\n";
        echo "     Status: {$periode->status}, Status Kepegawaian: {$periode->status_kepegawaian}\n";
        echo "     Tanggal: {$periode->tanggal_mulai} - {$periode->tanggal_selesai}\n\n";
    }
}

// 3. Test query untuk setiap pegawai
echo "3. TEST QUERY UNTUK SETIAP PEGAWAI:\n";
echo "-----------------------------------\n";

foreach ($pegawais as $pegawai) {
    echo "Pegawai: {$pegawai->nip} ({$pegawai->status_kepegawaian})\n";

    // Query seperti di controller
    $periodeUsulans = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')
        ->where('status', 'Buka')
        ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
        ->orderBy('tanggal_mulai', 'desc')
        ->get();

    if ($periodeUsulans->count() == 0) {
        echo "   ❌ Tidak ada periode usulan yang cocok\n";

        // Coba query alternatif
        $altPeriodeUsulans = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')
            ->where('status', 'Buka')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        if ($altPeriodeUsulans->count() > 0) {
            echo "   ⚠️  Tapi ada periode usulan tanpa filter status kepegawaian:\n";
            foreach ($altPeriodeUsulans as $periode) {
                echo "      - {$periode->nama_periode} (Status: {$periode->status_kepegawaian})\n";
            }
        }
    } else {
        echo "   ✅ Ditemukan " . $periodeUsulans->count() . " periode usulan:\n";
        foreach ($periodeUsulans as $periode) {
            echo "      - {$periode->nama_periode}\n";
        }
    }
    echo "\n";
}

// 4. Cek struktur JSON status_kepegawaian
echo "4. CEK STRUKTUR JSON STATUS KEPEGAWAIAN:\n";
echo "----------------------------------------\n";

$periodeUsulans = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')->get();

foreach ($periodeUsulans as $periode) {
    echo "Periode: {$periode->nama_periode}\n";
    echo "   Raw status_kepegawaian: {$periode->status_kepegawaian}\n";

    // Decode JSON
    $decoded = json_decode($periode->status_kepegawaian, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "   Decoded JSON: " . implode(', ', $decoded) . "\n";

        // Test JSON_CONTAINS
        foreach (['Dosen PNS', 'Tenaga Kependidikan PNS'] as $status) {
            $contains = DB::select("SELECT JSON_CONTAINS(?, ?) as contains_result",
                [$periode->status_kepegawaian, json_encode($status)]);
            echo "   Contains '{$status}': " . ($contains[0]->contains_result ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "   ❌ Error decoding JSON: " . json_last_error_msg() . "\n";
    }
    echo "\n";
}

// 5. Rekomendasi
echo "5. REKOMENDASI:\n";
echo "---------------\n";

$totalPegawai = $pegawais->count();
$totalPeriode = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')->count();

if ($totalPegawai == 0) {
    echo "❌ Tambahkan data pegawai dengan status kepegawaian 'Dosen PNS' atau 'Tenaga Kependidikan PNS'\n";
}

if ($totalPeriode == 0) {
    echo "❌ Tambahkan data periode usulan dengan jenis_usulan = 'Usulan Jabatan'\n";
}

if ($totalPegawai > 0 && $totalPeriode > 0) {
    echo "✅ Data pegawai dan periode usulan tersedia\n";
    echo "✅ Pastikan status_kepegawaian di periode_usulans sesuai dengan pegawai\n";
    echo "✅ Pastikan status periode usulan = 'Buka'\n";
}

echo "\n🎯 SCRIPT SELESAI!\n";
?>
