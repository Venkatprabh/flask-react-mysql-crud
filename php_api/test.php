<?php
echo "<h1>PHP API Test</h1>";
echo "<p>If you can see this, PHP is working!</p>";

// Test database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ecommerce_db', 'root', 'yourpassword');
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // List tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Database Tables:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test API endpoints
echo "<h3>API Endpoints:</h3>";
echo "<ul>";
echo "<li><a href='/api/users'>GET /api/users</a> - List users</li>";
echo "<li><a href='/api/products'>GET /api/products</a> - List products</li>";
echo "</ul>";
?>