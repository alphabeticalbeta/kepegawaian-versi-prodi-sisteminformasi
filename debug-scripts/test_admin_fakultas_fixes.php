<?php

/**
 * Test Script untuk Verifikasi Perbaikan Admin Fakultas
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_admin_fakultas_fixes.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

class AdminFakultasFixesTest
{
    public function testAllFixes()
    {
        echo "🔍 TESTING ADMIN FAKULTAS FIXES\n";
        echo "==============================\n\n";

        try {
            // Test 1: Check if user is authenticated
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User terautentikasi: {$user->nama_lengkap}\n";
            echo "   - Role: " . ($user->role ?? 'N/A') . "\n\n";

            // Test 2: Find a usulan to test
            $usulan = Usulan::with([
                'pegawai:id,nama_lengkap,email,nip,gelar_depan,gelar_belakang,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_terakhir_id,jenis_pegawai,status_kepegawaian,nuptk,tempat_lahir,tanggal_lahir,jenis_kelamin,nomor_handphone,nomor_kartu_pegawai,tmt_pangkat,tmt_jabatan,tmt_cpns,tmt_pns,pendidikan_terakhir,mata_kuliah_diampu,ranting_ilmu_kepakaran,url_profil_sinta,predikat_kinerja_tahun_pertama,predikat_kinerja_tahun_kedua,nilai_konversi,ijazah_terakhir,transkrip_nilai_terakhir,sk_pangkat_terakhir,sk_jabatan_terakhir,skp_tahun_pertama,skp_tahun_kedua,pak_konversi,sk_cpns,sk_pns,sk_penyetaraan_ijazah,disertasi_thesis_terakhir',
                'pegawai.pangkat:id,pangkat',
                'pegawai.jabatan:id,jabatan',
                'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama',
                'jabatanLama:id,jabatan',
                'jabatanTujuan:id,jabatan',
                'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai,status'
            ])->first();

            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            echo "✅ Menggunakan usulan ID: {$usulan->id}\n";
            echo "   - Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n\n";

            // Test 3: Test BKD Generation and Display
            echo "📚 BKD GENERATION & DISPLAY TEST:\n";
            $this->testBkdGeneration($usulan);

            // Test 4: Test Validation Fields for Admin Fakultas
            echo "\n🎯 VALIDATION FIELDS TEST (Admin Fakultas):\n";
            $this->testValidationFields($usulan);

            // Test 5: Test Document Path Resolution
            echo "\n📄 DOCUMENT PATH RESOLUTION TEST:\n";
            $this->testDocumentPathResolution($usulan);

            // Test 6: Test Dokumen Pendukung
            echo "\n📋 DOKUMEN PENDUKUNG TEST:\n";
            $this->testDokumenPendukung($usulan);

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testBkdGeneration($usulan)
    {
        // Test BKD field names generation
        $bkdFields = $usulan->generateBkdFieldNames();
        echo "   - Generated " . count($bkdFields) . " BKD fields\n";
        foreach ($bkdFields as $field) {
            echo "     • {$field}\n";
        }

        // Test BKD labels
        $bkdLabels = $usulan->getBkdDisplayLabels();
        echo "   - Generated " . count($bkdLabels) . " BKD labels\n";
        
        $foundGenap2024 = false;
        foreach ($bkdLabels as $field => $label) {
            $status = (strpos($label, 'Genap 2024/2025') !== false) ? '🎯 FOUND' : '📋';
            echo "     {$status} {$field}: {$label}\n";
            
            if (strpos($label, 'Genap 2024/2025') !== false) {
                $foundGenap2024 = true;
            }
        }

        if ($foundGenap2024) {
            echo "   ✅ BKD Semester Genap 2024/2025 ditemukan dalam labels\n";
        } else {
            echo "   ❌ BKD Semester Genap 2024/2025 TIDAK ditemukan dalam labels\n";
        }
    }

    private function testValidationFields($usulan)
    {
        // Test validation fields for admin fakultas
        $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_fakultas');
        
        echo "   - Total categories: " . count($validationFields) . "\n";
        
        $foundDokumenPendukung = false;
        foreach ($validationFields as $category => $fields) {
            $status = ($category === 'dokumen_pendukung') ? '🎯 FOUND' : '📋';
            echo "     {$status} {$category}: " . count($fields) . " fields\n";
            
            if ($category === 'dokumen_pendukung') {
                $foundDokumenPendukung = true;
                foreach ($fields as $field) {
                    echo "       • {$field}\n";
                }
            }
        }

        if ($foundDokumenPendukung) {
            echo "   ✅ Dokumen Pendukung ditemukan dalam validation fields\n";
        } else {
            echo "   ❌ Dokumen Pendukung TIDAK ditemukan dalam validation fields\n";
        }

        // Test validation fields for other roles (should not have dokumen_pendukung)
        $validationFieldsOther = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_universitas');
        $hasDokumenPendukungOther = isset($validationFieldsOther['dokumen_pendukung']);
        
        if (!$hasDokumenPendukungOther) {
            echo "   ✅ Dokumen Pendukung TIDAK muncul untuk role lain (correct)\n";
        } else {
            echo "   ❌ Dokumen Pendukung muncul untuk role lain (incorrect)\n";
        }
    }

    private function testDocumentPathResolution($usulan)
    {
        $bkdLabels = $usulan->getBkdDisplayLabels();
        
        foreach ($bkdLabels as $field => $label) {
            if (strpos($label, 'Genap 2024/2025') !== false) {
                echo "   🎯 Testing BKD Genap 2024/2025 document path:\n";
                echo "     - Field: {$field}\n";
                echo "     - Label: {$label}\n";
                
                // Test enhanced getDocumentPath
                $docPath = $usulan->getDocumentPath($field);
                $status = $docPath ? '✅ FOUND' : '❌ NOT FOUND';
                echo "     {$status} Document Path: " . ($docPath ?: 'NULL') . "\n";
                
                // Test legacy key mapping
                $legacyKey = 'bkd_genap_2024_2025';
                $legacyPath = $usulan->getDocumentPath($legacyKey);
                $legacyStatus = $legacyPath ? '✅ FOUND' : '❌ NOT FOUND';
                echo "     {$legacyStatus} Legacy Key ({$legacyKey}): " . ($legacyPath ?: 'NULL') . "\n";
                
                break;
            }
        }
    }

    private function testDokumenPendukung($usulan)
    {
        // Test dokumen pendukung fields
        $pendukungFields = [
            'nomor_surat_usulan', 'file_surat_usulan',
            'nomor_berita_senat', 'file_berita_senat'
        ];

        echo "   - Testing " . count($pendukungFields) . " dokumen pendukung fields\n";
        
        foreach ($pendukungFields as $field) {
            if (str_starts_with($field, 'file_')) {
                // Test file fields
                $validasiData = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                $pathKey = $field . '_path';
                $path = $validasiData[$pathKey] ?? $validasiData[$field] ?? null;
                
                $status = $path ? '✅ EXISTS' : '❌ MISSING';
                echo "     {$status} {$field}: " . ($path ?: 'NULL') . "\n";
            } else {
                // Test text fields
                $value = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'][$field] ?? null;
                $status = $value ? '✅ FILLED' : '❌ EMPTY';
                echo "     {$status} {$field}: " . ($value ?: 'NULL') . "\n";
            }
        }

        // Test field helper for dokumen pendukung
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
        echo "   - Testing field helper for dokumen pendukung:\n";
        
        foreach ($pendukungFields as $field) {
            $value = $fieldHelper->getFieldValue('dokumen_pendukung', $field);
            $status = (strpos($value, 'Lihat Dokumen') !== false || (trim($value) !== '' && $value !== '-')) ? '✅ OK' : '❌ MISSING';
            echo "     {$status} {$field}: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
        }
    }

    public function testControllerMethods()
    {
        echo "\n🔧 CONTROLLER METHODS TEST:\n";
        echo "==========================\n\n";

        try {
            $controller = new \App\Http\Controllers\Backend\AdminFakultas\AdminFakultasController();
            
            // Test if methods exist
            $methods = [
                'show', 'saveValidation', 'showUsulanDocument', 
                'showPegawaiDocument', 'showDokumenPendukung',
                'processDokumenDataForView', 'processDokumenPendukung'
            ];
            
            foreach ($methods as $method) {
                $status = method_exists($controller, $method) ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} {$method} method\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    public function testRoutes()
    {
        echo "\n🔗 ROUTES TEST:\n";
        echo "==============\n\n";

        try {
            $usulan = Usulan::first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk test routes\n";
                return;
            }

            // Test route generation
            $routes = [
                'admin-fakultas.usulan.show' => route('admin-fakultas.usulan.show', $usulan->id),
                'admin-fakultas.usulan.save-validation' => route('admin-fakultas.usulan.save-validation', $usulan->id),
                'admin-fakultas.usulan.show-document' => route('admin-fakultas.usulan.show-document', [$usulan->id, 'bkd_semester_1']),
                'admin-fakultas.usulan.show-dokumen-pendukung' => route('admin-fakultas.usulan.show-dokumen-pendukung', [$usulan->id, 'file_surat_usulan'])
            ];

            foreach ($routes as $name => $url) {
                $status = $url ? '✅ OK' : '❌ FAILED';
                echo "   {$status} {$name}: " . ($url ?: 'NULL') . "\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new AdminFakultasFixesTest();
$test->testAllFixes();
$test->testControllerMethods();
$test->testRoutes();
