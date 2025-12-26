<?php
// pages/tugas-pending.php - FINAL FIXED VERSION
$page_title = "Tugas Pending";
$css_file = "pending.css";
$js_file = "pending.js";

include '../includes/header.php';
include '../includes/koneksi.php';

// Query hanya tugas pending dengan prioritas
$query = "SELECT * FROM tugas WHERE status = 'Pending' ORDER BY 
          CASE prioritas 
            WHEN 'tinggi' THEN 1 
            WHEN 'sedang' THEN 2 
            WHEN 'rendah' THEN 3 
            ELSE 4 
          END,
          deadline ASC,
          created_at DESC";

$result = mysqli_query($conn, $query);
$total_pending = mysqli_num_rows($result);

// Hitung statistik
$today = date('Y-m-d');
$three_days_later = date('Y-m-d', strtotime('+3 days'));

// Tugas mendekati deadline (3 hari)
$query_upcoming = "SELECT COUNT(*) as upcoming FROM tugas 
                   WHERE status = 'Pending' 
                   AND deadline BETWEEN '$today' AND '$three_days_later'";
$result_upcoming = mysqli_query($conn, $query_upcoming);
$upcoming_count = mysqli_fetch_assoc($result_upcoming)['upcoming'] ?? 0;

// Tugas lewat deadline
$query_overdue = "SELECT COUNT(*) as overdue FROM tugas 
                  WHERE status = 'Pending' AND deadline < '$today'";
$result_overdue = mysqli_query($conn, $query_overdue);
$overdue_count = mysqli_fetch_assoc($result_overdue)['overdue'] ?? 0;

// Hitung berdasarkan prioritas
$query_high = "SELECT COUNT(*) as high FROM tugas 
               WHERE status = 'Pending' AND prioritas = 'tinggi'";
$result_high = mysqli_query($conn, $query_high);
$high_count = mysqli_fetch_assoc($result_high)['high'] ?? 0;
?>

<div class="pending-page">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-clock"></i>
        </h1>
        <p class="page-subtitle"><?php echo $total_pending; ?> tugas sedang menunggu penyelesaian</p>
    </div>

    <!-- Statistik Ringkas -->
    <div class="stats-overview">
        <div class="stat-card overdue-stat">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $overdue_count; ?></h3>
                <p>Tugas Terlambat</p>
            </div>
        </div>
        
        <div class="stat-card upcoming-stat">
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $upcoming_count; ?></h3>
                <p>Deadline Mendekat</p>
            </div>
        </div>
        
        <div class="stat-card high-priority-stat">
            <div class="stat-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $high_count; ?></h3>
                <p>Prioritas Tinggi</p>
            </div>
        </div>
        
        <div class="stat-card total-stat">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $total_pending; ?></h3>
                <p>Total Pending</p>
            </div>
        </div>
    </div>

    
    </div>

    <!-- Daftar Tugas Pending -->
    <div class="pending-tasks-container">
        <?php if ($total_pending > 0): ?>
            <div class="tasks-header">
                <h3><i class="fas fa-list"></i> Daftar Tugas Pending</h3>
                <div class="tasks-summary">
                    <span class="summary-item">
                        <i class="fas fa-square" style="color: #f44336;"></i> Tinggi: <?php echo $high_count; ?>
                    </span>
                    <span class="summary-item">
                        <i class="fas fa-square" style="color: #ff9800;"></i> Sedang: <?php echo $total_pending - $high_count; ?>
                    </span>
                </div>
            </div>
            
            <div class="pending-tasks-list" id="tasksList">
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    // Hitung hari tersisa
                    $deadline_date = new DateTime($row['deadline']);
                    $today_date = new DateTime();
                    $interval = $today_date->diff($deadline_date);
                    $days_left = $interval->days;
                    $is_overdue = $interval->invert && $days_left > 0;
                    
                    if ($is_overdue) {
                        $days_left = -$days_left;
                    }
                    
                    // Tentukan status deadline
                    $deadline_status = 'normal';
                    $deadline_text = '';
                    
                    if ($is_overdue) {
                        $deadline_status = 'overdue';
                        $deadline_text = 'Terlambat ' . $days_left . ' hari';
                    } elseif ($days_left == 0) {
                        $deadline_status = 'urgent';
                        $deadline_text = 'Hari ini!';
                    } elseif ($days_left <= 3) {
                        $deadline_status = 'warning';
                        $deadline_text = $days_left . ' hari lagi';
                    } else {
                        $deadline_text = $days_left . ' hari';
                    }
                    
                    // Class untuk prioritas
                    $priority_class = $row['prioritas'] ?? 'sedang';
                    $priority_text = ucfirst($priority_class);
                    
                    // Class untuk kategori
                    $category_class = !empty($row['kategori']) ? $row['kategori'] : 'none';
                ?>
                    <div class="task-item <?php echo $priority_class; ?> <?php echo $deadline_status; ?>" 
                         data-priority="<?php echo $priority_class; ?>"
                         data-category="<?php echo $category_class; ?>"
                         data-days="<?php echo $is_overdue ? '-' . $days_left : $days_left; ?>">
                        
                        <!-- Task Checkbox -->
                        <div class="task-checkbox">
                            <input type="checkbox" 
                                   class="task-select"
                                   data-task-id="<?php echo $row['id']; ?>"
                                   id="task-<?php echo $row['id']; ?>">
                            <label for="task-<?php echo $row['id']; ?>"></label>
                        </div>
                        
                        <!-- Task Details -->
                        <div class="task-details">
                            <div class="task-header">
                                <h4 class="task-title"><?php echo htmlspecialchars($row['judul']); ?></h4>
                                <div class="task-tags">
                                    <?php if (!empty($row['kategori'])): ?>
                                        <span class="task-tag category">
                                            <i class="fas fa-tag"></i> <?php echo ucfirst($row['kategori']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="task-tag priority <?php echo $priority_class; ?>">
                                        <i class="fas fa-flag"></i> <?php echo $priority_text; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if (!empty($row['deskripsi'])): ?>
                                <p class="task-description">
                                    <?php echo htmlspecialchars(substr($row['deskripsi'], 0, 200)); ?>
                                    <?php if (strlen($row['deskripsi']) > 200): ?>...<?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="task-footer">
                                <div class="deadline-info <?php echo $deadline_status; ?>">
                                    <i class="fas fa-calendar"></i>
                                    <span class="deadline-date">
                                        <?php echo date('d M Y', strtotime($row['deadline'])); ?>
                                    </span>
                                    <span class="deadline-status">
                                        <?php echo $deadline_text; ?>
                                    </span>
                                </div>
                                
                                <div class="task-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-calendar-plus"></i>
                                        Dibuat: <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Task Actions -->
                        <div class="task-actions">
                            <div class="quick-actions">
                                <a href="../proses.php?toggle_status=<?php echo $row['id']; ?>" 
                                   class="btn-action complete-btn"
                                   title="Tandai Selesai">
                                    <i class="fas fa-check"></i>
                                    <span class="action-text">Selesai</span>
                                </a>
                                
                                <a href="tambah-tugas.php?edit=<?php echo $row['id']; ?>" 
                                   class="btn-action edit-btn"
                                   title="Edit Tugas">
                                    <i class="fas fa-edit"></i>
                                    <span class="action-text">Edit</span>
                                </a>
                                
                                <a href="../proses.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn-action delete-btn"
                                   title="Hapus Tugas"
                                   onclick="return confirm('Yakin menghapus tugas ini?')">
                                    <i class="fas fa-trash"></i>
                                    <span class="action-text">Hapus</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Selected Tasks Actions -->
            <div class="selected-actions" id="selectedActions" style="display: none;">
                <div class="selected-info">
                    <span id="selectedCount">0</span> tugas dipilih
                </div>
                <div class="selected-buttons">
                    <button class="btn-mark-selected" id="markSelectedBtn">
                        <i class="fas fa-check"></i> Tandai yang Dipilih sebagai Selesai
                    </button>
                    <button class="btn-clear-selection" id="clearSelectionBtn">
                        <i class="fas fa-times"></i> Batalkan Pilihan
                    </button>
                </div>
            </div>
            
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>🎉 Tidak ada tugas pending!</h3>
                <p>Semua tugas Anda sudah selesai. Anda bisa:</p>
                <div class="empty-actions">
                    <a href="tambah-tugas.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Tugas Baru
                    </a>
                    <a href="tugas-selesai.php" class="btn-secondary">
                        <i class="fas fa-history"></i> Lihat Riwayat Selesai
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Tips Section -->
    <?php if ($total_pending > 0): ?>
    <div class="tips-section">
        <h4><i class="fas fa-lightbulb"></i> Tips Produktivitas</h4>
        <div class="tips-grid">
            <div class="tip-card">
                <i class="fas fa-bolt"></i>
                <p>Mulai dari tugas dengan prioritas <strong>Tinggi</strong> terlebih dahulu</p>
            </div>
            <div class="tip-card">
                <i class="fas fa-calendar-times"></i>
                <p>Selesaikan tugas yang <strong>terlambat</strong> secepat mungkin</p>
            </div>
            <div class="tip-card">
                <i class="fas fa-hourglass-end"></i>
                <p>Tugas dengan deadline <strong>3 hari lagi</strong> perlu perhatian khusus</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
// Tampilkan notifikasi jika ada
if (isset($_GET['completed'])) {
    echo '<script>showNotification("Tugas berhasil ditandai selesai!", "success");</script>';
}
?>
