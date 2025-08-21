<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\PeriodeUsulan;

echo "=== CHECK PERIODE DATA ===\n";

$periodes = PeriodeUsulan::all();

foreach ($periodes as $periode) {
    echo "ID: " . $periode->id . "\n";
    echo "Nama: " . $periode->nama_periode . "\n";
    echo "Jenis Usulan: " . $periode->jenis_usulan . "\n";
    echo "Status: " . $periode->status . "\n";
    echo "Status Kepegawaian: " . json_encode($periode->status_kepegawaian) . "\n";
    echo "---\n";
}

echo "=== CHECK COMPLETED ===\n";
