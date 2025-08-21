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

echo "=== TEST FORM SUBMISSION WITH AUTHENTICATION ===\n";

try {
    // Test 1: Authenticate as first pegawai
    echo "\n1. Authenticating as first pegawai...\n";

    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "❌ No pegawai found in database\n";
        exit(1);
    }

    Auth::login($pegawai);
    echo "✅ Authenticated as: " . $pegawai->nama_lengkap . " (ID: " . $pegawai->id . ")\n";

    // Test 2: Check if we can access the form now
    echo "\n2. Testing form access with authentication...\n";

    $request = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/create', 'GET');
    $request->headers->set('Accept', 'text/html');

    $response = app()->handle($request);
    echo "Form access status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() === 200) {
        echo "✅ Form can be accessed with authentication\n";
    } else {
        echo "❌ Form access still failed\n";
        echo "Response content: " . substr($response->getContent(), 0, 500) . "...\n";
    }

    // Test 3: Check if we can submit form data with CSRF token
    echo "\n3. Testing form submission with CSRF token...\n";

    // First get CSRF token
    $csrfRequest = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/create', 'GET');
    $csrfResponse = app()->handle($csrfRequest);
    $csrfContent = $csrfResponse->getContent();

    // Extract CSRF token from response
    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $csrfContent, $matches);
    $csrfToken = $matches[1] ?? null;

    if ($csrfToken) {
        echo "✅ CSRF token found: " . substr($csrfToken, 0, 10) . "...\n";

        $formData = [
            '_token' => $csrfToken,
            'action' => 'save_draft',
            'periode_usulan_id' => 1,
            'jenis_jabatan' => 'Lektor',
            'alasan_pengajuan' => 'Test pengajuan jabatan',
            'karya_ilmiah' => 'Artikel Jurnal Nasional',
            'nama_jurnal' => 'Jurnal Test',
            'judul_artikel' => 'Artikel Test',
            'penerbit_artikel' => 'Penerbit Test',
            'volume_artikel' => 'Vol 1',
            'nomor_artikel' => 'No 1',
            'edisi_artikel' => '2025',
            'halaman_artikel' => '1-10',
            'catatan' => 'Test catatan'
        ];

        $request = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan', 'POST', $formData);
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
        $request->headers->set('X-CSRF-TOKEN', $csrfToken);

        $response = app()->handle($request);
        echo "Form submission status: " . $response->getStatusCode() . "\n";
        echo "Response content: " . $response->getContent() . "\n";

    } else {
        echo "❌ CSRF token not found\n";
    }

    // Test 4: Check database status
    echo "\n4. Checking database status...\n";

    $usulanCount = Usulan::count();
    echo "Current usulan count: " . $usulanCount . "\n";

    $periodeCount = PeriodeUsulan::count();
    echo "Current periode count: " . $periodeCount . "\n";

    // Test 5: Check if we can create usulan directly
    echo "\n5. Testing direct usulan creation...\n";

    $periode = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')->where('status', 'Buka')->first();

    if ($periode) {
        echo "Found active periode: " . $periode->nama_periode . "\n";

        $usulan = new Usulan();
        $usulan->pegawai_id = $pegawai->id;
        $usulan->periode_usulan_id = $periode->id;
        $usulan->jenis_usulan = 'Usulan Jabatan';
        $usulan->status_usulan = 'Draft';
        $usulan->data_usulan = ['test' => 'data'];
        $usulan->save();

        echo "✅ Direct usulan creation successful. ID: " . $usulan->id . "\n";

        // Clean up
        $usulan->delete();
        echo "Test usulan cleaned up\n";

    } else {
        echo "❌ No active periode found\n";
    }

    echo "\n=== TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
