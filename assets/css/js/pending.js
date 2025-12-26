// assets/js/pending.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pending tasks page loaded');
    
    // Elements
    const priorityFilter = document.getElementById('priorityFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const timeFilter = document.getElementById('timeFilter');
    const taskItems = document.querySelectorAll('.task-item');
    const taskCheckboxes = document.querySelectorAll('.task-select');
    const markAllBtn = document.getElementById('markAllBtn');
    const markSelectedBtn = document.getElementById('markSelectedBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const selectedActions = document.getElementById('selectedActions');
    const selectedCount = document.getElementById('selectedCount');
    
    // Selected tasks array
    let selectedTasks = [];
    
    // Filter functionality
    function applyFilters() {
        const priority = priorityFilter ? priorityFilter.value : 'all';
        const category = categoryFilter ? categoryFilter.value : 'all';
        const time = timeFilter ? timeFilter.value : 'all';
        
        taskItems.forEach(task => {
            const taskPriority = task.dataset.priority;
            const taskCategory = task.dataset.category;
            const taskDays = parseInt(task.dataset.days);
            
            let show = true;
            
            // Priority filter
            if (priority !== 'all' && taskPriority !== priority) {
                show = false;
            }
            
            // Category filter
            if (category !== 'all' && taskCategory !== category) {
                show = false;
            }
            
            // Time filter
            if (time !== 'all') {
                switch (time) {
                    case 'today':
                        if (taskDays !== 0) show = false;
                        break;
                    case 'week':
                        if (taskDays > 7) show = false;
                        break;
                    case 'overdue':
                        if (taskDays >= 0) show = false;
                        break;
                }
            }
            
            // Show/hide with animation
            if (show) {
                task.style.display = 'grid';
                setTimeout(() => {
                    task.style.opacity = '1';
                    task.style.transform = 'translateY(0)';
                }, 10);
            } else {
                task.style.opacity = '0';
                task.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    task.style.display = 'none';
                }, 300);
            }
        });
        
        updateTaskCount();
    }
    
    // Update visible task count
    function updateTaskCount() {
        const visibleTasks = Array.from(taskItems).filter(task => 
            task.style.display !== 'none'
        ).length;
        
        // Update count display if exists
        const countElement = document.querySelector('.tasks-summary .visible-count');
        if (countElement) {
            countElement.textContent = `${visibleTasks} tugas`;
        }
    }
    
    // Checkbox functionality
    if (taskCheckboxes.length > 0) {
        taskCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.dataset.taskId;
                
                if (this.checked) {
                    selectedTasks.push(taskId);
                    this.closest('.task-item').classList.add('selected');
                } else {
                    selectedTasks = selectedTasks.filter(id => id !== taskId);
                    this.closest('.task-item').classList.remove('selected');
                }
                
                updateSelectedUI();
            });
        });
    }
    
    // Update selected tasks UI
    function updateSelectedUI() {
        const count = selectedTasks.length;
        
        if (count > 0) {
            selectedActions.style.display = 'flex';
            selectedCount.textContent = count;
            
            // Highlight selected tasks
            taskItems.forEach(task => {
                const checkbox = task.querySelector('.task-select');
                if (checkbox && checkbox.checked) {
                    task.style.boxShadow = '0 0 0 3px rgba(255, 77, 109, 0.2)';
                    task.style.border = '2px solid var(--accent-color)';
                } else {
                    task.style.boxShadow = '';
                    task.style.border = '';
                }
            });
        } else {
            selectedActions.style.display = 'none';
        }
    }
    
    // Mark all as complete
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            if (confirm('Tandai SEMUA tugas sebagai selesai?')) {
                showLoading(this, 'Memproses...');
                
                // Mark all tasks
                const taskIds = Array.from(taskItems).map(task => {
                    const checkbox = task.querySelector('.task-select');
                    return checkbox ? checkbox.dataset.taskId : null;
                }).filter(id => id !== null);
                
                // Send requests
                markTasksAsComplete(taskIds, true);
            }
        });
    }
    
    // Mark selected as complete
    if (markSelectedBtn) {
        markSelectedBtn.addEventListener('click', function() {
            if (selectedTasks.length === 0) {
                showNotification('Pilih setidaknya satu tugas', 'warning');
                return;
            }
            
            if (confirm(`Tandai ${selectedTasks.length} tugas yang dipilih sebagai selesai?`)) {
                showLoading(this, 'Memproses...');
                markTasksAsComplete(selectedTasks, false);
            }
        });
    }
    
    // Clear selection
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            selectedTasks = [];
            taskCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.closest('.task-item').classList.remove('selected');
            });
            updateSelectedUI();
        });
    }
    
    // Mark tasks as complete via AJAX
    function markTasksAsComplete(taskIds, isAll) {
        let completed = 0;
        const total = taskIds.length;
        
        taskIds.forEach((taskId, index) => {
            setTimeout(() => {
                fetch(`../proses.php?toggle_status=${taskId}`)
                    .then(response => {
                        if (response.ok) {
                            completed++;
                            
                            // Update UI for this task
                            const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
                            if (taskElement) {
                                taskElement.closest('.task-item').style.opacity = '0.5';
                            }
                            
                            // If all done, reload
                            if (completed === total) {
                                setTimeout(() => {
                                    showNotification(
                                        isAll ? 'Semua tugas ditandai selesai!' : `${total} tugas ditandai selesai!`,
                                        'success'
                                    );
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                }, 500);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan', 'error');
                    });
            }, index * 100); // Stagger requests
        });
    }
    
    // Show loading on button
    function showLoading(button, text) {
        const originalHTML = button.innerHTML;
        button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${text}`;
        button.disabled = true;
        
        // Restore after 3 seconds if still loading
        setTimeout(() => {
            if (button.disabled) {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        }, 3000);
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
        
        // Add styles if not exists
        if (!document.querySelector('#notification-styles-pending')) {
            const style = document.createElement('style');
            style.id = 'notification-styles-pending';
            style.textContent = `
                .notification {
                    position: fixed;
                    bottom: 20px;
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
                    animation: slideInUp 0.3s ease;
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
                
                .notification.warning {
                    border-left-color: #ff9800;
                }
                
                .notification.info {
                    border-left-color: #2196f3;
                }
                
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                
                .notification-content i {
                    font-size: 20px;
                }
                
                .notification.success i { color: #4CAF50; }
                .notification.error i { color: #f44336; }
                .notification.warning i { color: #ff9800; }
                .notification.info i { color: #2196f3; }
                
                .notification-close {
                    background: none;
                    border: none;
                    color: #999;
                    cursor: pointer;
                    padding: 5px;
                    font-size: 14px;
                }
                
                @keyframes slideInUp {
                    from { transform: translateY(100%); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                
                .notification.hide {
                    animation: slideOutDown 0.3s ease forwards;
                }
                
                @keyframes slideOutDown {
                    to { transform: translateY(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // Apply filters on change
    if (priorityFilter) priorityFilter.addEventListener('change', applyFilters);
    if (categoryFilter) categoryFilter.addEventListener('change', applyFilters);
    if (timeFilter) timeFilter.addEventListener('change', applyFilters);
    
    // Initialize
    updateTaskCount();
    
    // Check for overdue tasks notification
    const overdueTasks = document.querySelectorAll('.task-item.overdue');
    if (overdueTasks.length > 0) {
        setTimeout(() => {
            showNotification(
                `Anda memiliki ${overdueTasks.length} tugas terlambat!`,
                'warning'
            );
        }, 1000);
    }
});