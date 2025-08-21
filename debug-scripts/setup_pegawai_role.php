<?php

/**
 * Manual Setup Script untuk Role Pegawai
 *
 * Script ini dapat dijalankan secara manual untuk setup role pegawai
 * jika ada masalah dengan koneksi database atau seeder.
 */

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BackendUnivUsulan\Pegawai;
use Illuminate\Support\Facades\Hash;

echo "=== MANUAL SETUP ROLE PEGAWAI ===\n\n";

try {
    // Bootstrap Laravel
    $app = Application::configure(basePath: __DIR__)
        ->withRouting(
            web: __DIR__.'/routes/web.php',
            commands: __DIR__.'/routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            //
        })
        ->withExceptions(function (Exceptions $exceptions) {
            //
        })->create();

    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    echo "✅ Laravel berhasil di-bootstrap\n\n";

    // Step 1: Create Roles
    echo "1. Membuat roles...\n";

    $roles = [
        'Admin Universitas Usulan',
        'Admin Universitas',
        'Admin Fakultas',
        'Penilai Universitas',
        'Pegawai Unmul'
    ];

    foreach ($roles as $roleName) {
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'pegawai']
        );
        echo "   ✅ Role '{$roleName}' " . ($role->wasRecentlyCreated ? 'dibuat' : 'sudah ada') . "\n";
    }

    // Step 2: Create Permissions
    echo "\n2. Membuat permissions...\n";

    $permissions = [
        'view_all_pegawai_documents',
        'view_fakultas_pegawai_documents',
        'view_own_documents',
        'view_assessment_documents',
        'edit_own_profile',
        'submit_usulan',
        'view_own_usulan_status'
    ];

    foreach ($permissions as $permission) {
        $perm = Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'pegawai'
        ]);
        echo "   ✅ Permission '{$permission}' " . ($perm->wasRecentlyCreated ? 'dibuat' : 'sudah ada') . "\n";
    }

    // Step 3: Assign Permissions to Roles
    echo "\n3. Assign permissions ke roles...\n";

    $adminUnivUsulan = Role::where('name', 'Admin Universitas Usulan')->first();
    if ($adminUnivUsulan) {
        $adminUnivUsulan->givePermissionTo(['view_all_pegawai_documents']);
        echo "   ✅ Admin Universitas Usulan: view_all_pegawai_documents\n";
    }

    $adminFakultas = Role::where('name', 'Admin Fakultas')->first();
    if ($adminFakultas) {
        $adminFakultas->givePermissionTo(['view_fakultas_pegawai_documents']);
        echo "   ✅ Admin Fakultas: view_fakultas_pegawai_documents\n";
    }

    $pegawaiUnmul = Role::where('name', 'Pegawai Unmul')->first();
    if ($pegawaiUnmul) {
        $pegawaiUnmul->givePermissionTo([
            'view_own_documents',
            'edit_own_profile',
            'submit_usulan',
            'view_own_usulan_status'
        ]);
        echo "   ✅ Pegawai Unmul: view_own_documents, edit_own_profile, submit_usulan, view_own_usulan_status\n";
    }

    $penilaiUniversitas = Role::where('name', 'Penilai Universitas')->first();
    if ($penilaiUniversitas) {
        $penilaiUniversitas->givePermissionTo(['view_assessment_documents']);
        echo "   ✅ Penilai Universitas: view_assessment_documents\n";
    }

    // Step 4: Create Sample Pegawai Users
    echo "\n4. Membuat sample pegawai users...\n";

    $samplePegawais = [
        [
            'nip' => '199001012015011001',
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi.santoso@unmul.ac.id',
            'jenis_pegawai' => 'Dosen',
            'status_kepegawaian' => 'Dosen PNS',
            'is_admin' => false,
            'roles' => ['Pegawai Unmul']
        ],
        [
            'nip' => '199202022016022002',
            'nama_lengkap' => 'Citra Lestari',
            'email' => 'citra.lestari@unmul.ac.id',
            'jenis_pegawai' => 'Tenaga Kependidikan',
            'status_kepegawaian' => 'Tenaga Kependidikan PNS',
            'is_admin' => false,
            'roles' => ['Pegawai Unmul']
        ],
        [
            'nip' => '199503032017033003',
            'nama_lengkap' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@unmul.ac.id',
            'jenis_pegawai' => 'Dosen',
            'status_kepegawaian' => 'Dosen PNS',
            'is_admin' => false,
            'roles' => ['Pegawai Unmul']
        ],
        [
            'nip' => '199604042018044004',
            'nama_lengkap' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@unmul.ac.id',
            'jenis_pegawai' => 'Tenaga Kependidikan',
            'status_kepegawaian' => 'Tenaga Kependidikan PNS',
            'is_admin' => false,
            'roles' => ['Pegawai Unmul']
        ],
        [
            'nip' => '199705052019055005',
            'nama_lengkap' => 'Rizki Pratama',
            'email' => 'rizki.pratama@unmul.ac.id',
            'jenis_pegawai' => 'Dosen',
            'status_kepegawaian' => 'Dosen PPPK',
            'is_admin' => false,
            'roles' => ['Pegawai Unmul']
        ]
    ];

    foreach ($samplePegawais as $pegawaiData) {
        // Basic data untuk pegawai
        $baseData = [
            'gelar_depan' => null,
            'gelar_belakang' => 'S.Kom., M.Kom.',
            'nomor_kartu_pegawai' => '1234567890123456',
            'tempat_lahir' => 'Samarinda',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'Laki-Laki',
            'nomor_handphone' => '081234567890',
            'tmt_cpns' => '2020-01-01',
            'tmt_pns' => '2021-01-01',
            'tmt_pangkat' => '2022-01-01',
            'tmt_jabatan' => '2023-01-01',
            'pendidikan_terakhir' => 'Magister (S2)',
            'predikat_kinerja_tahun_pertama' => 'Baik',
            'predikat_kinerja_tahun_kedua' => 'Sangat Baik',
            'nuptk' => '1234567890123456',
            'nilai_konversi' => 85.5,
            'password' => Hash::make($pegawaiData['nip']),
            'username' => $pegawaiData['nip'],
        ];

        // Data khusus untuk Dosen
        if ($pegawaiData['jenis_pegawai'] === 'Dosen') {
            $baseData = array_merge($baseData, [
                'mata_kuliah_diampu' => 'Pemrograman Web, Basis Data, Algoritma',
                'ranting_ilmu_kepakaran' => 'Teknologi Informasi',
                'url_profil_sinta' => 'https://sinta.kemdikbud.go.id/authors/profile/123456',
            ]);
        }

        // Dummy document paths
        $dummyPaths = [
            'foto' => 'pegawai-files/foto/dummy-avatar.svg',
            'sk_cpns' => 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf',
            'sk_pns' => 'pegawai-files/sk_pns/dummy-sk-pns.pdf',
            'sk_pangkat_terakhir' => 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf',
            'sk_jabatan_terakhir' => 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf',
            'ijazah_terakhir' => 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf',
            'transkrip_nilai_terakhir' => 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf',
            'skp_tahun_pertama' => 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf',
            'skp_tahun_kedua' => 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf',
            'pak_konversi' => 'pegawai-files/pak_konversi/dummy-pak.pdf',
            'sk_penyetaraan_ijazah' => null,
            'disertasi_thesis_terakhir' => null,
        ];

        $baseData = array_merge($baseData, $dummyPaths);

        // Gabungkan data
        $pegawaiData = array_merge($baseData, [
            'nip' => $pegawaiData['nip'],
            'nama_lengkap' => $pegawaiData['nama_lengkap'],
            'email' => $pegawaiData['email'],
            'jenis_pegawai' => $pegawaiData['jenis_pegawai'],
            'status_kepegawaian' => $pegawaiData['status_kepegawaian'],
        ]);

        // Simpan roles sebelum array_merge
        $roles = $pegawaiData['roles'];

        // Simpan pegawai
        $pegawai = Pegawai::updateOrCreate(
            ['nip' => $pegawaiData['nip']],
            $pegawaiData
        );

        // Assign roles
        foreach ($roles as $role) {
            $pegawai->assignRole($role);
        }

        echo "   ✅ Pegawai '{$pegawaiData['nama_lengkap']}' " . ($pegawai->wasRecentlyCreated ? 'dibuat' : 'diupdate') . "\n";
    }

    // Step 5: Summary
    echo "\n5. Summary...\n";

    $totalRoles = Role::count();
    $totalPermissions = Permission::count();
    $totalPegawais = Pegawai::count();
    $pegawaiUnmulCount = Pegawai::whereHas('roles', function($q) {
        $q->where('name', 'Pegawai Unmul');
    })->count();

    echo "   📊 Total Roles: {$totalRoles}\n";
    echo "   📊 Total Permissions: {$totalPermissions}\n";
    echo "   📊 Total Pegawais: {$totalPegawais}\n";
    echo "   📊 Pegawai dengan role 'Pegawai Unmul': {$pegawaiUnmulCount}\n";

    echo "\n✅ SETUP ROLE PEGAWAI BERHASIL!\n\n";

    echo "🔑 LOGIN CREDENTIALS:\n";
    echo "   Username: NIP (contoh: 199001012015011001)\n";
    echo "   Password: NIP (contoh: 199001012015011001)\n\n";

    echo "📋 SAMPLE USERS:\n";
    foreach ($samplePegawais as $user) {
        echo "   • {$user['nama_lengkap']} ({$user['nip']}) - {$user['jenis_pegawai']} {$user['status_kepegawaian']}\n";
    }

    echo "\n🎯 ROLE PEGAWAI UNMUL FITUR:\n";
    echo "   ✅ View Own Documents - Melihat dokumen pribadi\n";
    echo "   ✅ Edit Own Profile - Mengedit profil pribadi\n";
    echo "   ✅ Submit Usulan - Mengajukan usulan jabatan\n";
    echo "   ✅ View Own Usulan Status - Melihat status usulan pribadi\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== SETUP COMPLETED ===\n";
