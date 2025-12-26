<?php
// pages/tugas-selesai.php - VERSI DIPERBAIKI
$page_title = "Tugas Selesai"; // HARUS DI SINI SEBELUM header.php
$css_file = "completed.css";
$js_file = "completed.js";

include '../includes/header.php';
include '../includes/koneksi.php';

// Query hanya tugas selesai dengan detail lengkap
$query = "SELECT *, 
          DATEDIFF(updated_at, created_at) as days_to_complete,
          DATE(updated_at) as completion_date
          FROM tugas 
          WHERE status = 'Selesai' 
          ORDER BY updated_at DESC";

$result = mysqli_query($conn, $query);
$total_completed = mysqli_num_rows($result);

// Hitung statistik
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$week_ago = date('Y-m-d', strtotime('-7 days'));
$month_ago = date('Y-m-d', strtotime('-30 days'));

// Tugas selesai hari ini
$query_today = "SELECT COUNT(*) as today FROM tugas 
                WHERE status = 'Selesai' 
                AND DATE(updated_at) = '$today'";
$result_today = mysqli_query($conn, $query_today);
$today_count = mysqli_fetch_assoc($result_today)['today'] ?? 0;

// Tugas selesai kemarin
$query_yesterday = "SELECT COUNT(*) as yesterday FROM tugas 
                    WHERE status = 'Selesai' 
                    AND DATE(updated_at) = '$yesterday'";
$result_yesterday = mysqli_query($conn, $query_yesterday);
$yesterday_count = mysqli_fetch_assoc($result_yesterday)['yesterday'] ?? 0;

// Tugas selesai minggu ini
$query_week = "SELECT COUNT(*) as week FROM tugas 
               WHERE status = 'Selesai' 
               AND updated_at >= '$week_ago'";
$result_week = mysqli_query($conn, $query_week);
$week_count = mysqli_fetch_assoc($result_week)['week'] ?? 0;

// Hitung rata-rata waktu penyelesaian
$query_avg = "SELECT AVG(DATEDIFF(updated_at, created_at)) as avg_days 
              FROM tugas WHERE status = 'Selesai'";
$result_avg = mysqli_query($conn, $query_avg);
$avg_days = mysqli_fetch_assoc($result_avg)['avg_days'] ?? 0;
$avg_days_formatted = number_format($avg_days, 1);

// Helper function untuk time ago
function time_ago($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' menit yang lalu';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' jam yang lalu';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' hari yang lalu';
    } else {
        return date('d M Y', $time);
    }
}

// Helper function untuk badge kecepatan
function get_speed_badge($days) {
    if ($days <= 1) {
        return '<span class="speed-badge fast"><i class="fas fa-bolt"></i> Cepat</span>';
    } elseif ($days <= 3) {
        return '<span class="speed-badge medium"><i class="fas fa-tachometer-alt"></i> Sedang</span>';
    } else {
        return '<span class="speed-badge slow"><i class="fas fa-turtle"></i> Lambat</span>';
    }
}

// Helper function untuk achievement
function get_achievement_level($total) {
    if ($total >= 100) {
        return ['level' => 'Legend', 'icon' => 'fa-crown', 'color' => '#FFD700'];
    } elseif ($total >= 50) {
        return ['level' => 'Master', 'icon' => 'fa-trophy', 'color' => '#C0C0C0'];
    } elseif ($total >= 25) {
        return ['level' => 'Advanced', 'icon' => 'fa-medal', 'color' => '#CD7F32'];
    } elseif ($total >= 10) {
        return ['level' => 'Intermediate', 'icon' => 'fa-star', 'color' => '#4CAF50'];
    } else {
        return ['level' => 'Beginner', 'icon' => 'fa-seedling', 'color' => '#2196F3'];
    }
}

$achievement = get_achievement_level($total_completed);
?>

<div class="completed-page">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-check-circle"></i>
        </h1>
        <p class="page-subtitle"><?php echo $total_completed; ?> tugas telah berhasil diselesaikan</p>
    </div>

    <!-- Achievement Banner -->
    <div class="achievement-banner" style="background: linear-gradient(135deg, <?php echo $achievement['color']; ?>, #ffffff);">
        <div class="achievement-content">
            <div class="achievement-icon">
                <i class="fas <?php echo $achievement['icon']; ?>"></i>
            </div>
            <div class="achievement-info">
                <h3>Tingkat: <?php echo $achievement['level']; ?></h3>
                <p>Anda telah menyelesaikan <?php echo $total_completed; ?> tugas</p>
            </div>
            <div class="achievement-stats">
                <div class="stat-item">
                    <span class="stat-value"><?php echo $avg_days_formatted; ?></span>
                    <span class="stat-label">Hari rata-rata</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $today_count; ?></span>
                    <span class="stat-label">Selesai hari ini</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="performance-stats">
        <div class="stat-card today-stat">
            <div class="stat-icon">
                <i class="fas fa-sun"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $today_count; ?></h3>
                <p>Selesai Hari Ini</p>
                <?php if ($yesterday_count > 0): ?>
                    <span class="stat-trend <?php echo $today_count >= $yesterday_count ? 'up' : 'down'; ?>">
                        <i class="fas fa-arrow-<?php echo $today_count >= $yesterday_count ? 'up' : 'down'; ?>"></i>
                        <?php echo abs($today_count - $yesterday_count); ?> dari kemarin
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="stat-card week-stat">
            <div class="stat-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $week_count; ?></h3>
                <p>7 Hari Terakhir</p>
                <span class="stat-trend up">
                    <i class="fas fa-chart-line"></i> Progress mingguan
                </span>
            </div>
        </div>
        
        <div class="stat-card avg-stat">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $avg_days_formatted; ?></h3>
                <p>Hari Rata-rata</p>
                <span class="stat-trend">
                    <i class="fas fa-history"></i> Waktu penyelesaian
                </span>
            </div>
        </div>
        
        <div class="stat-card total-stat">
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $total_completed; ?></h3>
                <p>Total Selesai</p>
                <span class="stat-trend up">
                    <i class="fas fa-award"></i> Achievement
                </span>
            </div>
        </div>
    </div>

    <!-- Daftar Tugas Selesai -->
    <div class="completed-tasks-container">
        <div class="tasks-header">
            <h3><i class="fas fa-history"></i> Riwayat Penyelesaian</h3>
            <div class="tasks-summary">
                <span class="summary-item">
                    <i class="fas fa-check" style="color: #4CAF50;"></i> Total: <?php echo $total_completed; ?>
                </span>
                <span class="summary-item">
                    <i class="fas fa-clock" style="color: #2196F3;"></i> Rata-rata: <?php echo $avg_days_formatted; ?> hari
                </span>
            </div>
        </div>
        
        <?php if ($total_completed > 0): ?>
            <div class="completed-tasks-list" id="tasksList">
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $days_to_complete = $row['days_to_complete'] ?? 0;
                    $completion_date = $row['updated_at'] ?? $row['created_at'];
                    $time_ago = time_ago($completion_date);
                    $completion_day = date('l, d F Y', strtotime($completion_date));
                    $completion_time = date('H:i', strtotime($completion_date));
                    
                    // Tentukan kecepatan
                    $speed_class = 'medium';
                    if ($days_to_complete <= 1) $speed_class = 'fast';
                    if ($days_to_complete > 3) $speed_class = 'slow';
                    
                    // Format durasi
                    $duration_text = $days_to_complete == 0 ? 'Kurang dari 1 hari' : $days_to_complete . ' hari';
                    
                    // Prioritas
                    $priority_class = $row['prioritas'] ?? 'sedang';
                    $priority_text = ucfirst($priority_class);
                ?>
                    <div class="completion-item <?php echo $speed_class; ?>" 
                         data-date="<?php echo date('Y-m-d', strtotime($completion_date)); ?>"
                         data-days="<?php echo $days_to_complete; ?>"
                         data-priority="<?php echo $priority_class; ?>">
                        
                        <!-- Completion Timeline -->
                        <div class="completion-timeline">
                            <div class="timeline-date">
                                <div class="date-day"><?php echo date('d', strtotime($completion_date)); ?></div>
                                <div class="date-month"><?php echo date('M', strtotime($completion_date)); ?></div>
                            </div>
                            <div class="timeline-connector"></div>
                        </div>
                        
                        <!-- Completion Card -->
                        <div class="completion-card">
                            <div class="card-header">
                                <div class="header-left">
                                    <h4 class="task-title"><?php echo htmlspecialchars($row['judul']); ?></h4>
                                    <div class="task-meta">
                                        <?php if (!empty($row['kategori'])): ?>
                                            <span class="meta-tag category">
                                                <i class="fas fa-tag"></i> <?php echo ucfirst($row['kategori']); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <span class="meta-tag priority <?php echo $priority_class; ?>">
                                            <i class="fas fa-flag"></i> <?php echo $priority_text; ?>
                                        </span>
                                        
                                        <?php echo get_speed_badge($days_to_complete); ?>
                                    </div>
                                </div>
                                
                                <div class="header-right">
                                    <span class="completion-time">
                                        <i class="far fa-clock"></i> <?php echo $completion_time; ?>
                                    </span>
                                    <span class="time-ago"><?php echo $time_ago; ?></span>
                                </div>
                            </div>
                            
                            <?php if (!empty($row['deskripsi'])): ?>
                                <div class="card-description">
                                    <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-footer">
                                <div class="footer-left">
                                    <div class="completion-details">
                                        <span class="detail-item">
                                            <i class="fas fa-calendar-check"></i>
                                            <strong>Selesai:</strong> <?php echo $completion_day; ?>
                                        </span>
                                        
                                        <span class="detail-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <strong>Deadline:</strong> <?php echo date('d M Y', strtotime($row['deadline'])); ?>
                                        </span>
                                        
                                        <span class="detail-item">
                                            <i class="fas fa-hourglass-half"></i>
                                            <strong>Durasi:</strong> <?php echo $duration_text; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="footer-right">
                                    <div class="action-buttons">
                                        <a href="../proses.php?toggle_status=<?php echo $row['id']; ?>" 
                                           class="btn-action undo-btn"
                                           title="Batalkan Selesai"
                                           onclick="return confirm('Batalkan status selesai untuk tugas ini?')">
                                            <i class="fas fa-undo"></i>
                                            <span>Batalkan</span>
                                        </a>
                                        
                                        <a href="tambah-tugas.php?edit=<?php echo $row['id']; ?>" 
                                           class="btn-action edit-btn"
                                           title="Edit Tugas">
                                            <i class="fas fa-edit"></i>
                                            <span>Edit</span>
                                        </a>
                                        
                                        <a href="../proses.php?delete=<?php echo $row['id']; ?>" 
                                           class="btn-action delete-btn"
                                           title="Hapus Tugas"
                                           onclick="return confirm('Hapus tugas ini dari riwayat?')">
                                            <i class="fas fa-trash"></i>
                                            <span>Hapus</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_completed > 10): ?>
            <div class="pagination-container">
                <nav class="pagination">
                    <button class="page-btn prev" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <div class="page-numbers">
                        <button class="page-number active">1</button>
                        <?php if ($total_completed > 20): ?>
                            <button class="page-number">2</button>
                            <button class="page-number">3</button>
                            <span class="page-dots">...</span>
                            <button class="page-number">5</button>
                        <?php endif; ?>
                    </div>
                    
                    <button class="page-btn next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </nav>
                
                <div class="page-info">
                    Menampilkan 1-10 dari <?php echo $total_completed; ?> tugas
                </div>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>📭 Belum ada riwayat penyelesaian</h3>
                <p>Mulai selesaikan tugas Anda untuk melihat pencapaian di sini!</p>
                <div class="empty-actions">
                    <a href="tugas-pending.php" class="btn-primary">
                        <i class="fas fa-tasks"></i> Lihat Tugas Pending
                    </a>
                    <a href="tambah-tugas.php" class="btn-secondary">
                        <i class="fas fa-plus-circle"></i> Buat Tugas Baru
                    </a>
                </div>
                <div class="empty-tips">
                    <p><strong>Tips:</strong> Tandai tugas sebagai "Selesai" untuk mulai membangun riwayat produktivitas Anda.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Insights Section -->
    <?php if ($total_completed > 0): ?>
    <div class="insights-section">
        <h3><i class="fas fa-chart-line"></i> Insights & Analytics</h3>
        
        <div class="insights-grid">
            <div class="insight-card productivity">
                <div class="insight-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="insight-content">
                    <h4>Produktivitas Harian</h4>
                    <p>Rata-rata <?php echo $today_count; ?> tugas per hari</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo min($today_count * 10, 100); ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="insight-card efficiency">
                <div class="insight-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="insight-content">
                    <h4>Efisiensi Waktu</h4>
                    <p><?php echo $avg_days_formatted; ?> hari per tugas</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo min(100 - ($avg_days * 10), 100); ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="insight-card consistency">
                <div class="insight-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="insight-content">
                    <h4>Konsistensi</h4>
                    <p><?php echo $week_count; ?> tugas dalam 7 hari terakhir</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo min(($week_count / 10) * 100, 100); ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="insight-card next-goal">
                <div class="insight-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="insight-content">
                    <h4>Goal Selanjutnya</h4>
                    <p><?php echo max(0, 25 - $total_completed); ?> menuju Level Intermediate</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo min(($total_completed / 25) * 100, 100); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
// Tampilkan notifikasi jika ada
if (isset($_GET['undo'])) {
    echo '<script>showNotification("Tugas berhasil dikembalikan ke pending!", "success");</script>';
}
?>


<?php include '../includes/footer.php'; ?>