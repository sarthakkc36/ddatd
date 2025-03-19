<?php
define('ALLOWED_ACCESS', true);
require_once 'includes/config.php';

echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>";
echo "<h2 style='color: #2C7BE5;'>Database Initialization</h2>";

try {
    // Create database connection without database name
    $pdo = new PDO(
        "mysql:host=" . DB_HOST,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: #10B981;'>✓ Connected to MySQL server</p>";

    // Drop database if exists and create new one
    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
    $pdo->exec("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: #10B981;'>✓ Created fresh database: " . DB_NAME . "</p>";
    
    // Select the database
    $pdo->exec("USE " . DB_NAME);
    echo "<p style='color: #10B981;'>✓ Selected database</p>";

    // Read and execute the SQL file
    $sql = file_get_contents('admin/database.sql');
    if (!$sql) {
        throw new Exception("Could not read database.sql file");
    }
    echo "<p style='color: #10B981;'>✓ Read SQL file successfully</p>";
    
    // Split SQL by semicolon to execute multiple queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    $total_queries = count($queries);
    $executed_queries = 0;
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdo->exec($query);
            $executed_queries++;
        }
    }
    echo "<p style='color: #10B981;'>✓ Executed {$executed_queries} SQL queries successfully</p>";

    // Final success message
    echo "<h2 style='color: #2C7BE5;'>Database Initialized Successfully!</h2>";
    echo "<p><strong>You can now access the admin panel at:</strong> <a href='/ddatd/admin/'>/admin/</a></p>";
    echo "<p><strong>Default credentials:</strong><br>";
    echo "Username: admin<br>";
    echo "Password: admin123</p>";
    echo "<p style='color: #EF4444;'><strong>Important:</strong> Please change the default password after logging in.</p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); background-color: #FEE2E2; color: #EF4444;'>";
    echo "<h2>Database Initialization Failed</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
