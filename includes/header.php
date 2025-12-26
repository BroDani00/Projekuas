
<?php
// includes/header.php - KODE FINAL YANG BERFUNGSI
session_start();

// Tentukan root path
$root_path = '';

// Cek jika file ini diakses dari folder pages/
if (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) {
    $root_path = '../';
}

// Ambil nama file untuk active navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tugas Harian - <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></title>
    
    <!-- CSS Global -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>assets/css/responsive.css">
    
    <!-- Dynamic CSS -->
    <?php if (isset($css_file)): ?>
        <link rel="stylesheet" href="<?php echo $root_path; ?>assets/css/<?php echo $css_file; ?>">
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1002;
            background: linear-gradient(135deg, #ff6b8b, #ff8e9e);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            align-items: center;
            justify-content: center;
        }
        
        .mobile-toggle:hover {
            background: #ff4d6d;
            transform: scale(1.1);
        }
        
        /* Overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            cursor: pointer;
        }
        
        .overlay.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Sidebar Styling */
        .sidebar {
            width: 280px;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar.mobile-open {
            transform: translateX(0) !important;
        }
        
        .sidebar.mobile-closed {
            transform: translateX(-100%) !important;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: linear-gradient(135deg, #ff6b8b, #ff8e9e);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sidebar-close {
            display: none;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo i {
            font-size: 28px;
        }
        
        .logo h2 {
            font-size: 24px;
            font-weight: 700;
        }
        
        /* Ensure main content is clickable */
        .main-content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Overlay untuk mobile -->
    <div class="overlay" id="overlay"></div>
    
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-tasks"></i>
                    <h2>Manajemen Tugas Harian</h2>
                </div>
                <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <nav class="nav-menu">
                <a href="<?php echo $root_path; ?>pages/index.php" 
                   class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $root_path; ?>pages/semua-tugas.php" 
                   class="nav-item <?php echo $current_page == 'semua-tugas.php' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i>
                    <span>Semua Tugas</span>
                </a>
                <a href="<?php echo $root_path; ?>pages/tambah-tugas.php" 
                   class="nav-item <?php echo $current_page == 'tambah-tugas.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Tugas</span>
                </a>
                <a href="<?php echo $root_path; ?>pages/tugas-pending.php" 
                   class="nav-item <?php echo $current_page == 'tugas-pending.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i>
                    <span>Tugas Pending</span>
                </a>
                <a href="<?php echo $root_path; ?>pages/tugas-selesai.php" 
                   class="nav-item <?php echo $current_page == 'tugas-selesai.php' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i>
                    <span>Tugas Selesai</span>
                </a>
            </nav>
            
            <div class="user-profile">
                    <div class="user-avatar">
        <!-- SIMPAN FOTO DI: assets/images/profile.jpg -->
        <img src="../assets/images/Propil.jpg" alt="Foto Profil" 
             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
    </div>
                <div class="user-info">
                    <h4>Create By</h4>
                    <p>DANI & LUQMAN</p>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <header class="top-bar">
                <div class="page-info">
                    <h1 class="page-title"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                    <p class="page-subtitle"><?php echo date('l, d F Y'); ?></p>
                </div>
                <div class="top-bar-actions">
                    <div class="search-box">
                        <form method="GET" action="search.php" id="searchForm" style="display: flex; align-items: center;">
                          <i class="fas fa-search" style="margin-right: 10px;"></i>
                         <input type="text" name="q" id="searchInput" placeholder="Cari tugas..." 
                               style="border: none; outline: none; width: 100%;">
                        </form>
                    </div>
                </div>
            </header>
            
            <div class="content-wrapper" id="contentWrapper">