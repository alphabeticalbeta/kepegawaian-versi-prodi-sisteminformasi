<?php

/**
 * Test Script untuk Admin Fakultas Detail Usulan
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_admin_fakultas_detail.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

class AdminFakultasDetailTest
{
    public function testUsulanDetailData()
    {
        echo "🔍 TESTING ADMIN FAKULTAS DETAIL USULAN\n";
        echo "=====================================\n\n";

        try {
            // Test 1: Check if user is authenticated
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User terautentikasi: {$user->nama_lengkap}\n";
            echo "   - Role: " . ($user->role ?? 'N/A') . "\n";
            echo "   - Unit Kerja ID: " . ($user->unit_kerja_id ?? 'N/A') . "\n";

            // Test 2: Check if user is admin fakultas
            if ($user->role !== 'Admin Fakultas') {
                echo "❌ User bukan Admin Fakultas\n";
                return;
            }

            echo "✅ User adalah Admin Fakultas\n";

            // Test 3: Find usulans for this admin's faculty
            $usulans = Usulan::query()
                ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($query) use ($user) {
                    $query->where('id', $user->unit_kerja_id);
                })
                ->with([
                    'pegawai:id,nama_lengkap,email,nip,gelar_depan,gelar_belakang,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_terakhir_id,jenis_pegawai,status_kepegawaian',
                    'pegawai.pangkat:id,pangkat',
                    'pegawai.jabatan:id,jabatan',
                    'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                    'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                    'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama',
                    'jabatanLama:id,jabatan',
                    'jabatanTujuan:id,jabatan',
                    'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai,status'
                ])
                ->limit(5)
                ->get();

            if ($usulans->isEmpty()) {
                echo "❌ Tidak ada usulan untuk fakultas ini\n";
                return;
            }

            echo "✅ Ditemukan " . $usulans->count() . " usulan untuk fakultas ini\n\n";

            // Test 4: Check each usulan data
            foreach ($usulans as $index => $usulan) {
                echo "📋 USULAN #" . ($index + 1) . " (ID: {$usulan->id})\n";
                echo "   ======================================\n";
                
                // Check pegawai data
                echo "   👤 PEGAWAI DATA:\n";
                if ($usulan->pegawai) {
                    echo "      - Nama: {$usulan->pegawai->nama_lengkap}\n";
                    echo "      - NIP: {$usulan->pegawai->nip}\n";
                    echo "      - Email: {$usulan->pegawai->email}\n";
                    echo "      - Gelar Depan: " . ($usulan->pegawai->gelar_depan ?? 'N/A') . "\n";
                    echo "      - Gelar Belakang: " . ($usulan->pegawai->gelar_belakang ?? 'N/A') . "\n";
                    echo "      - Jenis Pegawai: {$usulan->pegawai->jenis_pegawai}\n";
                    echo "      - Status Kepegawaian: {$usulan->pegawai->status_kepegawaian}\n";
                    
                    // Check relationships
                    echo "      - Pangkat: " . ($usulan->pegawai->pangkat->pangkat ?? 'N/A') . "\n";
                    echo "      - Jabatan: " . ($usulan->pegawai->jabatan->jabatan ?? 'N/A') . "\n";
                    echo "      - Unit Kerja: " . ($usulan->pegawai->unitKerja->nama ?? 'N/A') . "\n";
                    
                    if ($usulan->pegawai->unitKerja && $usulan->pegawai->unitKerja->subUnitKerja) {
                        echo "      - Sub Unit Kerja: " . ($usulan->pegawai->unitKerja->subUnitKerja->nama ?? 'N/A') . "\n";
                        if ($usulan->pegawai->unitKerja->subUnitKerja->unitKerja) {
                            echo "      - Fakultas: " . ($usulan->pegawai->unitKerja->subUnitKerja->unitKerja->nama ?? 'N/A') . "\n";
                        }
                    }
                } else {
                    echo "      ❌ Data pegawai tidak ditemukan\n";
                }

                // Check usulan data
                echo "   📄 USULAN DATA:\n";
                echo "      - Status: {$usulan->status_usulan}\n";
                echo "      - Jenis: {$usulan->jenis_usulan}\n";
                echo "      - Jabatan Lama: " . ($usulan->jabatanLama->jabatan ?? 'N/A') . "\n";
                echo "      - Jabatan Tujuan: " . ($usulan->jabatanTujuan->jabatan ?? 'N/A') . "\n";
                echo "      - Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n";
                echo "      - Data Usulan: " . (is_array($usulan->data_usulan) ? 'Valid' : 'Invalid') . "\n";

                // Check if data is complete for display
                $missingFields = [];
                if (!$usulan->pegawai->nama_lengkap) $missingFields[] = 'nama_lengkap';
                if (!$usulan->pegawai->nip) $missingFields[] = 'nip';
                if (!$usulan->pegawai->jabatan) $missingFields[] = 'jabatan';
                if (!$usulan->pegawai->unitKerja) $missingFields[] = 'unit_kerja';

                if (empty($missingFields)) {
                    echo "      ✅ Semua data pegawai lengkap untuk ditampilkan\n";
                } else {
                    echo "      ❌ Data pegawai tidak lengkap. Missing: " . implode(', ', $missingFields) . "\n";
                }

                echo "\n";
            }

            // Test 5: Test route generation
            echo "🔗 ROUTE TESTING:\n";
            $testUsulan = $usulans->first();
            $detailRoute = route('admin-fakultas.usulan.show', $testUsulan->id);
            echo "   - Detail Route: {$detailRoute}\n";
            echo "   ✅ Route generated successfully\n";

            // Test 6: Test view data preparation
            echo "\n🎯 VIEW DATA PREPARATION:\n";
            $this->testViewDataPreparation($testUsulan);

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testViewDataPreparation($usulan)
    {
        try {
            // Test validation fields
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan);
            echo "   - Validation Fields: " . (is_array($validationFields) ? count($validationFields) . ' categories' : 'Invalid') . "\n";

            // Test BKD labels
            $bkdLabels = $usulan->getBkdDisplayLabels();
            echo "   - BKD Labels: " . (is_array($bkdLabels) ? count($bkdLabels) . ' labels' : 'Invalid') . "\n";

            // Test existing validation
            $existingValidation = $usulan->getValidasiByRole('admin_fakultas');
            echo "   - Existing Validation: " . (is_array($existingValidation) ? count($existingValidation) . ' items' : 'Invalid') . "\n";

            // Test document data
            $controller = new \App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController();
            $dokumenData = $controller->processDokumenDataForView($usulan);
            echo "   - Document Data: " . (is_array($dokumenData) ? count($dokumenData) . ' documents' : 'Invalid') . "\n";

            echo "   ✅ View data preparation successful\n";

        } catch (\Exception $e) {
            echo "   ❌ View data preparation failed: " . $e->getMessage() . "\n";
        }
    }

    public function testControllerMethod()
    {
        echo "\n🔧 CONTROLLER METHOD TESTING:\n";
        echo "============================\n\n";

        try {
            $controller = new \App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController();
            
            // Test if methods exist
            $methods = ['show', 'saveValidation', 'processValidationFieldsForView', 'processDokumenDataForView'];
            foreach ($methods as $method) {
                echo "   - {$method} method: " . (method_exists($controller, $method) ? 'Exists' : 'Missing') . "\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new AdminFakultasDetailTest();
$test->testUsulanDetailData();
$test->testControllerMethod();
