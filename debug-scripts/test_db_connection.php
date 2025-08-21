<?php
// Test database connection
$host = '127.0.0.1';
$port = 3307;
$database = 'db_kepegunmul';
$username = 'root';
$password = 'root';

echo "Testing database connection...\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n";
echo "Username: $username\n\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connection successful!\n\n";

    // Test if pegawais table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'pegawais'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'pegawais' exists\n";

        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM pegawais");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "📊 Total records in pegawais table: " . $result['count'] . "\n";

        // Test specific query
        $stmt = $pdo->prepare("SELECT * FROM pegawais WHERE nip = ? LIMIT 1");
        $stmt->execute(['199405242024061001']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "✅ Found user with NIP: 199405242024061001\n";
        } else {
            echo "❌ User with NIP: 199405242024061001 not found\n";
        }

    } else {
        echo "❌ Table 'pegawais' does not exist\n";

        // Show available tables
        $stmt = $pdo->query("SHOW TABLES");
        echo "Available tables:\n";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "- " . $row[0] . "\n";
        }
    }

} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";

    // Try without database name
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✅ Connection to MySQL server successful (without database)\n";

        // Show databases
        $stmt = $pdo->query("SHOW DATABASES");
        echo "Available databases:\n";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "- " . $row[0] . "\n";
        }

    } catch (PDOException $e2) {
        echo "❌ Connection to MySQL server failed: " . $e2->getMessage() . "\n";
    }
}
?>
