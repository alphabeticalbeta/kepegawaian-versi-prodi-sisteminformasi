<?php

/**
 * Script manual untuk menambahkan role baru
 * Jalankan script ini jika ada masalah koneksi database
 */

// Database configuration
$host = '127.0.0.1';
$dbname = 'kepegawaian_unmul';
$username = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Berhasil terhubung ke database\n\n";

    // Check if roles already exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM roles WHERE name IN ('Admin Keuangan', 'Tim Senat') AND guard_name = 'pegawai'");
    $stmt->execute();
    $existingRoles = $stmt->fetchColumn();

    if ($existingRoles > 0) {
        echo "ℹ️ Role 'Admin Keuangan' dan 'Tim Senat' sudah ada di database\n";
    } else {
        // Insert new roles
        $stmt = $pdo->prepare("INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?, 'pegawai', NOW(), NOW())");

        $roles = ['Admin Keuangan', 'Tim Senat'];
        foreach ($roles as $role) {
            $stmt->execute([$role]);
            echo "✅ Role '$role' berhasil ditambahkan\n";
        }

        // Insert permissions
        $stmt = $pdo->prepare("INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES (?, 'pegawai', NOW(), NOW())");

        $permissions = ['view_financial_documents', 'view_senate_documents'];
        foreach ($permissions as $permission) {
            $stmt->execute([$permission]);
            echo "✅ Permission '$permission' berhasil ditambahkan\n";
        }

        // Assign permissions to roles
        $stmt = $pdo->prepare("INSERT INTO role_has_permissions (permission_id, role_id)
                              SELECT p.id, r.id
                              FROM permissions p, roles r
                              WHERE p.name = ? AND r.name = ? AND p.guard_name = 'pegawai' AND r.guard_name = 'pegawai'");

        $stmt->execute(['view_financial_documents', 'Admin Keuangan']);
        $stmt->execute(['view_senate_documents', 'Tim Senat']);

        echo "✅ Permissions berhasil di-assign ke roles\n";
    }

    // Show all roles
    echo "\n📋 Daftar semua role yang tersedia:\n";
    $stmt = $pdo->query("SELECT name FROM roles WHERE guard_name = 'pegawai' ORDER BY name");
    $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($roles as $role) {
        echo "   • $role\n";
    }

    echo "\n🎉 Setup role selesai!\n";
    echo "💡 Role baru sekarang tersedia di halaman Edit Master Role Pegawai\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Pastikan database MySQL berjalan dan konfigurasi benar\n";
}
