<?php
/**
 * Simple Database Test
 * Akses: http://localhost/kepegawaian-unmul-v2/public/db_test.php
 */

echo "<h1>Database Connection Test</h1>";
echo "<p>Testing connection to MySQL...</p>";

// Konfigurasi database
$host = '127.0.0.1';
$port = 3307;
$database = 'db_kepegunmul';
$username = 'root';
$password = 'root';

echo "<h2>Configuration:</h2>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Port: $port</li>";
echo "<li>Database: $database</li>";
echo "<li>Username: $username</li>";
echo "</ul>";

// Test 1: Check if MySQL is running
echo "<h2>Test 1: MySQL Server Connection</h2>";
$connection = @fsockopen($host, $port, $errno, $errstr, 5);
if ($connection) {
    echo "<p style='color: green;'>✅ MySQL is running on port $port</p>";
    fclose($connection);
} else {
    echo "<p style='color: red;'>❌ MySQL is not running on port $port</p>";
    echo "<p>Error: $errstr ($errno)</p>";

    // Try port 3306
    echo "<p>Trying port 3306...</p>";
    $connection = @fsockopen($host, 3306, $errno, $errstr, 5);
    if ($connection) {
        echo "<p style='color: green;'>✅ MySQL is running on port 3306</p>";
        echo "<p style='color: orange;'>💡 You might need to change DB_PORT to 3306 in .env file</p>";
        fclose($connection);
    } else {
        echo "<p style='color: red;'>❌ MySQL is not running on port 3306 either</p>";
    }
}

// Test 2: Database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p style='color: green;'>✅ Database connection successful!</p>";

    // Check if pegawais table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'pegawais'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Table 'pegawais' exists</p>";

        // Check for specific user
        $stmt = $pdo->prepare("SELECT * FROM pegawais WHERE nip = ? LIMIT 1");
        $stmt->execute(['199405242024061001']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "<p style='color: green;'>✅ User with NIP 199405242024061001 found</p>";
        } else {
            echo "<p style='color: red;'>❌ User with NIP 199405242024061001 not found</p>";
            echo "<p style='color: orange;'>💡 This might be the login issue - user doesn't exist</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Table 'pegawais' does not exist</p>";
        echo "<p style='color: orange;'>💡 You need to run migrations</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";

    // Try without database
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "<p style='color: green;'>✅ MySQL server connection successful (without database)</p>";

        // Show databases
        $stmt = $pdo->query("SHOW DATABASES");
        echo "<p>Available databases:</p>";
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $highlight = ($row[0] === $database) ? " <strong>(Target)</strong>" : "";
            echo "<li>{$row[0]}$highlight</li>";
        }
        echo "</ul>";

    } catch (PDOException $e2) {
        echo "<p style='color: red;'>❌ MySQL server connection failed: " . $e2->getMessage() . "</p>";
    }
}

echo "<h2>Solutions:</h2>";
echo "<ol>";
echo "<li>If MySQL is not running: Start MySQL service</li>";
echo "<li>If wrong port: Change DB_PORT in .env file</li>";
echo "<li>If database doesn't exist: Create database or run migrations</li>";
echo "<li>If table doesn't exist: Run 'php artisan migrate'</li>";
echo "<li>If user doesn't exist: Add user to database or use correct NIP</li>";
echo "</ol>";

echo "<p><a href='db_test.php'>Refresh Test</a></p>";
?>
