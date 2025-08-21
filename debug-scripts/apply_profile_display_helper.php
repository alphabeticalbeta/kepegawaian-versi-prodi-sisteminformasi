<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\File;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Menerapkan ProfileDisplayHelper ke semua tab profile...\n";

// Daftar file yang perlu diupdate
$files = [
    'resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/personal-tab.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/kepegawaian-tab.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/dosen-tab.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/pak-skp-tab.blade.php',
    'resources/views/backend/layouts/views/pegawai-unmul/profile/components/tabs/dokumen-tab.blade.php',
];

// Mapping untuk replacement
$replacements = [
    // Personal tab replacements
    "{{ \$pegawai->nama_lengkap ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayNamaLengkap(\$pegawai) }}",
    "{{ \$pegawai->email ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayEmail(\$pegawai) }}",
    "{{ \$pegawai->gelar_depan ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->gelar_depan) }}",
    "{{ \$pegawai->gelar_belakang ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->gelar_belakang) }}",
    "{{ \$pegawai->tempat_lahir ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->tempat_lahir) }}",
    "{{ \$pegawai->jenis_kelamin ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->jenis_kelamin) }}",
    "{{ \$pegawai->nomor_handphone ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayNomorHandphone(\$pegawai) }}",
    "{{ \$pegawai->pendidikan_terakhir ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayPendidikanTerakhir(\$pegawai) }}",
    "{{ \$pegawai->nama_universitas_sekolah ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayNamaUniversitasSekolah(\$pegawai) }}",
    "{{ \$pegawai->nama_prodi_jurusan ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayNamaProdiJurusan(\$pegawai) }}",

    // Kepegawaian tab replacements
    "{{ \$pegawai->nomor_kartu_pegawai ?? '-' }}" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->nomor_kartu_pegawai) }}",

    // Dosen tab replacements
    "Belum diisi" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->nuptk, 'Belum diisi') }}",
    "Belum diisi" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->url_profil_sinta, 'Belum diisi') }}",
    "Belum diisi" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->ranting_ilmu_kepakaran, 'Belum diisi') }}",
    "Belum diisi" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->mata_kuliah_diampu, 'Belum diisi') }}",

    // PAK-SKP tab replacements
    "Belum diisi" => "{{ \\App\\Helpers\\ProfileDisplayHelper::displayValue(\$pegawai->nilai_konversi, 'Belum diisi') }}",
];

$totalFiles = 0;
$totalReplacements = 0;

foreach ($files as $file) {
    if (!File::exists($file)) {
        echo "File tidak ditemukan: $file\n";
        continue;
    }

    $content = File::get($file);
    $originalContent = $content;
    $fileReplacements = 0;

    foreach ($replacements as $search => $replace) {
        $count = substr_count($content, $search);
        if ($count > 0) {
            $content = str_replace($search, $replace, $content);
            $fileReplacements += $count;
            echo "  - Replaced '$search' with '$replace' ($count times)\n";
        }
    }

    if ($content !== $originalContent) {
        File::put($file, $content);
        $totalFiles++;
        $totalReplacements += $fileReplacements;
        echo "✓ Updated: $file ($fileReplacements replacements)\n";
    } else {
        echo "- No changes needed: $file\n";
    }
}

echo "\nSelesai! Total files updated: $totalFiles, Total replacements: $totalReplacements\n";
