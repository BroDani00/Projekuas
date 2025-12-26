<?php
// config.php - Letakkan di root folder
session_start();

// Konfigurasi path
define('BASE_URL', 'http://localhost/Projekuas/');
define('ASSETS_PATH', BASE_URL . 'assets/');
define('INCLUDES_PATH', __DIR__ . '/includes/');

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_projekuas');
?>