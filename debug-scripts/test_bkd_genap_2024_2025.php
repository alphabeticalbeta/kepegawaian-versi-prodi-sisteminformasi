<?php

/**
 * Test Script untuk Verifikasi BKD Semester Genap 2024/2025
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_bkd_genap_2024_2025.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Carbon\Carbon;

class BkdGenap20242025Test
{
    public function testBkdGeneration()
    {
        echo "🔍 TESTING BKD SEMESTER GENAP 2024/2025\n";
        echo "========================================\n\n";

        try {
            // Test 1: Check if user is authenticated
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User terautentikasi: {$user->nama_lengkap}\n\n";

            // Test 2: Find a usulan with periode
            $usulan = Usulan::with(['periodeUsulan'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            echo "✅ Menggunakan usulan ID: {$usulan->id}\n";
            echo "   - Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n";
            echo "   - Tanggal Mulai: " . ($usulan->periodeUsulan->tanggal_mulai ?? 'N/A') . "\n\n";

            // Test 3: Test BKD generation logic
            echo "📅 BKD GENERATION LOGIC TEST:\n";
            $this->testBkdGenerationLogic($usulan->periodeUsulan);

            // Test 4: Test BKD labels
            echo "\n🎯 BKD LABELS TEST:\n";
            $this->testBkdLabels($usulan);

            // Test 5: Test BKD document paths
            echo "\n📄 BKD DOCUMENT PATHS TEST:\n";
            $this->testBkdDocumentPaths($usulan);

            // Test 6: Test field helper for BKD
            echo "\n🔧 FIELD HELPER BKD TEST:\n";
            $this->testFieldHelperBkd($usulan);

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testBkdGenerationLogic($periode)
    {
        $startDate = Carbon::parse($periode->tanggal_mulai);
        $month = $startDate->month;
        $year = $startDate->year;

        echo "   📅 Periode Start Date: {$startDate->format('d M Y')}\n";
        echo "   📅 Month: {$month}, Year: {$year}\n";

        // Determine current semester based on month
        $currentSemester = '';
        $currentYear = 0;

        if ($month >= 1 && $month <= 6) {
            $currentSemester = 'Genap';
            $currentYear = $year - 1;
            echo "   📅 Current Semester: {$currentSemester} {$currentYear}/" . ($currentYear + 1) . "\n";
        } elseif ($month >= 7 && $month <= 12) {
            $currentSemester = 'Ganjil';
            $currentYear = $year;
            echo "   📅 Current Semester: {$currentSemester} {$currentYear}/" . ($currentYear + 1) . "\n";
        }

        // Test BKD start calculation (mundur 2 semester)
        $bkdStartSemester = $currentSemester;
        $bkdStartYear = $currentYear;

        echo "   🔄 Calculating BKD start (mundur 2 semester):\n";
        for ($i = 0; $i < 2; $i++) {
            if ($bkdStartSemester === 'Ganjil') {
                $bkdStartSemester = 'Genap';
                $bkdStartYear--;
            } else {
                $bkdStartSemester = 'Ganjil';
            }
            echo "     Step " . ($i + 1) . ": {$bkdStartSemester} {$bkdStartYear}/" . ($bkdStartYear + 1) . "\n";
        }

        echo "   🎯 BKD Start Point: {$bkdStartSemester} {$bkdStartYear}/" . ($bkdStartYear + 1) . "\n";

        // Generate 4 semesters
        echo "   📚 Generated 4 BKD Semesters:\n";
        $tempSemester = $bkdStartSemester;
        $tempYear = $bkdStartYear;

        for ($i = 0; $i < 4; $i++) {
            $academicYear = $tempYear . '/' . ($tempYear + 1);
            $label = "BKD Semester {$tempSemester} {$academicYear}";
            $slug = 'bkd_' . strtolower($tempSemester) . '_' . str_replace('/', '_', $academicYear);
            
            $status = ($tempSemester === 'Genap' && $tempYear === 2024) ? '🎯 TARGET' : '📋';
            echo "     {$status} {$label} (slug: {$slug})\n";

            // Move to previous semester
            if ($tempSemester === 'Ganjil') {
                $tempSemester = 'Genap';
                $tempYear--;
            } else {
                $tempSemester = 'Ganjil';
            }
        }
    }

    private function testBkdLabels($usulan)
    {
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

    private function testBkdDocumentPaths($usulan)
    {
        $bkdLabels = $usulan->getBkdDisplayLabels();
        
        foreach ($bkdLabels as $field => $label) {
            if (strpos($label, 'Genap 2024/2025') !== false) {
                echo "   🎯 Testing BKD Genap 2024/2025:\n";
                echo "     - Field: {$field}\n";
                echo "     - Label: {$label}\n";
                
                // Test direct path
                $directPath = $usulan->getDocumentPath($field);
                echo "     - Direct Path: " . ($directPath ?: 'NOT FOUND') . "\n";
                
                // Test legacy key mapping
                $legacyKey = 'bkd_genap_2024_2025';
                $legacyPath = $usulan->getDocumentPath($legacyKey);
                echo "     - Legacy Key ({$legacyKey}): " . ($legacyPath ?: 'NOT FOUND') . "\n";
                
                // Test data_usulan structure
                $newStructurePath = $usulan->data_usulan['dokumen_usulan'][$field]['path'] ?? null;
                echo "     - New Structure: " . ($newStructurePath ?: 'NOT FOUND') . "\n";
                
                $legacyStructurePath = $usulan->data_usulan['dokumen_usulan'][$legacyKey]['path'] ?? null;
                echo "     - Legacy Structure: " . ($legacyStructurePath ?: 'NOT FOUND') . "\n";
                
                // Test flat structure
                $flatPath = $usulan->data_usulan[$field] ?? null;
                echo "     - Flat Structure: " . ($flatPath ?: 'NOT FOUND') . "\n";
                
                $flatLegacyPath = $usulan->data_usulan[$legacyKey] ?? null;
                echo "     - Flat Legacy: " . ($flatLegacyPath ?: 'NOT FOUND') . "\n";
                
                break;
            }
        }
    }

    private function testFieldHelperBkd($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
        $bkdLabels = $usulan->getBkdDisplayLabels();
        
        foreach ($bkdLabels as $field => $label) {
            if (strpos($label, 'Genap 2024/2025') !== false) {
                echo "   🎯 Testing Field Helper for BKD Genap 2024/2025:\n";
                
                $value = $fieldHelper->getFieldValue('dokumen_bkd', $field);
                $status = (strpos($value, 'Lihat Dokumen') !== false) ? '✅ UPLOADED' : '❌ MISSING';
                echo "     {$status} {$field}: " . (strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value) . "\n";
                
                // Test legacy key
                $legacyValue = $fieldHelper->getFieldValue('dokumen_bkd', 'bkd_genap_2024_2025');
                $legacyStatus = (strpos($legacyValue, 'Lihat Dokumen') !== false) ? '✅ UPLOADED' : '❌ MISSING';
                echo "     {$legacyStatus} bkd_genap_2024_2025: " . (strlen($legacyValue) > 100 ? substr($legacyValue, 0, 100) . '...' : $legacyValue) . "\n";
                
                break;
            }
        }
    }

    public function testValidationFields()
    {
        echo "\n🎯 VALIDATION FIELDS TEST:\n";
        echo "==========================\n\n";

        try {
            $usulan = Usulan::with(['periodeUsulan'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            // Test validation fields generation
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan);
            
            if (isset($validationFields['dokumen_bkd'])) {
                echo "✅ BKD fields in validation: " . count($validationFields['dokumen_bkd']) . " fields\n";
                
                $foundGenap2024 = false;
                foreach ($validationFields['dokumen_bkd'] as $field) {
                    $status = (strpos($field, 'genap_2024_2025') !== false) ? '🎯 FOUND' : '📋';
                    echo "   {$status} {$field}\n";
                    
                    if (strpos($field, 'genap_2024_2025') !== false) {
                        $foundGenap2024 = true;
                    }
                }
                
                if ($foundGenap2024) {
                    echo "   ✅ BKD Semester Genap 2024/2025 ditemukan dalam validation fields\n";
                } else {
                    echo "   ❌ BKD Semester Genap 2024/2025 TIDAK ditemukan dalam validation fields\n";
                }
            } else {
                echo "❌ dokumen_bkd tidak ada dalam validation fields\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new BkdGenap20242025Test();
$test->testBkdGeneration();
$test->testValidationFields();
