<?php
// Zorg dat output_buffering aan is voor sessies
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Genereer sessie ID als het niet bestaat
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = bin2hex(random_bytes(16));
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grappenschrijver</title>
    <link rel="stylesheet" href="/grappenschrijver/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🎭 Grappenschrijver</h1>
            <div class="nav-links">
                <a href="/grappenschrijver/index.php">Home</a>
            </div>
        </div>
    </header>

    <div class="container">
