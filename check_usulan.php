<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking usulan 19 validation data...\n";

$usulan = \App\Models\KepegawaianUniversitas\Usulan::find(19);
if (!$usulan) {
    echo "Usulan 19 not found!\n";
    exit;
}

echo "Usulan ID: " . $usulan->id . "\n";
echo "Status: " . $usulan->status_usulan . "\n";
echo "Validasi data:\n";
var_dump($usulan->validasi_data);

echo "\nGetting validation by role 'admin_fakultas':\n";
$validation = $usulan->getValidasiByRole('admin_fakultas');
var_dump($validation);

echo "\nValidation structure:\n";
if (isset($validation['validation'])) {
    echo "Has validation key: YES\n";
    echo "Validation keys: " . implode(', ', array_keys($validation['validation'])) . "\n";
    foreach ($validation['validation'] as $category => $fields) {
        echo "Category: $category\n";
        foreach ($fields as $field => $data) {
            echo "  Field: $field, Status: " . ($data['status'] ?? 'null') . "\n";
        }
    }
} else {
    echo "Has validation key: NO\n";
    echo "Available keys: " . implode(', ', array_keys($validation)) . "\n";
}

