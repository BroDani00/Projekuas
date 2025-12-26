<?php
// pages/semua-tugas.php - DENGAN SEARCH FUNCTIONALITY
$page_title = "Semua Tugas";
$css_file = "tasks.css";
$js_file = "tasks.js";

include '../includes/header.php';
include '../includes/koneksi.php';

// Handle search from form (jika menggunakan form GET)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query dengan search filter
$query = "SELECT * FROM tugas WHERE 1=1";

if (!empty($search)) {
    $search_clean = mysqli_real_escape_string($conn, $search);
    $query .= " AND (judul LIKE '%$search_clean%' OR 
                     deskripsi LIKE '%$search_clean%' OR 
                     kategori LIKE '%$search_clean%')";
}

$query .= " ORDER BY deadline ASC, created_at DESC";
$result = mysqli_query($conn, $query);

$total_tasks = mysqli_num_rows($result);
?>

<div class="content-wrapper">
    <div class="all-tasks-page">
        <!-- Page Header -->
        <div class="page-header">
            <p class="page-subtitle"><?php echo $total_tasks; ?> tugas ditemukan</p>
        </div>
        
        <!-- Search and Filter Section -->
      
        
        <!-- Tasks Container -->
        <div class="tasks-container">
            <?php if ($total_tasks > 0): ?>
                <div class="tasks-grid" id="tasksGrid">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="task-card" 
                             data-status="<?php echo strtolower($row['status']); ?>"
                             data-priority="<?php echo $row['prioritas']; ?>"
                             data-category="<?php echo $row['kategori']; ?>">
                            
                            <!-- Task Header -->
                            <div class="task-header">
                                <h3 class="task-title"><?php echo htmlspecialchars($row['judul']); ?></h3>
                                <span class="task-status <?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </div>
                            
                            <!-- Task Body -->
                            <div class="task-body">
                                <?php if (!empty($row['deskripsi'])): ?>
                                <p class="task-desc"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                                <?php endif; ?>
                                
                                <div class="task-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span class="deadline">
                                            <?php echo date('d M Y', strtotime($row['deadline'])); ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($row['kategori'])): ?>
                                    <div class="meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span class="category"><?php echo ucfirst($row['kategori']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="meta-item">
                                        <span class="priority-badge <?php echo $row['prioritas']; ?>">
                                            <i class="fas fa-flag"></i>
                                            <?php echo ucfirst($row['prioritas']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Task Actions -->
                            <div class="task-actions">
                                <a href="../proses.php?toggle_status=<?php echo $row['id']; ?>" 
                                   class="btn-action status-toggle">
                                    <i class="fas fa-check"></i>
                                    <?php echo $row['status'] == 'Selesai' ? 'Batalkan' : 'Selesai'; ?>
                                </a>
                                
                                <a href="tambah-tugas.php?edit=<?php echo $row['id']; ?>" 
                                   class="btn-action edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Tidak ada tugas</h3>
                    <p><?php echo !empty($search) ? 'Coba dengan kata kunci lain' : 'Mulai dengan menambahkan tugas baru'; ?></p>
                    <a href="tambah-tugas.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Tugas
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Additional search functionality for semua-tugas.php
document.addEventListener('DOMContentLoaded', function() {
    // Filter buttons functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const taskCards = document.querySelectorAll('.task-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filter tasks
            taskCards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = '';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>