<?php
// pages/tambah-tugas.php - FINAL FIXED VERSION
$page_title = "Tambah Tugas Baru";
$css_file = "form.css";
$js_file = "form.js";

include '../includes/header.php';
include '../includes/koneksi.php';

// Edit mode
$edit_mode = false;
$task_data = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $task_id = intval($_GET['edit']);
    $query = "SELECT * FROM tugas WHERE id = $task_id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $task_data = mysqli_fetch_assoc($result);
        $page_title = "Edit Tugas: " . htmlspecialchars(substr($task_data['judul'], 0, 50));
    } else {
        header('Location: semua-tugas.php');
        exit();
    }
}
?>

<div class="form-page">
    <div class="form-header">
        <h1 class="page-title">

        </h1>
        <p class="page-subtitle">
            <?php echo $edit_mode 
                ? 'Perbarui informasi tugas Anda' 
                : 'Isi formulir di bawah untuk menambahkan tugas baru'; ?>
        </p>
    </div>

    <div class="form-wrapper">
        <!-- Form Container -->
        <div class="form-container">
            <form id="taskForm" action="../proses.php" method="POST" novalidate>
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $task_data['id']; ?>">
                    <input type="hidden" name="edit" value="1">
                <?php else: ?>
                    <input type="hidden" name="tambah" value="1">
                <?php endif; ?>
                
                <div class="form-grid">
                    <!-- Kolom Kiri -->
                    <div class="form-column">
                        <!-- Judul Tugas -->
                        <div class="form-group">
                            <label for="judul" class="form-label required">
                                <i class="fas fa-heading"></i> Judul Tugas
                            </label>
                            <input type="text" 
                                   id="judul" 
                                   name="judul" 
                                   class="form-input" 
                                   placeholder="Masukkan judul tugas"
                                   value="<?php echo $edit_mode ? htmlspecialchars($task_data['judul']) : ''; ?>"
                                   required
                                   data-minlength="3"
                                   data-maxlength="200">
                            <div class="form-hint">Minimal 3 karakter, maksimal 200 karakter</div>
                            <div class="error-message" id="judul-error"></div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-group">
                            <label for="deskripsi" class="form-label">
                                <i class="fas fa-align-left"></i> Deskripsi
                            </label>
                            <textarea id="deskripsi" 
                                      name="deskripsi" 
                                      class="form-textarea" 
                                      rows="6"
                                      placeholder="Deskripsikan tugas secara detail..."
                                      data-maxlength="1000"><?php echo $edit_mode ? htmlspecialchars($task_data['deskripsi']) : ''; ?></textarea>
                            <div class="form-hint">Opsional. Maksimal 1000 karakter</div>
                            <div class="char-counter">
                                <span id="charCount">0</span>/1000 karakter
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="form-column">
                        <!-- Deadline -->
                        <div class="form-group">
                            <label for="deadline" class="form-label required">
                                <i class="fas fa-calendar-day"></i> Deadline
                            </label>
                            <div class="date-input-wrapper">
                                <input type="date" 
                                       id="deadline" 
                                       name="deadline" 
                                       class="form-input"
                                       value="<?php echo $edit_mode ? $task_data['deadline'] : date('Y-m-d', strtotime('+7 days')); ?>"
                                       required
                                       min="<?php echo date('Y-m-d'); ?>">
                                <i class="fas fa-calendar-alt date-icon"></i>
                            </div>
                            <div class="form-hint">Tanggal harus hari ini atau setelahnya</div>
                            <div class="error-message" id="deadline-error"></div>
                        </div>

                        <!-- Kategori -->
                        <div class="form-group">
                            <label for="kategori" class="form-label">
                                <i class="fas fa-tag"></i> Kategori
                            </label>
                            <select id="kategori" name="kategori" class="form-select">
                                <option value="" <?php echo (!$edit_mode) ? 'selected' : ''; ?>>Pilih Kategori</option>
                                <option value="kerja" <?php echo ($edit_mode && $task_data['kategori'] == 'kerja') ? 'selected' : ''; ?>>Pekerjaan</option>
                                <option value="pribadi" <?php echo ($edit_mode && $task_data['kategori'] == 'pribadi') ? 'selected' : ''; ?>>Pribadi</option>
                                <option value="belajar" <?php echo ($edit_mode && $task_data['kategori'] == 'belajar') ? 'selected' : ''; ?>>Belajar</option>
                                <option value="hobi" <?php echo ($edit_mode && $task_data['kategori'] == 'hobi') ? 'selected' : ''; ?>>Hobi</option>
                                <option value="lainnya" <?php echo ($edit_mode && $task_data['kategori'] == 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                            <div class="form-hint">Kategorikan tugas Anda (opsional)</div>
                        </div>

                        <!-- Prioritas -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-exclamation-circle"></i> Prioritas
                            </label>
                            <div class="priority-options">
                                <label class="priority-option low">
                                    <input type="radio" name="prioritas" value="rendah" 
                                        <?php echo (!$edit_mode || ($edit_mode && $task_data['prioritas'] == 'rendah')) ? 'checked' : ''; ?>>
                                    <span class="priority-dot"></span>
                                    <span class="priority-label">Rendah</span>
                                </label>
                                
                                <label class="priority-option medium">
                                    <input type="radio" name="prioritas" value="sedang" 
                                        <?php echo ($edit_mode && $task_data['prioritas'] == 'sedang') ? 'checked' : ''; ?>>
                                    <span class="priority-dot"></span>
                                    <span class="priority-label">Sedang</span>
                                </label>
                                
                                <label class="priority-option high">
                                    <input type="radio" name="prioritas" value="tinggi" 
                                        <?php echo ($edit_mode && $task_data['prioritas'] == 'tinggi') ? 'checked' : ''; ?>>
                                    <span class="priority-dot"></span>
                                    <span class="priority-label">Tinggi</span>
                                </label>
                            </div>
                        </div>

                        <!-- Status (hanya untuk edit mode) -->
                        <?php if ($edit_mode): ?>
                        <div class="form-group">
                            <label for="status" class="form-label">
                                <i class="fas fa-check-circle"></i> Status
                            </label>
                            <div class="status-toggle-group">
                                <label class="switch">
                                    <input type="checkbox" 
                                           id="statusToggle" 
                                           name="status" 
                                           value="Selesai"
                                           <?php echo ($task_data['status'] == 'Selesai') ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="status-label" id="statusLabel">
                                    <?php echo ($task_data['status'] == 'Selesai') ? 'Selesai' : 'Pending'; ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas <?php echo $edit_mode ? 'fa-save' : 'fa-plus-circle'; ?>"></i>
                        <?php echo $edit_mode ? 'Simpan Perubahan' : 'Tambah Tugas'; ?>
                    </button>
                    
                    <a href="semua-tugas.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    
                    <?php if ($edit_mode): ?>
                    <a href="../proses.php?delete=<?php echo $task_data['id']; ?>" 
                       class="btn-delete"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                        <i class="fas fa-trash"></i> Hapus Tugas
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Preview Card -->
        <div class="preview-section">
            <h3><i class="fas fa-eye"></i> Preview Tugas</h3>
            <div class="preview-card">
                <div class="preview-header">
                    <h4 id="previewTitle">Judul Tugas</h4>
                    <span class="preview-status" id="previewStatus">Pending</span>
                </div>
                <div class="preview-body">
                    <p id="previewDesc">Deskripsi tugas akan muncul di sini...</p>
                    <div class="preview-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span id="previewDeadline"><?php echo date('d M Y', strtotime('+7 days')); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span id="previewCategory">-</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="previewPriority">Sedang</span>
                        </div>
                    </div>
                </div>
            </div>
            <p class="preview-note">Preview akan diperbarui saat Anda mengisi form</p>
        </div>
    </div>
</div>

<?php
// Tampilkan pesan sukses/error
if (isset($_GET['success'])) {
    echo '<script>showNotification("Tugas berhasil ditambahkan!", "success");</script>';
}
if (isset($_GET['updated'])) {
    echo '<script>showNotification("Tugas berhasil diperbarui!", "success");</script>';
}
if (isset($_GET['error'])) {
    echo '<script>showNotification("Terjadi kesalahan. Silakan coba lagi.", "error");</script>';
}
?>

<?php include '../includes/footer.php'; ?>