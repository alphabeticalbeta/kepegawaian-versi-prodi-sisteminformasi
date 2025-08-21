<?php
/**
 * Script untuk menguji koneksi database
 * Akses melalui: http://localhost/kepegawaian-unmul-v2/public/test_db.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Database Connection Test</h1>
    <p>Menguji koneksi database untuk aplikasi Kepegawaian UNMUL</p>

    <?php
    // Konfigurasi database dari .env
    $config = [
        'host' => '127.0.0.1',
        'port' => 3307,
        'database' => 'db_kepegunmul',
        'username' => 'root',
        'password' => 'root'
    ];

    echo "<div class='test-section'>";
    echo "<h2>📋 Konfigurasi Database</h2>";
    echo "<p><strong>Host:</strong> {$config['host']}</p>";
    echo "<p><strong>Port:</strong> {$config['port']}</p>";
    echo "<p><strong>Database:</strong> {$config['database']}</p>";
    echo "<p><strong>Username:</strong> {$config['username']}</p>";
    echo "</div>";

    // Test 1: Cek apakah MySQL berjalan di port 3307
    echo "<div class='test-section'>";
    echo "<h2>🔌 Test 1: Koneksi MySQL Server</h2>";

    $connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
    if ($connection) {
        echo "<p class='success'>✅ MySQL berjalan di port {$config['port']}</p>";
        fclose($connection);
    } else {
        echo "<p class='error'>❌ MySQL tidak berjalan di port {$config['port']}</p>";
        echo "<p class='info'>Error: $errstr ($errno)</p>";

        // Test port 3306
        echo "<p class='info'>Mencoba port 3306...</p>";
        $connection = @fsockopen($config['host'], 3306, $errno, $errstr, 5);
        if ($connection) {
            echo "<p class='success'>✅ MySQL berjalan di port 3306</p>";
            echo "<p class='warning'>💡 Saran: Ubah DB_PORT di .env menjadi 3306</p>";
            fclose($connection);
        } else {
            echo "<p class='error'>❌ MySQL tidak berjalan di port 3306 juga</p>";
        }
    }
    echo "</div>";

    // Test 2: Koneksi database
    echo "<div class='test-section'>";
    echo "<h2>🗄️ Test 2: Koneksi Database</h2>";

    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "<p class='success'>✅ Koneksi database berhasil!</p>";

        // Test tabel pegawais
        $stmt = $pdo->query("SHOW TABLES LIKE 'pegawais'");
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✅ Tabel 'pegawais' ada</p>";

            // Test query yang error
            $stmt = $pdo->prepare("SELECT * FROM pegawais WHERE nip = ? LIMIT 1");
            $stmt->execute(['199405242024061001']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo "<p class='success'>✅ User dengan NIP 199405242024061001 ditemukan</p>";
                echo "<pre>" . print_r($result, true) . "</pre>";
            } else {
                echo "<p class='error'>❌ User dengan NIP 199405242024061001 tidak ditemukan</p>";
                echo "<p class='warning'>💡 Ini mungkin penyebab error login - user tidak ada di database</p>";

                // Tampilkan beberapa user yang ada
                $stmt = $pdo->query("SELECT nip, nama FROM pegawais LIMIT 5");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($users) {
                    echo "<p class='info'>📋 Beberapa user yang ada di database:</p>";
                    echo "<ul>";
                    foreach ($users as $user) {
                        echo "<li>NIP: {$user['nip']} - Nama: {$user['nama']}</li>";
                    }
                    echo "</ul>";
                }
            }
        } else {
            echo "<p class='error'>❌ Tabel 'pegawais' tidak ada</p>";
            echo "<p class='warning'>💡 Anda perlu menjalankan migrasi database</p>";

            // Tampilkan tabel yang ada
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_NUM);
            if ($tables) {
                echo "<p class='info'>📋 Tabel yang ada di database:</p>";
                echo "<ul>";
                foreach ($tables as $table) {
                    echo "<li>{$table[0]}</li>";
                }
                echo "</ul>";
            }
        }

    } catch (PDOException $e) {
        echo "<p class='error'>❌ Koneksi database gagal: " . $e->getMessage() . "</p>";

        // Coba koneksi tanpa database
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "<p class='success'>✅ Koneksi ke MySQL server berhasil (tanpa database)</p>";

            // Cek database yang tersedia
            $stmt = $pdo->query("SHOW DATABASES");
            $databases = [];
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $databases[] = $row[0];
            }

            echo "<p class='info'>📋 Database yang tersedia:</p>";
            echo "<ul>";
            foreach ($databases as $db) {
                $highlight = ($db === $config['database']) ? " <strong>(Target)</strong>" : "";
                echo "<li>$db$highlight</li>";
            }
            echo "</ul>";

            if (!in_array($config['database'], $databases)) {
                echo "<p class='error'>❌ Database '{$config['database']}' tidak ada</p>";
                echo "<p class='warning'>💡 Anda perlu membuat database terlebih dahulu</p>";
            }

        } catch (PDOException $e2) {
            echo "<p class='error'>❌ Koneksi ke MySQL server gagal: " . $e2->getMessage() . "</p>";
        }
    }
    echo "</div>";

    // Solusi
    echo "<div class='test-section'>";
    echo "<h2>🛠️ Solusi</h2>";
    echo "<h3>Jika MySQL tidak berjalan:</h3>";
    echo "<ul>";
    echo "<li>Buka HeidiSQL dan pastikan MySQL berjalan</li>";
    echo "<li>Jika menggunakan Laragon: Buka Laragon → Klik 'Start All'</li>";
    echo "<li>Jika menggunakan XAMPP: Buka XAMPP Control Panel → Start MySQL</li>";
    echo "</ul>";

    echo "<h3>Jika database tidak ada:</h3>";
    echo "<ul>";
    echo "<li>Buat database '{$config['database']}' di HeidiSQL</li>";
    echo "<li>Atau jalankan: <code>php artisan migrate</code></li>";
    echo "</ul>";

    echo "<h3>Jika tabel tidak ada:</h3>";
    echo "<ul>";
    echo "<li>Jalankan: <code>php artisan migrate</code></li>";
    echo "<li>Jalankan: <code>php artisan db:seed</code></li>";
    echo "</ul>";

    echo "<h3>Jika user tidak ada:</h3>";
    echo "<ul>";
    echo "<li>Periksa apakah user dengan NIP 199405242024061001 ada di database</li>";
    echo "<li>Tambahkan user ke database atau gunakan NIP yang benar</li>";
    echo "</ul>";
    echo "</div>";
    ?>

    <div class="test-section">
        <h2>📞 Langkah Selanjutnya</h2>
        <p>Setelah memperbaiki masalah di atas:</p>
        <ol>
            <li>Jalankan: <code>php artisan migrate</code></li>
            <li>Jalankan: <code>php artisan db:seed</code></li>
            <li>Jalankan: <code>php artisan config:clear</code></li>
            <li>Coba login kembali</li>
        </ol>
    </div>

    <div class="test-section">
        <h2>🔄 Refresh Test</h2>
        <p><a href="test_db.php">Klik di sini</a> untuk menjalankan test ulang setelah memperbaiki masalah.</p>
    </div>
</body>
</html>
