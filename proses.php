<?php
// proses.php - FINAL FIXED VERSION
session_start();
include 'includes/koneksi.php';

// Debug mode
$debug = false;
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Log semua request untuk debugging
if ($debug) {
    error_log("=== PROSES.PHP ACCESSED ===");
    error_log("Method: " . $_SERVER['REQUEST_METHOD']);
    error_log("GET: " . print_r($_GET, true));
    error_log("POST: " . print_r($_POST, true));
}

// Function untuk redirect dengan pesan
function redirect($url, $message = '', $type = 'success') {
    if (!empty($message)) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    if ($debug) {
        error_log("Redirecting to: $url");
        error_log("Message: $message");
    }
    
    header("Location: $url");
    exit();
}

// Function untuk escape input
function clean_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// ========== CREATE ==========
if (isset($_POST['tambah'])) {
    if ($debug) error_log("=== CREATE OPERATION ===");
    
    $judul = clean_input($_POST['judul']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $deadline = $_POST['deadline'];
    $kategori = isset($_POST['kategori']) ? clean_input($_POST['kategori']) : '';
    $prioritas = isset($_POST['prioritas']) ? clean_input($_POST['prioritas']) : 'sedang';
    
    // Validasi input
    if (empty($judul)) {
        redirect('pages/tambah-tugas.php', 'Judul tugas tidak boleh kosong!', 'error');
    }
    
    if (empty($deadline)) {
        redirect('pages/tambah-tugas.php', 'Deadline tidak boleh kosong!', 'error');
    }
    
    // Query insert
    $query = "INSERT INTO tugas (judul, deskripsi, deadline, kategori, prioritas, status) 
              VALUES ('$judul', '$deskripsi', '$deadline', '$kategori', '$prioritas', 'Pending')";
    
    if ($debug) error_log("Insert Query: $query");
    
    if (mysqli_query($conn, $query)) {
        $new_id = mysqli_insert_id($conn);
        if ($debug) error_log("Insert successful, ID: $new_id");
        
        redirect('pages/semua-tugas.php', 'Tugas berhasil ditambahkan!', 'success');
    } else {
        $error = mysqli_error($conn);
        if ($debug) error_log("Insert failed: $error");
        
        redirect('pages/tambah-tugas.php', 'Gagal menambahkan tugas: ' . $error, 'error');
    }
}

// ========== UPDATE ==========
// Edit tugas (form submission)
if (isset($_POST['edit'])) {
    if ($debug) error_log("=== UPDATE OPERATION ===");
    
    $id = intval($_POST['id']);
    $judul = clean_input($_POST['judul']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $deadline = $_POST['deadline'];
    $kategori = isset($_POST['kategori']) ? clean_input($_POST['kategori']) : '';
    $prioritas = isset($_POST['prioritas']) ? clean_input($_POST['prioritas']) : 'sedang';
    
    // Status hanya jika ada di form (toggle)
    $status = isset($_POST['status']) ? 'Selesai' : 'Pending';
    
    // Validasi input
    if (empty($judul)) {
        redirect("pages/tambah-tugas.php?edit=$id", 'Judul tugas tidak boleh kosong!', 'error');
    }
    
    if (empty($deadline)) {
        redirect("pages/tambah-tugas.php?edit=$id", 'Deadline tidak boleh kosong!', 'error');
    }
    
    // Query update
    $query = "UPDATE tugas SET 
              judul = '$judul',
              deskripsi = '$deskripsi',
              deadline = '$deadline',
              kategori = '$kategori',
              prioritas = '$prioritas',
              status = '$status',
              updated_at = NOW()
              WHERE id = $id";
    
    if ($debug) error_log("Update Query: $query");
    
    if (mysqli_query($conn, $query)) {
        if ($debug) error_log("Update successful for ID: $id");
        
        redirect('pages/semua-tugas.php', 'Tugas berhasil diperbarui!', 'success');
    } else {
        $error = mysqli_error($conn);
        if ($debug) error_log("Update failed: $error");
        
        redirect("pages/tambah-tugas.php?edit=$id", 'Gagal memperbarui tugas: ' . $error, 'error');
    }
}

// ========== TOGGLE STATUS ==========
if (isset($_GET['toggle_status'])) {
    if ($debug) error_log("=== TOGGLE STATUS OPERATION ===");
    
    $id = intval($_GET['toggle_status']);
    
    // Get current status
    $query_current = "SELECT status FROM tugas WHERE id = $id";
    $result = mysqli_query($conn, $query_current);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_status = $row['status'];
        $new_status = ($current_status == 'Pending') ? 'Selesai' : 'Pending';
        
        // Update status
        $query = "UPDATE tugas SET status = '$new_status', updated_at = NOW() WHERE id = $id";
        
        if ($debug) error_log("Toggle Query: $query");
        
        if (mysqli_query($conn, $query)) {
            if ($debug) error_log("Status toggled for ID: $id ($current_status -> $new_status)");
            
            // Get referrer untuk redirect kembali
            $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
            redirect($referrer, "Status tugas berhasil diubah menjadi $new_status!", 'success');
        } else {
            $error = mysqli_error($conn);
            if ($debug) error_log("Toggle failed: $error");
            
            $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
            redirect($referrer, 'Gagal mengubah status tugas!', 'error');
        }
    } else {
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
        redirect($referrer, 'Tugas tidak ditemukan!', 'error');
    }
}

// ========== DELETE ==========
if (isset($_GET['delete'])) {
    if ($debug) error_log("=== DELETE OPERATION ===");
    
    $id = intval($_GET['delete']);
    
    // Periksa apakah tugas ada
    $query_check = "SELECT id FROM tugas WHERE id = $id";
    $result = mysqli_query($conn, $query_check);
    
    if (mysqli_num_rows($result) > 0) {
        // Hapus tugas
        $query = "DELETE FROM tugas WHERE id = $id";
        
        if ($debug) error_log("Delete Query: $query");
        
        if (mysqli_query($conn, $query)) {
            if ($debug) error_log("Delete successful for ID: $id");
            
            // Get referrer untuk redirect kembali
            $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
            redirect($referrer, 'Tugas berhasil dihapus!', 'success');
        } else {
            $error = mysqli_error($conn);
            if ($debug) error_log("Delete failed: $error");
            
            $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
            redirect($referrer, 'Gagal menghapus tugas!', 'error');
        }
    } else {
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
        redirect($referrer, 'Tugas tidak ditemukan!', 'error');
    }
}

// ========== BULK ACTIONS ==========
if (isset($_POST['bulk_action'])) {
    if ($debug) error_log("=== BULK OPERATION ===");
    
    $action = $_POST['bulk_action'];
    $task_ids = isset($_POST['task_ids']) ? $_POST['task_ids'] : array();
    
    if (empty($task_ids)) {
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
        redirect($referrer, 'Tidak ada tugas yang dipilih!', 'warning');
    }
    
    $ids_string = implode(',', array_map('intval', $task_ids));
    
    if ($action == 'complete') {
        $query = "UPDATE tugas SET status = 'Selesai', updated_at = NOW() WHERE id IN ($ids_string)";
        $message = 'Tugas berhasil ditandai selesai!';
    } elseif ($action == 'delete') {
        $query = "DELETE FROM tugas WHERE id IN ($ids_string)";
        $message = 'Tugas berhasil dihapus!';
    }
    
    if ($debug) error_log("Bulk Query: $query");
    
    if (mysqli_query($conn, $query)) {
        $affected = mysqli_affected_rows($conn);
        if ($debug) error_log("Bulk action successful, affected: $affected");
        
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
        redirect($referrer, "$message ($affected tugas)", 'success');
    } else {
        $error = mysqli_error($conn);
        if ($debug) error_log("Bulk action failed: $error");
        
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'pages/semua-tugas.php';
        redirect($referrer, 'Gagal melakukan aksi bulk!', 'error');
    }
}

// ========== DEFAULT REDIRECT ==========
// Jika tidak ada action yang cocok, redirect ke halaman utama
if ($debug) error_log("No action matched, redirecting to home");
redirect('pages/index.php');

// Tutup koneksi
mysqli_close($conn);
?>