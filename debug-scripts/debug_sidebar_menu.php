<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Route;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG SIDEBAR MENU ===\n\n";

// Check current route
$currentRoute = request()->route();
if ($currentRoute) {
    echo "Current Route: " . $currentRoute->getName() . "\n";
    echo "Current URL: " . request()->url() . "\n";
    echo "Current Path: " . request()->path() . "\n\n";
}

// Check if we're in periode usulan context
$isPeriodeUsulan = request()->is('*/periode-usulan/*') ||
                   request()->routeIs('backend.admin-univ-usulan.periode-usulan.*');

echo "Is Periode Usulan Context: " . ($isPeriodeUsulan ? 'YES' : 'NO') . "\n\n";

// Check master data menu patterns
$masterMenus = [
    ['route' => 'backend.admin-univ-usulan.data-pegawai.index', 'icon' => 'users', 'label' => 'Data Pegawai', 'pattern' => 'backend.admin-univ-usulan.data-pegawai.*'],
    ['route' => 'backend.admin-univ-usulan.role-pegawai.index', 'icon' => 'user-cog', 'label' => 'Role Pegawai', 'pattern' => 'backend.admin-univ-usulan.role-pegawai.*'],
    ['route' => 'backend.admin-univ-usulan.unitkerja.index', 'icon' => 'building-2', 'label' => 'Unit Kerja', 'pattern' => 'backend.admin-univ-usulan.unitkerja.*'],
    ['route' => 'backend.admin-univ-usulan.pangkat.index', 'icon' => 'award', 'label' => 'Pangkat', 'pattern' => 'backend.admin-univ-usulan.pangkat.*'],
    ['route' => 'backend.admin-univ-usulan.jabatan.index', 'icon' => 'briefcase', 'label' => 'Jabatan', 'pattern' => 'backend.admin-univ-usulan.jabatan.*'],
];

echo "=== MASTER DATA MENU CHECK ===\n";
foreach ($masterMenus as $menu) {
    $isActive = request()->routeIs($menu['pattern']);
    echo "Menu: {$menu['label']} - Active: " . ($isActive ? 'YES' : 'NO') . "\n";
}

// Check usulan menu patterns
echo "\n=== USULAN MENU CHECK ===\n";
$isUsulanActive = request()->is('*/usulan/*') ||
                  request()->routeIs('backend.admin-univ-usulan.usulan.*') ||
                  request()->routeIs('backend.admin-univ-usulan.dashboard-periode.*') ||
                  request()->routeIs('backend.admin-univ-usulan.periode-usulan.*');

echo "Is Usulan Active: " . ($isUsulanActive ? 'YES' : 'NO') . "\n";

// Check specific usulan types
$usulanTypes = [
    'nuptk' => 'Usulan NUPTK',
    'laporan-lkd' => 'Usulan Laporan LKD',
    'presensi' => 'Usulan Presensi',
    'penyesuaian-masa-kerja' => 'Usulan Penyesuaian Masa Kerja',
    'ujian-dinas-ijazah' => 'Usulan Ujian Dinas & Ijazah',
    'jabatan' => 'Usulan Jabatan',
    'laporan-serdos' => 'Usulan Laporan Serdos',
    'pensiun' => 'Usulan Pensiun',
    'kepangkatan' => 'Usulan Kepangkatan',
    'pencantuman-gelar' => 'Usulan Pencantuman Gelar',
    'id-sinta-sister' => 'Usulan ID SINTA ke SISTER',
    'satyalancana' => 'Usulan Satyalancana',
    'tugas-belajar' => 'Usulan Tugas Belajar',
    'pengaktifan-kembali' => 'Usulan Pengaktifan Kembali'
];

foreach ($usulanTypes as $type => $label) {
    $isActive = request()->get('jenis') == $type;
    echo "Usulan Type: {$label} - Active: " . ($isActive ? 'YES' : 'NO') . "\n";
}

echo "\n=== ROUTE LIST FOR ADMIN UNIV USULAN ===\n";
$routes = Route::getRoutes();
$adminUnivUsulanRoutes = [];

foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && strpos($name, 'backend.admin-univ-usulan') === 0) {
        $adminUnivUsulanRoutes[] = [
            'name' => $name,
            'uri' => $route->uri(),
            'methods' => $route->methods()
        ];
    }
}

foreach ($adminUnivUsulanRoutes as $route) {
    echo "Route: {$route['name']} - URI: {$route['uri']}\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
