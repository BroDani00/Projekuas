// assets/js/completed.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('Completed tasks page loaded');
    
    // Elements
    const timeFilter = document.getElementById('timeFilter');
    const speedFilter = document.getElementById('speedFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const completionItems = document.querySelectorAll('.completion-item');
    const exportButtons = document.querySelectorAll('.btn-export');
    const pageButtons = document.querySelectorAll('.page-btn, .page-number');
    
    // Filter functionality
    function applyFilters() {
        const time = timeFilter ? timeFilter.value : 'all';
        const speed = speedFilter ? speedFilter.value : 'all';
        const priority = priorityFilter ? priorityFilter.value : 'all';
        
        completionItems.forEach(item => {
            const itemDate = item.dataset.date;
            const itemDays = parseInt(item.dataset.days);
            const itemPriority = item.dataset.priority;
            
            let show = true;
            
            // Time filter
            if (time !== 'all') {
                const itemDateObj = new Date(itemDate);
                const today = new Date();
                
                switch (time) {
                    case 'today':
                        if (itemDate !== today.toISOString().split('T')[0]) show = false;
                        break;
                    case 'yesterday':
                        const yesterday = new Date();
                        yesterday.setDate(yesterday.getDate() - 1);
                        if (itemDate !== yesterday.toISOString().split('T')[0]) show = false;
                        break;
                    case 'week':
                        const weekAgo = new Date();
                        weekAgo.setDate(weekAgo.getDate() - 7);
                        if (new Date(itemDate) < weekAgo) show = false;
                        break;
                    case 'month':
                        const monthAgo = new Date();
                        monthAgo.setMonth(monthAgo.getMonth() - 1);
                        if (new Date(itemDate) < monthAgo) show = false;
                        break;
                }
            }
            
            // Speed filter
            if (speed !== 'all') {
                switch (speed) {
                    case 'fast':
                        if (itemDays > 1) show = false;
                        break;
                    case 'medium':
                        if (itemDays <= 1 || itemDays > 3) show = false;
                        break;
                    case 'slow':
                        if (itemDays <= 3) show = false;
                        break;
                }
            }
            
            // Priority filter
            if (priority !== 'all' && itemPriority !== priority) {
                show = false;
            }
            
            // Show/hide with animation
            if (show) {
                item.style.display = 'grid';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 10);
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });
        
        updateVisibleCount();
    }
    
    // Update visible task count
    function updateVisibleCount() {
        const visibleItems = Array.from(completionItems).filter(item => 
            item.style.display !== 'none'
        ).length;
        
        // Update count display if exists
        const countElement = document.querySelector('.page-info');
        if (countElement && visibleItems > 0) {
            countElement.textContent = `Menampilkan 1-${Math.min(visibleItems, 10)} dari ${visibleItems} tugas`;
        }
    }
    
    // Export functionality
    if (exportButtons.length > 0) {
        exportButtons.forEach(button => {
            button.addEventListener('click', function() {
                const format = this.dataset.format;
                
                switch (format) {
                    case 'print':
                        window.print();
                        break;
                        
                    case 'summary':
                        generateSummaryReport();
                        break;
                }
            });
        });
    }
    
    // Generate summary report
    function generateSummaryReport() {
        const totalTasks = completionItems.length;
        const fastTasks = Array.from(completionItems).filter(item => 
            item.classList.contains('fast')
        ).length;
        const mediumTasks = Array.from(completionItems).filter(item => 
            item.classList.contains('medium')
        ).length;
        const slowTasks = Array.from(completionItems).filter(item => 
            item.classList.contains('slow')
        ).length;
        
        const reportWindow = window.open('', '_blank');
        reportWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Ringkasan Produktivitas</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { color: #333; }
                    .report-header { text-align: center; margin-bottom: 30px; }
                    .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
                    .stat-card { background: #f9f9f9; padding: 20px; border-radius: 8px; text-align: center; }
                    .stat-value { font-size: 32px; font-weight: bold; color: #4CAF50; }
                    .stat-label { color: #666; margin-top: 5px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <div class="report-header">
                    <h1>📊 Ringkasan Produktivitas</h1>
                    <p>Dibuat pada: ${new Date().toLocaleDateString('id-ID', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    })}</p>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value">${totalTasks}</div>
                        <div class="stat-label">Total Tugas Selesai</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${fastTasks}</div>
                        <div class="stat-label">Tugas Cepat (≤1 hari)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${mediumTasks}</div>
                        <div class="stat-label">Tugas Sedang (2-3 hari)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${slowTasks}</div>
                        <div class="stat-label">Tugas Lambat (>3 hari)</div>
                    </div>
                </div>
                
                <h3>📈 Statistik Kecepatan Penyelesaian</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cepat (≤1 hari)</td>
                            <td>${fastTasks}</td>
                            <td>${totalTasks > 0 ? ((fastTasks / totalTasks * 100).toFixed(1)) : 0}%</td>
                        </tr>
                        <tr>
                            <td>Sedang (2-3 hari)</td>
                            <td>${mediumTasks}</td>
                            <td>${totalTasks > 0 ? ((mediumTasks / totalTasks * 100).toFixed(1)) : 0}%</td>
                        </tr>
                        <tr>
                            <td>Lambat (>3 hari)</td>
                            <td>${slowTasks}</td>
                            <td>${totalTasks > 0 ? ((slowTasks / totalTasks * 100).toFixed(1)) : 0}%</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td>Total</td>
                            <td>${totalTasks}</td>
                            <td>100%</td>
                        </tr>
                    </tbody>
                </table>
                
                <div style="margin-top: 30px; padding: 20px; background: #f0f8ff; border-radius: 8px;">
                    <h4>💡 Insight:</h4>
                    <p>${getInsightMessage(fastTasks, totalTasks)}</p>
                </div>
            </body>
            </html>
        `);
        reportWindow.document.close();
    }
    
    // Get insight message based on stats
    function getInsightMessage(fastTasks, totalTasks) {
        if (totalTasks === 0) {
            return "Belum ada data produktivitas. Mulai selesaikan tugas pertama Anda!";
        }
        
        const fastPercentage = (fastTasks / totalTasks) * 100;
        
        if (fastPercentage >= 70) {
            return "🎉 Excellent! Anda sangat produktif dengan sebagian besar tugas selesai dalam 1 hari.";
        } else if (fastPercentage >= 50) {
            return "👍 Good job! Lebih dari setengah tugas selesai dengan cepat.";
        } else if (fastPercentage >= 30) {
            return "👌 Solid progress! Terus pertahankan momentum Anda.";
        } else {
            return "💪 Keep going! Coba fokus pada penyelesaian tugas lebih cepat.";
        }
    }
    
    // Pagination functionality
    if (pageButtons.length > 0) {
        pageButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (this.classList.contains('page-number')) {
                    // Remove active class from all page numbers
                    document.querySelectorAll('.page-number').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Simulate page change (in real app, this would load new data)
                    simulatePageChange(parseInt(this.textContent));
                } else if (this.classList.contains('prev')) {
                    navigatePage(-1);
                } else if (this.classList.contains('next')) {
                    navigatePage(1);
                }
            });
        });
    }
    
    // Simulate page change
    function simulatePageChange(page) {
        showNotification(`Memuat halaman ${page}...`, 'info');
        
        // In a real app, you would make an AJAX request here
        setTimeout(() => {
            showNotification(`Halaman ${page} dimuat`, 'success');
        }, 500);
    }
    
    // Navigate pages
    function navigatePage(direction) {
        const activePage = document.querySelector('.page-number.active');
        const currentPage = parseInt(activePage.textContent);
        const newPage = currentPage + direction;
        
        if (newPage >= 1 && newPage <= 5) { // Assuming 5 pages max
            // Update active page
            document.querySelectorAll('.page-number').forEach(btn => {
                btn.classList.remove('active');
                if (parseInt(btn.textContent) === newPage) {
                    btn.classList.add('active');
                }
            });
            
            simulatePageChange(newPage);
        }
    }
    
    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                               type === 'error' ? 'fa-exclamation-circle' : 
                               type === 'warning' ? 'fa-exclamation-triangle' : 
                               'fa-info-circle'}"></i>
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
    }
    
    // Apply filters on change
    if (timeFilter) timeFilter.addEventListener('change', applyFilters);
    if (speedFilter) speedFilter.addEventListener('change', applyFilters);
    if (priorityFilter) priorityFilter.addEventListener('change', applyFilters);
    
    // Initialize
    updateVisibleCount();
    
    // Celebration effect if there are many completed tasks
    const completedCount = completionItems.length;
    if (completedCount >= 10) {
        setTimeout(() => {
            showNotification(
                `🎉 Luar biasa! Anda telah menyelesaikan ${completedCount} tugas!`,
                'success'
            );
        }, 2000);
    }
});