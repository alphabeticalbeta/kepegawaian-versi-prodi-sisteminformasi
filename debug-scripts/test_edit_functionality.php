<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__ . '/..')
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanLog;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

try {
    echo "=== TESTING EDIT FUNCTIONALITY ===\n\n";

    // Authenticate as first pegawai
    echo "1. Authenticating as first pegawai...\n";

    $pegawai = Pegawai::first();
    if (!$pegawai) {
        echo "❌ No pegawai found in database\n";
        exit(1);
    }

    Auth::login($pegawai);
    echo "✅ Authenticated as: " . $pegawai->nama_lengkap . " (ID: " . $pegawai->id . ")\n";

    // Check if there's an existing usulan to edit
    echo "\n2. Checking for existing usulan...\n";

    $existingUsulan = Usulan::where('pegawai_id', $pegawai->id)
        ->where('jenis_usulan', 'Usulan Jabatan')
        ->first();

    if (!$existingUsulan) {
        echo "❌ No existing usulan found. Creating one first...\n";

        // Create a test usulan first
        $periode = PeriodeUsulan::where('jenis_usulan', 'Usulan Jabatan')
            ->where('status', 'Buka')
            ->first();

        if (!$periode) {
            echo "❌ No active periode found\n";
            exit(1);
        }

        $usulan = new Usulan();
        $usulan->pegawai_id = $pegawai->id;
        $usulan->periode_usulan_id = $periode->id;
        $usulan->jenis_usulan = 'Usulan Jabatan';
        $usulan->status_usulan = 'Draft';
        $usulan->data_usulan = [
            'metadata' => [
                'created_at_snapshot' => now()->toISOString(),
                'version' => '1.0',
                'submission_type' => 'Draft',
            ],
            'pegawai_snapshot' => [
                'nama_lengkap' => $pegawai->nama_lengkap,
                'nip' => $pegawai->nip,
            ],
            'karya_ilmiah' => [],
            'dokumen_usulan' => [],
            'syarat_khusus' => [],
            'catatan_pengusul' => 'Test usulan untuk edit',
        ];
        $usulan->save();

        echo "✅ Created test usulan with ID: " . $usulan->id . "\n";
        $existingUsulan = $usulan;
    } else {
        echo "✅ Found existing usulan with ID: " . $existingUsulan->id . "\n";
        echo "Status: " . $existingUsulan->status_usulan . "\n";
    }

    // Test edit page access
    echo "\n3. Testing edit page access...\n";

    $editRequest = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/' . $existingUsulan->id . '/edit', 'GET');
    $editResponse = app()->handle($editRequest);

    echo "Edit page status: " . $editResponse->getStatusCode() . "\n";

    if ($editResponse->getStatusCode() === 200) {
        echo "✅ Edit page accessible\n";
    } else {
        echo "❌ Edit page not accessible\n";
        echo "Response content: " . $editResponse->getContent() . "\n";
    }

    // Test update functionality with complete data for Guru Besar
    echo "\n4. Testing update functionality with complete data...\n";

    // Get CSRF token from edit page
    $editContent = $editResponse->getContent();
    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $editContent, $matches);
    $csrfToken = $matches[1] ?? null;

    if (!$csrfToken) {
        echo "❌ CSRF token not found\n";
        exit(1);
    }

    echo "✅ CSRF token found: " . substr($csrfToken, 0, 10) . "...\n";

    // Prepare complete update data for Guru Besar
    $updateData = [
        '_token' => $csrfToken,
        '_method' => 'PUT',
        'periode_usulan_id' => $existingUsulan->periode_usulan_id,
        'action' => 'save_draft',
        'catatan' => 'Updated catatan dari test script - ' . now()->format('Y-m-d H:i:s'),

        // Karya ilmiah data untuk Guru Besar
        'karya_ilmiah' => 'Jurnal Internasional Bereputasi',
        'nama_jurnal' => 'Test Journal International',
        'judul_artikel' => 'Test Article Title',
        'penerbit_artikel' => 'Test Publisher',
        'volume_artikel' => 'Vol 1',
        'nomor_artikel' => 'No 1',
        'edisi_artikel' => 'Ed 1',
        'halaman_artikel' => '1-10',
        'link_artikel' => 'https://test-journal.com/article',
        'link_scopus' => 'https://www.scopus.com/test',
        'link_sinta' => 'https://sinta.kemdikbud.go.id/test',
        'link_scimago' => 'https://www.scimagojr.com/test',
        'link_wos' => 'https://www.webofscience.com/test',

        // Syarat khusus Guru Besar
        'syarat_guru_besar' => 'hibah',
    ];

    $updateRequest = \Illuminate\Http\Request::create('/pegawai-unmul/usulan-jabatan/' . $existingUsulan->id, 'PUT', $updateData);
    $updateRequest->headers->set('Accept', 'application/json');
    $updateRequest->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    $updateRequest->headers->set('X-CSRF-TOKEN', $csrfToken);

    $updateResponse = app()->handle($updateRequest);
    echo "Update status: " . $updateResponse->getStatusCode() . "\n";

    if ($updateResponse->getStatusCode() === 200 || $updateResponse->getStatusCode() === 302) {
        echo "✅ Update successful!\n";

        // Check if usulan was updated
        $updatedUsulan = Usulan::find($existingUsulan->id);
        if ($updatedUsulan) {
            echo "✅ Usulan updated successfully\n";
            echo "New catatan: " . ($updatedUsulan->data_usulan['catatan_pengusul'] ?? 'N/A') . "\n";

            // Check usulan log
            $latestLog = UsulanLog::where('usulan_id', $updatedUsulan->id)
                ->latest()
                ->first();

            if ($latestLog) {
                echo "✅ Usulan log updated with ID: " . $latestLog->id . "\n";
                echo "Log status: " . $latestLog->status_baru . "\n";
            } else {
                echo "⚠️ No usulan log found\n";
            }
        } else {
            echo "❌ Usulan not found after update\n";
        }
    } else {
        echo "❌ Update failed\n";
        echo "Response content: " . $updateResponse->getContent() . "\n";
    }

    echo "\n=== TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
