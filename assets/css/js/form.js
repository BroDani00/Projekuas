// assets/js/form.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('Form validation loaded');
    
    // Elements
    const form = document.getElementById('taskForm');
    const judulInput = document.getElementById('judul');
    const deadlineInput = document.getElementById('deadline');
    const deskripsiTextarea = document.getElementById('deskripsi');
    const charCount = document.getElementById('charCount');
    const statusToggle = document.getElementById('statusToggle');
    const statusLabel = document.getElementById('statusLabel');
    const submitBtn = document.getElementById('submitBtn');
    
    // Preview elements
    const previewTitle = document.getElementById('previewTitle');
    const previewDesc = document.getElementById('previewDesc');
    const previewDeadline = document.getElementById('previewDeadline');
    const previewStatus = document.getElementById('previewStatus');
    const previewCategory = document.getElementById('previewCategory');
    const previewPriority = document.getElementById('previewPriority');
    
    // Initialize
    if (deadlineInput) {
        const today = new Date().toISOString().split('T')[0];
        deadlineInput.min = today;
    }
    
    // Character counter
    if (deskripsiTextarea && charCount) {
        updateCharCount();
        
        deskripsiTextarea.addEventListener('input', function() {
            updateCharCount();
            updatePreviewDesc();
        });
    }
    
    // Status toggle
    if (statusToggle && statusLabel) {
        statusToggle.addEventListener('change', function() {
            statusLabel.textContent = this.checked ? 'Selesai' : 'Pending';
            updatePreviewStatus();
        });
    }
    
    // Real-time validation and preview updates
    if (judulInput) {
        judulInput.addEventListener('input', function() {
            validateJudul();
            updatePreviewTitle();
        });
    }
    
    if (deadlineInput) {
        deadlineInput.addEventListener('change', function() {
            validateDeadline();
            updatePreviewDeadline();
        });
    }
    
    // Category and priority updates
    const kategoriSelect = document.getElementById('kategori');
    if (kategoriSelect) {
        kategoriSelect.addEventListener('change', updatePreviewCategory);
    }
    
    document.querySelectorAll('input[name="prioritas"]').forEach(radio => {
        radio.addEventListener('change', updatePreviewPriority);
    });
    
    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                showLoading();
                setTimeout(() => {
                    form.submit();
                }, 1000);
            }
        });
    }
    
    // Validation functions
    function validateJudul() {
        if (!judulInput) return true;
        
        const value = judulInput.value.trim();
        const errorElement = document.getElementById('judul-error');
        
        if (value.length < 3) {
            showError(judulInput, errorElement, 'Judul harus minimal 3 karakter');
            return false;
        }
        
        if (value.length > 200) {
            showError(judulInput, errorElement, 'Judul maksimal 200 karakter');
            return false;
        }
        
        clearError(judulInput, errorElement);
        return true;
    }
    
    function validateDeadline() {
        if (!deadlineInput) return true;
        
        const value = deadlineInput.value;
        const errorElement = document.getElementById('deadline-error');
        const today = new Date().toISOString().split('T')[0];
        
        if (!value) {
            showError(deadlineInput, errorElement, 'Harap pilih deadline');
            return false;
        }
        
        if (value < today) {
            showError(deadlineInput, errorElement, 'Deadline tidak boleh dari hari kemarin');
            return false;
        }
        
        clearError(deadlineInput, errorElement);
        return true;
    }
    
    function validateForm() {
        const isJudulValid = validateJudul();
        const isDeadlineValid = validateDeadline();
        
        return isJudulValid && isDeadlineValid;
    }
    
    function showError(input, errorElement, message) {
        if (!input || !errorElement) return;
        
        input.classList.add('invalid');
        input.classList.remove('valid');
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
    
    function clearError(input, errorElement) {
        if (!input || !errorElement) return;
        
        input.classList.remove('invalid');
        input.classList.add('valid');
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    }
    
    function updateCharCount() {
        if (!deskripsiTextarea || !charCount) return;
        
        const length = deskripsiTextarea.value.length;
        charCount.textContent = length;
        
        if (length > 1000) {
            deskripsiTextarea.value = deskripsiTextarea.value.substring(0, 1000);
            charCount.textContent = 1000;
            charCount.style.color = '#ff4757';
        } else if (length > 900) {
            charCount.style.color = '#ff9800';
        } else {
            charCount.style.color = 'var(--accent-color)';
        }
    }
    
    // Preview update functions
    function updatePreviewTitle() {
        if (previewTitle && judulInput) {
            const title = judulInput.value.trim() || 'Judul Tugas';
            previewTitle.textContent = title.length > 50 ? title.substring(0, 50) + '...' : title;
        }
    }
    
    function updatePreviewDesc() {
        if (previewDesc && deskripsiTextarea) {
            const desc = deskripsiTextarea.value.trim() || 'Deskripsi tugas akan muncul di sini...';
            previewDesc.textContent = desc.length > 150 ? desc.substring(0, 150) + '...' : desc;
        }
    }
    
    function updatePreviewDeadline() {
        if (previewDeadline && deadlineInput.value) {
            const date = new Date(deadlineInput.value);
            previewDeadline.textContent = date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
    }
    
    function updatePreviewCategory() {
        if (previewCategory && kategoriSelect) {
            const selected = kategoriSelect.options[kategoriSelect.selectedIndex];
            previewCategory.textContent = selected.value ? selected.text : '-';
        }
    }
    
    function updatePreviewPriority() {
        if (previewPriority) {
            const selected = document.querySelector('input[name="prioritas"]:checked');
            if (selected) {
                previewPriority.textContent = selected.value.charAt(0).toUpperCase() + selected.value.slice(1);
                previewPriority.className = `priority-${selected.value}`;
            }
        }
    }
    
    function updatePreviewStatus() {
        if (previewStatus && statusToggle) {
            previewStatus.textContent = statusToggle.checked ? 'Selesai' : 'Pending';
            previewStatus.className = statusToggle.checked ? 'preview-status completed' : 'preview-status';
        }
    }
    
    function showLoading() {
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        }
    }
    
    // Initialize preview
    updatePreviewTitle();
    updatePreviewDesc();
    updatePreviewCategory();
    updatePreviewPriority();
    updatePreviewStatus();
    
    // Auto-resize textarea
    if (deskripsiTextarea) {
        deskripsiTextarea.style.height = 'auto';
        deskripsiTextarea.style.height = (deskripsiTextarea.scrollHeight) + 'px';
        
        deskripsiTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    
    // Notification function
    window.showNotification = function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close"><i class="fas fa-times"></i></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('hide');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', function() {
            notification.classList.add('hide');
            setTimeout(() => notification.remove(), 300);
        });
        
        // Add styles if not exists
        if (!document.querySelector('#notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 15px;
                    z-index: 10000;
                    animation: slideIn 0.3s ease;
                    border-left: 4px solid;
                    min-width: 300px;
                    max-width: 400px;
                }
                
                .notification.success {
                    border-left-color: #4CAF50;
                }
                
                .notification.error {
                    border-left-color: #f44336;
                }
                
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                
                .notification-content i {
                    font-size: 20px;
                }
                
                .notification.success i {
                    color: #4CAF50;
                }
                
                .notification.error i {
                    color: #f44336;
                }
                
                .notification-close {
                    background: none;
                    border: none;
                    color: #999;
                    cursor: pointer;
                    padding: 5px;
                    font-size: 14px;
                }
                
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                
                .notification.hide {
                    animation: slideOut 0.3s ease forwards;
                }
                
                @keyframes slideOut {
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    };
});