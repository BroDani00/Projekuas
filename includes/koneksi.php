<?php
// includes/koneksi.php - VERSI AMAN
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_projekuas';

// Disable error display untuk production
error_reporting(0);

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Jangan tampilkan error detail di production
    die("Database connection failed. Please check configuration.");
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>