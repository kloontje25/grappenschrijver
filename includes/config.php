<?php
// Database configuratie
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'grappenschrijver');

// Verbinding maken met database
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Controleer verbinding
    if ($conn->connect_error) {
        die("Verbindingsfout: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Databasefout: " . $e->getMessage());
}
?>
