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

echo "=== TEST FORM SUBMISSION FIXED ===\n";

try {
    // Authenticate as first pegawai
    echo "\n1. Authenticating as first pegawai...\n";

    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "❌ No pegawai found in database\n";
        exit(1);
    }

    Auth::login($pegawai);
    echo "✅ Authenticated as: " . $pegawai->nama_lengkap . " (ID: " . $pegawai->id . ")\n";
    echo "Current jabatan: " . $pegawai->jabatan->jabatan . "\n";
    echo "Next jabatan: " . $pegawai->jabatan->getNextLevel()->jabatan . "\n";

    // Get CSRF token
    echo "\n2. Getting CSRF token...\n";

    $csrfRequest = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/create', 'GET');
    $csrfResponse = app()->handle($csrfRequest);
    $csrfContent = $csrfResponse->getContent();

    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $csrfContent, $matches);
    $csrfToken = $matches[1] ?? null;

    if (!$csrfToken) {
        echo "❌ CSRF token not found\n";
        exit(1);
    }

    echo "✅ CSRF token found: " . substr($csrfToken, 0, 10) . "...\n";

    // Get correct periode ID
    $periode = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')
        ->where('status', 'Buka')
        ->first();

    if (!$periode) {
        echo "❌ No matching periode found\n";
        exit(1);
    }

    echo "✅ Using periode: " . $periode->nama_periode . " (ID: " . $periode->id . ")\n";

    // Test form submission with correct data for Guru Besar
    echo "\n3. Testing form submission for Guru Besar...\n";

    $formData = [
        '_token' => $csrfToken,
        'action' => 'save_draft',
        'periode_usulan_id' => $periode->id,
        'jenis_jabatan' => 'Guru Besar',
        'alasan_pengajuan' => 'Test pengajuan jabatan Guru Besar',
        'karya_ilmiah' => 'Jurnal Internasional Bereputasi', // Correct for Guru Besar
        'nama_jurnal' => 'International Journal of Test',
        'judul_artikel' => 'Test Article for Professor',
        'penerbit_artikel' => 'International Publisher',
        'volume_artikel' => 'Vol 1',
        'nomor_artikel' => 'No 1',
        'edisi_artikel' => '2025',
        'halaman_artikel' => '1-10',
        'link_artikel' => 'https://example.com/article',
        'link_scopus' => 'https://www.scopus.com/test', // Required for Guru Besar
        'link_sinta' => 'https://sinta.kemdikbud.go.id/test',
        'link_google_scholar' => 'https://scholar.google.com/test',
        'syarat_guru_besar' => 'hibah', // Required for Guru Besar
        'keterangan_syarat_guru_besar' => 'Test keterangan syarat guru besar',
        'catatan' => 'Test catatan untuk Guru Besar'
    ];

    $request = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan', 'POST', $formData);
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    $request->headers->set('X-CSRF-TOKEN', $csrfToken);

    $response = app()->handle($request);
    echo "Form submission status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";

    if ($response->getStatusCode() === 200 || $response->getStatusCode() === 302) {
        echo "✅ Form submission successful!\n";

        // Check if usulan was created
        $latestUsulan = Usulan::where('pegawai_id', $pegawai->id)
            ->where('jenis_usulan', 'Usulan Jabatan')
            ->latest()
            ->first();

        if ($latestUsulan) {
            echo "✅ Usulan created with ID: " . $latestUsulan->id . "\n";
            echo "Status: " . $latestUsulan->status_usulan . "\n";

            // Check usulan log
            $latestLog = UsulanLog::where('usulan_id', $latestUsulan->id)
                ->latest()
                ->first();

            if ($latestLog) {
                echo "✅ Usulan log created with ID: " . $latestLog->id . "\n";
                echo "Status change: " . $latestLog->status_sebelumnya . " -> " . $latestLog->status_baru . "\n";
            }
        }
    } else {
        echo "❌ Form submission failed\n";
    }

    echo "\n=== TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
