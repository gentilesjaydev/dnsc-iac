<?php
// Set Timezone for Philippines
date_default_timezone_set('Asia/Manila');

// Smart Environment Detection
$is_local = (
    !isset($_SERVER['HTTP_HOST']) || 
    in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || 
    strpos($_SERVER['SCRIPT_FILENAME'], 'xampp') !== false
);

if ($is_local) {
    // LOCAL XAMPP SETTINGS
    $host = 'localhost';
    $dbname = 'iac_covenant_db';
    $username = 'root';
    $password = '';
} else {
    // INFINITYFREE HOSTING SETTINGS
    $host = 'sql311.infinityfree.com';
    $dbname = 'if0_41815346_iac_covenant_db';
    $username = 'if0_41815346';
    $password = 'mQkeqngqP3Wk0G';
}

try {
    // For Remote Hosting, it's better to connect directly to the DB name
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Set attributes for Error handling and default fetch mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Sync MySQL timezone with Philippines
    $pdo->exec("SET time_zone = '+08:00'");

} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>