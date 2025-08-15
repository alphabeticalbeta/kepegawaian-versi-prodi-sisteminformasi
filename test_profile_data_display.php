<?php

/**
 * Test Script untuk Verifikasi Data Profil dan Dokumen BKD
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_profile_data_display.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

class ProfileDataDisplayTest
{
    public function testProfileDataCompleteness()
    {
        echo "🔍 TESTING PROFILE DATA COMPLETENESS\n";
        echo "==================================\n\n";

        try {
            // Test 1: Check if user is authenticated
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User terautentikasi: {$user->nama_lengkap}\n";

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

            echo "✅ Menggunakan usulan ID: {$usulan->id}\n\n";

            // Test 3: Check validation fields
            echo "📋 VALIDATION FIELDS TEST:\n";
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan);
            
            foreach ($validationFields as $category => $fields) {
                echo "   📂 {$category}: " . count($fields) . " fields\n";
                
                // Check specific categories
                if ($category === 'data_pribadi') {
                    $this->checkDataPribadi($usulan, $fields);
                } elseif ($category === 'data_kepegawaian') {
                    $this->checkDataKepegawaian($usulan, $fields);
                } elseif ($category === 'data_pendidikan') {
                    $this->checkDataPendidikan($usulan, $fields);
                } elseif ($category === 'data_kinerja') {
                    $this->checkDataKinerja($usulan, $fields);
                } elseif ($category === 'dokumen_profil') {
                    $this->checkDokumenProfil($usulan, $fields);
                } elseif ($category === 'dokumen_bkd') {
                    $this->checkDokumenBkd($usulan, $fields);
                }
            }

            // Test 4: Check BKD labels
            echo "\n🎯 BKD LABELS TEST:\n";
            $bkdLabels = $usulan->getBkdDisplayLabels();
            echo "   - Generated " . count($bkdLabels) . " BKD labels\n";
            foreach ($bkdLabels as $field => $label) {
                echo "     • {$field}: {$label}\n";
            }

            // Test 5: Check field helper
            echo "\n🔧 FIELD HELPER TEST:\n";
            $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
            
            // Test some key fields
            $testFields = [
                'data_pribadi' => ['nama_lengkap', 'nip', 'email'],
                'data_kepegawaian' => ['pangkat_saat_usul', 'jabatan_saat_usul'],
                'dokumen_profil' => ['ijazah_terakhir', 'sk_pangkat_terakhir'],
                'dokumen_bkd' => ['bkd_semester_1', 'bkd_semester_2']
            ];

            foreach ($testFields as $category => $fields) {
                echo "   📂 {$category}:\n";
                foreach ($fields as $field) {
                    $value = $fieldHelper->getFieldValue($category, $field);
                    $status = $value !== '-' ? '✅' : '❌';
                    echo "     {$status} {$field}: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
                }
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function checkDataPribadi($usulan, $fields)
    {
        echo "     👤 Data Pribadi Check:\n";
        $requiredFields = ['jenis_pegawai', 'status_kepegawaian', 'nip', 'nuptk', 'gelar_depan', 'nama_lengkap', 'gelar_belakang', 'email', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'nomor_handphone'];
        
        foreach ($requiredFields as $field) {
            $value = $usulan->pegawai->{$field} ?? null;
            $status = $value ? '✅' : '❌';
            echo "       {$status} {$field}: " . ($value ?: 'MISSING') . "\n";
        }
    }

    private function checkDataKepegawaian($usulan, $fields)
    {
        echo "     💼 Data Kepegawaian Check:\n";
        $requiredFields = ['pangkat_saat_usul', 'tmt_pangkat', 'jabatan_saat_usul', 'tmt_jabatan', 'tmt_cpns', 'tmt_pns', 'unit_kerja_saat_usul'];
        
        foreach ($requiredFields as $field) {
            if ($field === 'pangkat_saat_usul') {
                $value = $usulan->pegawai->pangkat->pangkat ?? null;
            } elseif ($field === 'jabatan_saat_usul') {
                $value = $usulan->pegawai->jabatan->jabatan ?? null;
            } elseif ($field === 'unit_kerja_saat_usul') {
                $value = $usulan->pegawai->unitKerja->nama ?? null;
            } else {
                $value = $usulan->pegawai->{$field} ?? null;
            }
            
            $status = $value ? '✅' : '❌';
            echo "       {$status} {$field}: " . ($value ?: 'MISSING') . "\n";
        }
    }

    private function checkDataPendidikan($usulan, $fields)
    {
        echo "     🎓 Data Pendidikan Check:\n";
        $requiredFields = ['pendidikan_terakhir', 'mata_kuliah_diampu', 'ranting_ilmu_kepakaran', 'url_profil_sinta'];
        
        foreach ($requiredFields as $field) {
            $value = $usulan->pegawai->{$field} ?? null;
            $status = $value ? '✅' : '❌';
            echo "       {$status} {$field}: " . ($value ?: 'MISSING') . "\n";
        }
    }

    private function checkDataKinerja($usulan, $fields)
    {
        echo "     📊 Data Kinerja Check:\n";
        $requiredFields = ['predikat_kinerja_tahun_pertama', 'predikat_kinerja_tahun_kedua', 'nilai_konversi'];
        
        foreach ($requiredFields as $field) {
            $value = $usulan->pegawai->{$field} ?? null;
            $status = $value ? '✅' : '❌';
            echo "       {$status} {$field}: " . ($value ?: 'MISSING') . "\n";
        }
    }

    private function checkDokumenProfil($usulan, $fields)
    {
        echo "     📄 Dokumen Profil Check:\n";
        $requiredFields = ['ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua', 'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir'];
        
        foreach ($requiredFields as $field) {
            $value = $usulan->pegawai->{$field} ?? null;
            $status = $value ? '✅' : '❌';
            echo "       {$status} {$field}: " . ($value ? 'UPLOADED' : 'MISSING') . "\n";
        }
    }

    private function checkDokumenBkd($usulan, $fields)
    {
        echo "     📚 Dokumen BKD Check:\n";
        echo "       - Expected fields: " . count($fields) . "\n";
        
        foreach ($fields as $field) {
            $docPath = $usulan->getDocumentPath($field);
            $status = $docPath ? '✅' : '❌';
            echo "       {$status} {$field}: " . ($docPath ? 'UPLOADED' : 'MISSING') . "\n";
        }
    }

    public function testViewDataPreparation()
    {
        echo "\n🎯 VIEW DATA PREPARATION TEST:\n";
        echo "============================\n\n";

        try {
            $usulan = Usulan::with(['pegawai', 'periodeUsulan'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            // Test validation fields generation
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan);
            echo "✅ Validation fields generated: " . count($validationFields) . " categories\n";

            // Test BKD labels generation
            $bkdLabels = $usulan->getBkdDisplayLabels();
            echo "✅ BKD labels generated: " . count($bkdLabels) . " labels\n";

            // Test field helper
            $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
            echo "✅ Field helper initialized\n";

            // Test some field values
            $testValue = $fieldHelper->getFieldValue('data_pribadi', 'nama_lengkap');
            echo "✅ Sample field value: " . ($testValue ?: 'EMPTY') . "\n";

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new ProfileDataDisplayTest();
$test->testProfileDataCompleteness();
$test->testViewDataPreparation();
