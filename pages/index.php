<?php
// pages/index.php - DASHBOARD 
$page_title = "Dashboard";
$css_file = "dashboard.css";
$js_file = "dashboard.js";

include '../includes/header.php';
include '../includes/koneksi.php';

// Query untuk statistik
$query_total = "SELECT COUNT(*) as total FROM tugas";
$query_pending = "SELECT COUNT(*) as pending FROM tugas WHERE status = 'Pending'";
$query_selesai = "SELECT COUNT(*) as selesai FROM tugas WHERE status = 'Selesai'";

$result_total = mysqli_query($conn, $query_total);
$result_pending = mysqli_query($conn, $query_pending);
$result_selesai = mysqli_query($conn, $query_selesai);

$total = mysqli_fetch_assoc($result_total)['total'];
$pending = mysqli_fetch_assoc($result_pending)['pending'];
$selesai = mysqli_fetch_assoc($result_selesai)['selesai'];

// Tugas deadline mendekat (3 hari ke depan)
$today = date('Y-m-d');
$three_days = date('Y-m-d', strtotime('+3 days'));
$query_upcoming = "SELECT COUNT(*) as upcoming FROM tugas 
                   WHERE deadline BETWEEN '$today' AND '$three_days' 
                   AND status = 'Pending'";
$result_upcoming = mysqli_query($conn, $query_upcoming);
$upcoming = mysqli_fetch_assoc($result_upcoming)['upcoming'];

// Tugas terbaru
$query_recent = "SELECT * FROM tugas ORDER BY created_at DESC LIMIT 5";
$result_recent = mysqli_query($conn, $query_recent);

// Tugas dengan deadline terdekat
$query_nearest = "SELECT * FROM tugas 
                  WHERE status = 'Pending' 
                  AND deadline >= '$today'
                  ORDER BY deadline ASC 
                  LIMIT 5";
$result_nearest = mysqli_query($conn, $query_nearest);
?>

<div class="dashboard-page">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="page-title"></h1>
            <p class="page-subtitle">Kelola tugas harian Anda dengan mudah</p>
        </div>
        <div class="header-actions">
            <a href="tambah-tugas.php" class="btn-add-task">
                <i class="fas fa-plus-circle"></i> Tambah Tugas Baru
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $total; ?></h3>
                <p>Total Tugas</p>
            </div>
            <div class="stat-trend">
                <i class="fas fa-chart-line"></i> Semua tugas
            </div>
        </div>

        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $pending; ?></h3>
                <p>Tugas Pending</p>
            </div>
            <div class="stat-trend">
                <i class="fas fa-exclamation-circle"></i> Perlu perhatian
            </div>
        </div>

        <div class="stat-card completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $selesai; ?></h3>
                <p>Tugas Selesai</p>
            </div>
            <div class="stat-trend">
                <i class="fas fa-trophy"></i> <?php echo $total > 0 ? round(($selesai/$total)*100) : 0; ?>% selesai
            </div>
        </div>

        <div class="stat-card upcoming">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $upcoming; ?></h3>
                <p>Deadline Mendekat</p>
            </div>
            <div class="stat-trend">
                <i class="fas fa-running"></i> 3 hari ke depan
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Recent Tasks -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Tugas Terbaru</h3>
                <a href="semua-tugas.php" class="view-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_recent) > 0): ?>
                    <div class="tasks-list">
                        <?php while ($row = mysqli_fetch_assoc($result_recent)): ?>
                            <div class="task-item">
                                <div class="task-info">
                                    <h4 class="task-title"><?php echo htmlspecialchars($row['judul']); ?></h4>
                                    <div class="task-meta">
                                        <span class="task-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                        </span>
                                        <span class="task-priority <?php echo $row['prioritas']; ?>">
                                            <i class="fas fa-flag"></i>
                                            <?php echo ucfirst($row['prioritas']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="task-status <?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-tasks"></i>
                        <p>Belum ada tugas</p>
                        <a href="tambah-tugas.php" class="btn-primary">Tambah Tugas Pertama</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-calendar-alt"></i> Deadline Terdekat</h3>
                <span class="date-range"><?php echo date('d M') . ' - ' . date('d M', strtotime('+7 days')); ?></span>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result_nearest) > 0): ?>
                    <div class="deadlines-list">
                        <?php while ($row = mysqli_fetch_assoc($result_nearest)): 
                            $days_left = floor((strtotime($row['deadline']) - strtotime($today)) / 86400);
                            $urgency = $days_left <= 1 ? 'high' : ($days_left <= 3 ? 'medium' : 'low');
                        ?>
                            <div class="deadline-item urgency-<?php echo $urgency; ?>">
                                <div class="deadline-date">
                                    <div class="date-day"><?php echo date('d', strtotime($row['deadline'])); ?></div>
                                    <div class="date-month"><?php echo date('M', strtotime($row['deadline'])); ?></div>
                                </div>
                                <div class="deadline-details">
                                    <h4><?php echo htmlspecialchars($row['judul']); ?></h4>
                                    <div class="deadline-meta">
                                        <span class="days-left">
                                            <i class="fas fa-clock"></i>
                                            <?php 
                                            if ($days_left == 0) echo 'Hari ini';
                                            elseif ($days_left == 1) echo 'Besok';
                                            else echo $days_left . ' hari lagi';
                                            ?>
                                        </span>
                                        <span class="priority-badge <?php echo $row['prioritas']; ?>">
                                            <?php echo ucfirst($row['prioritas']); ?>
                                        </span>
                                    </div>
                                </div>
                                <a href="tambah-tugas.php?edit=<?php echo $row['id']; ?>" class="deadline-action">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <p>Tidak ada deadline mendekat</p>
                        <p class="text-muted">Semua tugas sudah selesai atau belum ada deadline</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>