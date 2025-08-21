<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanLog;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Pegawai;

echo "=== CHECK PEGAWAI AND PERIODE DATA ===\n";

try {
    // Check pegawai data
    echo "\n1. Checking pegawai data...\n";

    $pegawai = Pegawai::first();
    if ($pegawai) {
        echo "Pegawai ID: " . $pegawai->id . "\n";
        echo "Nama: " . $pegawai->nama_lengkap . "\n";
        echo "NIP: " . $pegawai->nip . "\n";
        echo "Jenis Pegawai: " . $pegawai->jenis_pegawai . "\n";
        echo "Status Kepegawaian: " . $pegawai->status_kepegawaian . "\n";
        echo "Jabatan: " . ($pegawai->jabatan ? $pegawai->jabatan->jabatan : 'Tidak ada') . "\n";
        echo "Pangkat: " . ($pegawai->pangkat ? $pegawai->pangkat->pangkat : 'Tidak ada') . "\n";
    } else {
        echo "❌ No pegawai found\n";
    }

    // Check periode data
    echo "\n2. Checking periode data...\n";

    $periodes = PeriodeUsulan::all();
    foreach ($periodes as $periode) {
        echo "Periode ID: " . $periode->id . "\n";
        echo "Nama: " . $periode->nama_periode . "\n";
        echo "Jenis Usulan: " . $periode->jenis_usulan . "\n";
        echo "Status: " . $periode->status . "\n";
        echo "Status Kepegawaian: " . json_encode($periode->status_kepegawaian) . "\n";
        echo "Tanggal Mulai: " . $periode->tanggal_mulai . "\n";
        echo "Tanggal Selesai: " . $periode->tanggal_selesai . "\n";
        echo "---\n";
    }

    // Check what jenis usulan should be for this pegawai
    echo "\n3. Determining correct jenis usulan for pegawai...\n";

    if ($pegawai) {
        $jenisUsulanPeriode = null;

        if ($pegawai->jenis_pegawai === 'Dosen') {
            if ($pegawai->status_kepegawaian === 'Dosen PNS') {
                $jenisUsulanPeriode = 'Usulan Jabatan';
            } elseif ($pegawai->status_kepegawaian === 'Dosen PPPK') {
                $jenisUsulanPeriode = 'Usulan Jabatan';
            } else {
                $jenisUsulanPeriode = 'Usulan Jabatan';
            }
        } elseif ($pegawai->jenis_pegawai === 'Tenaga Kependidikan') {
            if ($pegawai->status_kepegawaian === 'Tenaga Kependidikan PNS') {
                $jenisUsulanPeriode = 'Usulan Jabatan';
            } elseif ($pegawai->status_kepegawaian === 'Tenaga Kependidikan PPPK') {
                $jenisUsulanPeriode = 'Usulan Jabatan';
            } else {
                $jenisUsulanPeriode = 'Usulan Jabatan';
            }
        }

        echo "Expected jenis usulan for this pegawai: " . $jenisUsulanPeriode . "\n";

        // Check if there's a matching periode
        $matchingPeriode = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->first();

        if ($matchingPeriode) {
            echo "✅ Found matching periode: " . $matchingPeriode->nama_periode . " (ID: " . $matchingPeriode->id . ")\n";
        } else {
            echo "❌ No matching periode found\n";

            // Try without JSON contains
            $altPeriode = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->first();

            if ($altPeriode) {
                echo "⚠️ Found periode without status_kepegawaian check: " . $altPeriode->nama_periode . " (ID: " . $altPeriode->id . ")\n";
            }
        }
    }

    // Check jabatan hierarchy
    echo "\n4. Checking jabatan hierarchy...\n";

    if ($pegawai && $pegawai->jabatan) {
        echo "Current jabatan: " . $pegawai->jabatan->jabatan . " (Level: " . $pegawai->jabatan->hierarchy_level . ")\n";

        $nextJabatan = $pegawai->jabatan->getNextLevel();
        if ($nextJabatan) {
            echo "Next jabatan: " . $nextJabatan->jabatan . " (Level: " . $nextJabatan->hierarchy_level . ")\n";

            // Check if next jabatan is Guru Besar
            if (strpos(strtolower($nextJabatan->jabatan), 'guru besar') !== false) {
                echo "⚠️ Next jabatan is Guru Besar - special requirements apply\n";
            }
        } else {
            echo "❌ No next jabatan found\n";
        }
    }

    echo "\n=== CHECK COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
