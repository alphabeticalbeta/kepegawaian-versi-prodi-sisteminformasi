<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanLog;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

echo "=== TEST FORM SUBMISSION CURRENT STATUS ===\n";

try {
    // Test 1: Check if we can access the form
    echo "\n1. Testing form access...\n";

    $request = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/create', 'GET');
    $request->headers->set('Accept', 'text/html');

    $response = app()->handle($request);
    echo "Form access status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() === 200) {
        echo "✅ Form can be accessed\n";
    } else {
        echo "❌ Form access failed\n";
    }

    // Test 2: Check if we can submit form data
    echo "\n2. Testing form submission...\n";

    $formData = [
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
    $request->headers->set('Content-Type', 'application/json');

    $response = app()->handle($request);
    echo "Form submission status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";

    // Test 3: Check database connection
    echo "\n3. Testing database connection...\n";

    $usulanCount = Usulan::count();
    echo "Current usulan count: " . $usulanCount . "\n";

    $periodeCount = PeriodeUsulan::count();
    echo "Current periode count: " . $periodeCount . "\n";

    // Test 4: Check if we can create usulan directly
    echo "\n4. Testing direct usulan creation...\n";

    $periode = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')->where('status', 'Buka')->first();

    if ($periode) {
        echo "Found active periode: " . $periode->nama_periode . "\n";

        $usulan = new Usulan();
        $usulan->pegawai_id = 1;
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
