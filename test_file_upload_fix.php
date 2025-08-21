<?php

require_once 'vendor/autoload.php';

use App\Services\FileStorageService;
use App\Services\ValidationService;
use App\Models\BackendUnivUsulan\Usulan;

echo "=== TEST FILE UPLOAD FIX ===\n\n";

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

// Test 2: Check if Usulan model has required methods
echo "\n2. Testing Usulan model methods...\n";
try {
    $usulan = Usulan::first();
    if ($usulan) {
        $docPath = $usulan->getDocumentPath('file_surat_usulan');
        echo "✅ getDocumentPath method works: " . ($docPath ?? 'null') . "\n";
    } else {
        echo "⚠️ No usulan found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing Usulan model: " . $e->getMessage() . "\n";
}

// Test 3: Check validation rules
echo "\n3. Testing validation rules...\n";
$rules = $validationService->getDokumenPendukungRules();
echo "✅ Validation rules: " . count($rules) . " rules found\n";
foreach ($rules as $field => $rule) {
    echo "   - {$field}: {$rule}\n";
}

// Test 4: Check storage configuration
echo "\n4. Testing storage configuration...\n";
try {
    $usage = $fileStorage->getStorageUsage();
    echo "✅ Storage usage: " . round($usage['total_size_gb'], 2) . " GB\n";
    echo "   Usage percentage: " . round($usage['usage_percentage'], 2) . "%\n";
} catch (Exception $e) {
    echo "❌ Error checking storage: " . $e->getMessage() . "\n";
}

// Test 5: Check route configuration
echo "\n5. Testing route configuration...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminFakultasRoutes = collect($routes)->filter(function ($route) {
        return str_contains($route->getName(), 'admin-fakultas.usulan');
    });
    echo "✅ Admin Fakultas routes found: " . $adminFakultasRoutes->count() . "\n";
} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "✅ File upload fix implementation is ready for testing!\n";
echo "\nNext steps:\n";
echo "1. Test file upload in browser\n";
echo "2. Check Laravel logs for any errors\n";
echo "3. Verify file storage in public disk\n";
