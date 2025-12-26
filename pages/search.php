<?php
// PAGES/SEARCH.PHP - VERSI DENGAN DESAIN MODERN
$page_title = "Hasil Pencarian";
include '../includes/header.php';
include '../includes/koneksi.php';

// Ambil kata kunci
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<div class="search-results-page">
    <!-- HEADER HASIL PENCARIAN -->
    <div class="page-header" style="background: var(--gradient-pink); padding: 30px; border-radius: 15px; margin-bottom: 30px; color: white;">
        <h1><i class="fas fa-search"></i> Hasil Pencarian</h1>
        <p class="search-subtitle">
            <?php if(!empty($keyword)): ?>
                Menampilkan hasil untuk: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong>
            <?php else: ?>
                Masukkan kata kunci di kotak pencarian
            <?php endif; ?>
        </p>
    </div>

    <!-- HASIL PENCARIAN -->
    <div class="results-container">
        <?php if(!empty($keyword)): 
            $sql = "SELECT * FROM tugas 
                    WHERE judul LIKE '%$keyword%' 
                    OR deskripsi LIKE '%$keyword%'
                    ORDER BY deadline ASC";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) > 0): 
        ?>
            <div class="results-count" style="margin-bottom: 20px; color: var(--text-light);">
                <i class="fas fa-list-check"></i> 
                Ditemukan <?php echo mysqli_num_rows($result); ?> tugas
            </div>

            <div class="tasks-grid">
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $deadline_class = (strtotime($row['deadline']) < time()) ? 'overdue' : '';
                ?>
                <div class="task-card-search">
                    <div class="task-card-header">
                        <h3 class="task-title"><?php echo htmlspecialchars($row['judul']); ?></h3>
                        <span class="task-status <?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </div>
                    
                    <p class="task-description"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                    
                    <div class="task-card-footer">
                        <div class="deadline-info <?php echo $deadline_class; ?>">
                            <i class="fas fa-calendar-day"></i>
                            <span>Deadline: <?php echo date('d F Y', strtotime($row['deadline'])); ?></span>
                        </div>
                        <div class="task-actions">
                            <a href="semua-tugas.php?edit=<?php echo $row['id']; ?>" class="btn-edit-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
        <?php else: ?>
            <!-- TIDAK ADA HASIL -->
            <div class="no-results" style="text-align: center; padding: 60px 20px;">
                <div class="no-results-icon" style="font-size: 80px; color: var(--secondary-color); margin-bottom: 20px;">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h3 style="color: var(--text-dark); margin-bottom: 10px;">Tidak ditemukan</h3>
                <p style="color: var(--text-light); max-width: 500px; margin: 0 auto 30px;">
                    Tidak ada tugas yang cocok dengan "<strong><?php echo htmlspecialchars($keyword); ?></strong>"
                </p>
                <a href="tambah-tugas.php" class="btn-pink">
                    <i class="fas fa-plus"></i> Tambah Tugas Baru
                </a>
            </div>
        <?php endif; ?>
        
        <?php else: ?>
            <!-- KEYWORD KOSONG -->
            <div class="no-results" style="text-align: center; padding: 60px 20px;">
                <div class="no-results-icon" style="font-size: 80px; color: var(--secondary-color); margin-bottom: 20px;">
                    <i class="fas fa-search"></i>
                </div>
                <h3 style="color: var(--text-dark); margin-bottom: 10px;">Mulai pencarian Anda</h3>
                <p style="color: var(--text-light); max-width: 500px; margin: 0 auto;">
                    Gunakan kotak pencarian di atas untuk mencari tugas berdasarkan judul atau deskripsi
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>