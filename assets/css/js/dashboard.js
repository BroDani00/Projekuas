// assets/js/dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats counter
    function animateCounter(element, target, duration = 1000) {
        const start = 0;
        const increment = target / (duration / 16); // 60fps
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 16);
    }
    
    // Animate all stats
    const statValues = document.querySelectorAll('.stat-content h3');
    statValues.forEach(stat => {
        const target = parseInt(stat.textContent);
        if (!isNaN(target)) {
            stat.textContent = '0';
            setTimeout(() => {
                animateCounter(stat, target);
            }, 500);
        }
    });
    
    // Progress bar animation
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 800);
    });
    
    // Task completion toggle
    const taskCheckboxes = document.querySelectorAll('.task-check input[type="checkbox"]');
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const taskItem = this.closest('.task-item');
            
            if (this.checked) {
                taskItem.classList.add('completed');
                
                // Send AJAX request to update status
                fetch(`../proses.php?toggle_status=${taskId}`)
                    .then(response => response.text())
                    .then(data => {
                        console.log(`Task ${taskId} marked as complete`);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    });
    
    // Deadline urgency indicator
    function updateDeadlineUrgency() {
        const deadlineItems = document.querySelectorAll('.deadline-item');
        const now = new Date();
        
        deadlineItems.forEach(item => {
            const daysText = item.querySelector('.days-left')?.textContent;
            if (daysText) {
                if (daysText.includes('Hari ini') || daysText.includes('Besok')) {
                    item.classList.add('urgent-pulse');
                    
                    // Add pulsing animation
                    item.style.animation = 'pulse 2s infinite';
                }
            }
        });
    }
    
    // Add pulsing animation for urgent deadlines
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(244, 67, 54, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(244, 67, 54, 0); }
            100% { box-shadow: 0 0 0 0 rgba(244, 67, 54, 0); }
        }
        
        .urgent-pulse {
            position: relative;
        }
        
        .urgent-pulse::before {
            content: '';
            position: absolute;
            top: 5px;
            right: 5px;
            width: 8px;
            height: 8px;
            background: #f44336;
            border-radius: 50%;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    `;
    document.head.appendChild(style);
    
    // Update urgency on load
    updateDeadlineUrgency();
    
    // Auto-refresh dashboard every 30 seconds
    setInterval(() => {
        // Only refresh if user is active on dashboard
        if (!document.hidden) {
            location.reload();
        }
    }, 30000);
    
    // Chart.js integration (optional)
    if (typeof Chart !== 'undefined') {
        const ctx = document.getElementById('statsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Pending', 'Mendatang'],
                    datasets: [{
                        data: [
                            document.querySelector('.stat-card.completed h3')?.textContent || 0,
                            document.querySelector('.stat-card.pending h3')?.textContent || 0,
                            document.querySelector('.stat-card.upcoming h3')?.textContent || 0
                        ],
                        backgroundColor: [
                            '#4CAF50',
                            '#FF9800',
                            '#2196F3'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
    
    // Add greeting based on time
    function updateGreeting() {
        const hour = new Date().getHours();
        let greeting = 'Selamat ';
        
        if (hour < 12) greeting += 'Pagi';
        else if (hour < 15) greeting += 'Siang';
        else if (hour < 19) greeting += 'Sore';
        else greeting += 'Malam';
        
        const greetingElement = document.querySelector('.greeting');
        if (greetingElement) {
            greetingElement.textContent = greeting;
        }
    }
    
    // Initialize greeting
    updateGreeting();
});