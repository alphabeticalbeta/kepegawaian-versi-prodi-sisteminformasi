<?php

/**
 * Script khusus untuk Laragon - Setup Role Baru
 * Mencoba berbagai konfigurasi database yang umum digunakan di Laragon
 */

echo "🚀 Script Setup Role untuk Laragon Environment\n\n";

// Array konfigurasi database yang akan dicoba
$configs = [
    [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'kepegawaian_unmul',
        'username' => 'root',
        'password' => '',
        'label' => 'Laragon Standard (127.0.0.1:3306)'
    ],
    [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'kepegawaian_unmul',
        'username' => 'root',
        'password' => '',
        'label' => 'Localhost Standard'
    ],
    [
        'host' => '127.0.0.1',
        'port' => '3307',
        'dbname' => 'kepegawaian_unmul',
        'username' => 'root',
        'password' => '',
        'label' => 'Alternative Port 3307'
    ]
];

$connected = false;
$pdo = null;

// Coba setiap konfigurasi sampai berhasil
foreach ($configs as $config) {
    echo "🔍 Mencoba koneksi: {$config['label']}\n";

    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Test koneksi dengan query sederhana
        $stmt = $pdo->query("SELECT 1");

        echo "✅ Berhasil terhubung dengan: {$config['label']}\n\n";
        $connected = true;
        break;

    } catch (PDOException $e) {
        echo "❌ Gagal: " . $e->getMessage() . "\n";
        continue;
    }
}

if (!$connected) {
    echo "\n❌ Tidak dapat terhubung ke database dengan konfigurasi manapun.\n";
    echo "📋 Checklist untuk troubleshooting:\n";
    echo "   1. Pastikan Laragon sudah running (Start All)\n";
    echo "   2. Pastikan MySQL service aktif di Laragon\n";
    echo "   3. Pastikan database 'kepegawaian_unmul' sudah dibuat\n";
    echo "   4. Cek port MySQL di Laragon (biasanya 3306)\n\n";
    echo "🔧 Cara manual:\n";
    echo "   1. Buka phpMyAdmin atau MySQL client\n";
    echo "   2. Jalankan SQL berikut:\n\n";

    echo "-- Insert new roles\n";
    echo "INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES\n";
    echo "('Admin Keuangan', 'pegawai', NOW(), NOW()),\n";
    echo "('Tim Senat', 'pegawai', NOW(), NOW());\n\n";

    echo "-- Insert new permissions\n";
    echo "INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES\n";
    echo "('view_financial_documents', 'pegawai', NOW(), NOW()),\n";
    echo "('view_senate_documents', 'pegawai', NOW(), NOW());\n\n";

    echo "-- Assign permissions to roles\n";
    echo "INSERT INTO role_has_permissions (permission_id, role_id)\n";
    echo "SELECT p.id, r.id\n";
    echo "FROM permissions p, roles r\n";
    echo "WHERE p.name = 'view_financial_documents' AND r.name = 'Admin Keuangan';\n\n";

    echo "INSERT INTO role_has_permissions (permission_id, role_id)\n";
    echo "SELECT p.id, r.id\n";
    echo "FROM permissions p, roles r\n";
    echo "WHERE p.name = 'view_senate_documents' AND r.name = 'Tim Senat';\n\n";

    exit(1);
}

try {
    echo "🔍 Mengecek role yang sudah ada...\n";

    // Cek role yang ada
    $stmt = $pdo->query("SELECT name FROM roles WHERE guard_name = 'pegawai' ORDER BY name");
    $existingRoles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "📋 Role yang tersedia saat ini:\n";
    foreach ($existingRoles as $role) {
        echo "   • $role\n";
    }

    // Cek apakah role baru sudah ada
    $newRoles = ['Admin Keuangan', 'Tim Senat'];
    $missingRoles = [];

    foreach ($newRoles as $roleName) {
        if (!in_array($roleName, $existingRoles)) {
            $missingRoles[] = $roleName;
        }
    }

    if (empty($missingRoles)) {
        echo "\n✅ Semua role baru sudah ada di database!\n";
        echo "💡 Role 'Admin Keuangan' dan 'Tim Senat' sudah tersedia.\n";
        echo "💡 Silakan refresh halaman Edit Role Pegawai untuk melihat perubahan.\n";
    } else {
        echo "\n⚠️ Role yang belum ada: " . implode(', ', $missingRoles) . "\n";
        echo "🔧 Menambahkan role yang hilang...\n\n";

        // Insert new roles
        $stmt = $pdo->prepare("INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?, 'pegawai', NOW(), NOW())");

        foreach ($missingRoles as $roleName) {
            try {
                $stmt->execute([$roleName]);
                echo "✅ Role '$roleName' berhasil ditambahkan\n";
            } catch (PDOException $e) {
                echo "⚠️ Role '$roleName' mungkin sudah ada: " . $e->getMessage() . "\n";
            }
        }

        // Insert permissions
        echo "\n🔐 Menambahkan permissions...\n";
        $stmt = $pdo->prepare("INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at) VALUES (?, 'pegawai', NOW(), NOW())");

        $permissions = ['view_financial_documents', 'view_senate_documents'];
        foreach ($permissions as $permission) {
            try {
                $stmt->execute([$permission]);
                echo "✅ Permission '$permission' berhasil ditambahkan\n";
            } catch (PDOException $e) {
                echo "ℹ️ Permission '$permission' sudah ada\n";
            }
        }

        // Assign permissions to roles
        echo "\n🔗 Menghubungkan permissions dengan roles...\n";

        $assignments = [
            'view_financial_documents' => 'Admin Keuangan',
            'view_senate_documents' => 'Tim Senat'
        ];

        foreach ($assignments as $permissionName => $roleName) {
            try {
                $stmt = $pdo->prepare("
                    INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
                    SELECT p.id, r.id
                    FROM permissions p, roles r
                    WHERE p.name = ? AND r.name = ?
                    AND p.guard_name = 'pegawai' AND r.guard_name = 'pegawai'
                ");
                $stmt->execute([$permissionName, $roleName]);
                echo "✅ Permission '$permissionName' di-assign ke '$roleName'\n";
            } catch (PDOException $e) {
                echo "⚠️ Assignment '$permissionName' -> '$roleName': " . $e->getMessage() . "\n";
            }
        }
    }

    // Tampilkan semua role setelah update
    echo "\n📋 Daftar lengkap role setelah update:\n";
    $stmt = $pdo->query("
        SELECT r.name as role_name, GROUP_CONCAT(p.name SEPARATOR ', ') as permissions
        FROM roles r
        LEFT JOIN role_has_permissions rhp ON r.id = rhp.role_id
        LEFT JOIN permissions p ON rhp.permission_id = p.id
        WHERE r.guard_name = 'pegawai'
        GROUP BY r.id, r.name
        ORDER BY r.name
    ");

    $allRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($allRoles as $roleData) {
        echo "   • {$roleData['role_name']}\n";
        if ($roleData['permissions']) {
            echo "     Permissions: {$roleData['permissions']}\n";
        }
    }

    echo "\n🎉 Setup role selesai!\n";
    echo "💡 Role baru sekarang tersedia di halaman Edit Master Role Pegawai\n";
    echo "💡 Silakan refresh halaman dan coba edit role untuk pegawai.\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Query error pada setup roles\n";
}
