// assets/js/main.js - VERSI LENGKAP DENGAN SEARCH
document.addEventListener('DOMContentLoaded', function() {
    console.log('Main JS loaded - initializing search...');
    
    // ========== SEARCH FUNCTIONALITY ==========
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        console.log('Search input found, adding functionality...');
        
        // Real-time search with debounce
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performSearch(this.value.trim());
            }, 300); // 300ms delay
        });
        
        // Clear search on 'x' button click
        searchInput.addEventListener('search', function() {
            if (this.value === '') {
                performSearch('');
            }
        });
        
        // Add clear button dynamically
        searchInput.addEventListener('focus', function() {
            addClearButton(this);
        });
        
        // Perform search function
        function performSearch(searchTerm) {
            console.log('Searching for:', searchTerm);
            
            // Get all task cards on current page
            let taskCards;
            
            // Cari task cards berdasarkan halaman yang aktif
            if (document.querySelector('.tasks-grid')) {
                taskCards = document.querySelectorAll('.tasks-grid .task-card');
            } else if (document.querySelector('.pending-tasks-list')) {
                taskCards = document.querySelectorAll('.pending-tasks-list .pending-task-card');
            } else if (document.querySelector('.completed-tasks-list')) {
                taskCards = document.querySelectorAll('.completed-tasks-list .completed-task-card');
            } else if (document.querySelector('.tasks-list')) {
                taskCards = document.querySelectorAll('.tasks-list .task-item');
            } else {
                console.log('No task cards found on this page');
                return;
            }
            
            console.log('Found', taskCards.length, 'task cards');
            
            // Jika search kosong, tampilkan semua
            if (!searchTerm) {
                taskCards.forEach(card => {
                    card.style.display = '';
                    card.style.opacity = '1';
                });
                return;
            }
            
            const searchLower = searchTerm.toLowerCase();
            let foundCount = 0;
            
            // Filter tasks
            taskCards.forEach(card => {
                // Cari text dalam task card
                const title = card.querySelector('.task-title, h3, h4')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.task-desc, p, .task-details')?.textContent.toLowerCase() || '';
                const category = card.querySelector('.category, .task-category')?.textContent.toLowerCase() || '';
                
                // Cek apakah cocok dengan search term
                const matches = title.includes(searchLower) || 
                               description.includes(searchLower) || 
                               category.includes(searchLower);
                
                if (matches) {
                    card.style.display = '';
                    card.style.opacity = '1';
                    card.style.animation = 'fadeIn 0.3s ease';
                    foundCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            console.log('Found', foundCount, 'matching tasks');
            
            // Tampilkan "no results" message jika perlu
            showNoResultsMessage(foundCount === 0 && searchTerm !== '');
        }
        
        // Add clear button to search input
        function addClearButton(inputElement) {
            const parent = inputElement.parentElement;
            if (!parent.querySelector('.clear-search-btn')) {
                const clearBtn = document.createElement('button');
                clearBtn.className = 'clear-search-btn';
                clearBtn.innerHTML = '<i class="fas fa-times"></i>';
                clearBtn.setAttribute('aria-label', 'Clear search');
                clearBtn.setAttribute('title', 'Clear search');
                clearBtn.style.cssText = `
                    background: none;
                    border: none;
                    color: var(--accent-color);
                    cursor: pointer;
                    padding: 5px;
                    margin-left: 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;
                
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.focus();
                    performSearch('');
                    this.remove();
                });
                
                parent.appendChild(clearBtn);
                
                // Hapus clear button jika search kosong saat blur
                searchInput.addEventListener('blur', function() {
                    if (!this.value && clearBtn.parentNode) {
                        setTimeout(() => clearBtn.remove(), 200);
                    }
                });
            }
        }
        
        // Show no results message
        function showNoResultsMessage(show) {
            const container = document.querySelector('.tasks-container, .tasks-grid, .content-wrapper');
            if (!container) return;
            
            let noResultsMsg = container.querySelector('.no-results-message');
            
            if (show) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'no-results-message';
                    noResultsMsg.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: var(--text-light);">
                            <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                            <h3>Tidak ditemukan</h3>
                            <p>Tidak ada tugas yang cocok dengan pencarian Anda</p>
                        </div>
                    `;
                    container.appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = 'block';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        }
        
        console.log('Search functionality initialized');
    }
    
    // ========== SIDEBAR FUNCTIONALITY ==========
    // ... (kode sidebar yang sudah ada) ...
    
    // ========== ANIMATIONS ==========
    // Add fadeIn animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .task-card, .pending-task-card, .completed-task-card, .task-item {
            animation: fadeIn 0.3s ease;
        }
    `;
    document.head.appendChild(style);
});