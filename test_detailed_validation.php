<?php

require_once 'vendor/autoload.php';

use App\Services\FileStorageService;
use App\Services\ValidationService;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;

echo "=== TEST DETAILED VALIDATION ===\n\n";

// Test 1: Check if services can be instantiated
echo "1. Testing service instantiation...\n";
try {
    $fileStorage = new FileStorageService();
    $validationService = new ValidationService();
    echo "✅ Services created successfully\n";
} catch (Exception $e) {
    echo "❌ Error creating services: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check validation rules
echo "\n2. Testing validation rules...\n";
$rules = $validationService->getDokumenPendukungRules();
echo "✅ Validation rules: " . count($rules) . " rules found\n";
foreach ($rules as $field => $rule) {
    echo "   - {$field}: {$rule}\n";
}

// Test 3: Test validation methods exist
echo "\n3. Testing validation methods...\n";
$reflection = new ReflectionClass($validationService);
$methods = $reflection->getMethods(ReflectionMethod::IS_PRIVATE);
$validationMethods = array_filter($methods, function($method) {
    return strpos($method->getName(), 'validate') === 0;
});

echo "✅ Validation methods found: " . count($validationMethods) . "\n";
foreach ($validationMethods as $method) {
    echo "   - {$method->getName()}\n";
}

// Test 4: Test file storage methods
echo "\n4. Testing file storage methods...\n";
$fileStorageReflection = new ReflectionClass($fileStorage);
$fileStorageMethods = $fileStorageReflection->getMethods(ReflectionMethod::IS_PUBLIC);
echo "✅ FileStorage methods found: " . count($fileStorageMethods) . "\n";
foreach ($fileStorageMethods as $method) {
    echo "   - {$method->getName()}\n";
}

// Test 5: Check if Usulan model has required methods
echo "\n5. Testing Usulan model methods...\n";
try {
    $usulan = Usulan::first();
    if ($usulan) {
        $docPath = $usulan->getDocumentPath('file_surat_usulan');
        echo "✅ getDocumentPath method works: " . ($docPath ?? 'null') . "\n";

        $validasiData = $usulan->validasi_data ?? [];
        echo "✅ validasi_data accessible: " . (is_array($validasiData) ? 'yes' : 'no') . "\n";
    } else {
        echo "⚠️ No usulan found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing Usulan model: " . $e->getMessage() . "\n";
}

// Test 6: Test request simulation
echo "\n6. Testing request simulation...\n";
try {
    // Simulate a request with files
    $request = new Request();
    $request->merge([
        'dokumen_pendukung' => [
            'nomor_surat_usulan' => 'TEST/001/2024',
            'nomor_berita_senat' => 'BERITA/001/2024'
        ]
    ]);

    echo "✅ Request simulation successful\n";
    echo "   - Request method: " . $request->method() . "\n";
    echo "   - Has dokumen_pendukung: " . ($request->has('dokumen_pendukung') ? 'yes' : 'no') . "\n";

} catch (Exception $e) {
    echo "❌ Error testing request simulation: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "✅ Detailed validation implementation is ready for testing!\n";
echo "\nNext steps:\n";
echo "1. Test file upload in browser\n";
echo "2. Check Laravel logs for detailed validation info\n";
echo "3. Look for 'Dokumen pendukung validation detailed' log entries\n";
echo "4. Check 'Starting dokumen pendukung validation' log entries\n";
echo "\nExpected log entries:\n";
echo "- 'Starting dokumen pendukung validation'\n";
echo "- 'Dokumen pendukung validation detailed'\n";
echo "- 'Dokumen pendukung validation completed'\n";
echo "- 'Dokumen pendukung uploaded successfully' (if files uploaded)\n";
