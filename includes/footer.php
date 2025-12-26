<?php
// includes/footer.php - KODE FINAL YANG BERFUNGSI
?>
            </div> <!-- end content-wrapper -->
        </div> <!-- end main-content -->
    </div> <!-- end container -->

    <!-- JavaScript Global -->
    <script src="<?php echo $root_path; ?>assets/js/main.js"></script>
    
    <?php if (isset($js_file)): ?>
        <script src="<?php echo $root_path; ?>assets/js/<?php echo $js_file; ?>"></script>
    <?php endif; ?>
    
    <script>
    // SIDEBAR TOGGLE - SIMPLE & WORKING VERSION
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing sidebar...');
        
        // Get elements
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarClose = document.getElementById('sidebarClose');
        
        // Debug: Check if elements exist
        console.log('Elements:', {
            mobileToggle: mobileToggle ? 'Found' : 'Not found',
            sidebar: sidebar ? 'Found' : 'Not found',
            overlay: overlay ? 'Found' : 'Not found',
            sidebarClose: sidebarClose ? 'Found' : 'Not found'
        });
        
        // Toggle function - SIMPLE VERSION
        function toggleSidebar() {
            console.log('Toggling sidebar...');
            
            // Toggle sidebar
            sidebar.classList.toggle('mobile-open');
            sidebar.classList.toggle('mobile-closed');
            
            // Toggle overlay
            overlay.classList.toggle('active');
            
            // Toggle body class for mobile
            if (window.innerWidth <= 768) {
                document.body.classList.toggle('sidebar-open');
            }
            
            // Update toggle button icon
            if (mobileToggle) {
                if (sidebar.classList.contains('mobile-open')) {
                    mobileToggle.innerHTML = '<i class="fas fa-times"></i>';
                    mobileToggle.setAttribute('aria-label', 'Close sidebar');
                    console.log('Sidebar opened');
                } else {
                    mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
                    mobileToggle.setAttribute('aria-label', 'Open sidebar');
                    console.log('Sidebar closed');
                }
            }
        }
        
        // Initialize sidebar based on screen size
        function initSidebar() {
            console.log('Initializing sidebar for screen width:', window.innerWidth);
            
            if (window.innerWidth <= 768) {
                // Mobile: Hide sidebar by default, show toggle button
                if (sidebar) {
                    sidebar.classList.add('mobile-closed');
                    sidebar.classList.remove('mobile-open');
                }
                if (overlay) overlay.classList.remove('active');
                if (mobileToggle) {
                    mobileToggle.style.display = 'flex';
                    mobileToggle.setAttribute('aria-label', 'Open sidebar');
                }
                if (sidebarClose) sidebarClose.style.display = 'flex';
                console.log('Mobile mode initialized');
            } else {
                // Desktop: Show sidebar, hide toggle button
                if (sidebar) {
                    sidebar.classList.remove('mobile-closed', 'mobile-open');
                }
                if (overlay) overlay.classList.remove('active');
                if (mobileToggle) mobileToggle.style.display = 'none';
                if (sidebarClose) sidebarClose.style.display = 'none';
                document.body.classList.remove('sidebar-open');
                console.log('Desktop mode initialized');
            }
        }
        
        // Add event listeners
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
            console.log('Toggle button listener added');
        }
        
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
            });
            console.log('Overlay listener added');
        }
        
        if (sidebarClose) {
            sidebarClose.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
            });
            console.log('Close button listener added');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('mobile-open') &&
                !sidebar.contains(e.target) && 
                e.target !== mobileToggle &&
                !overlay.contains(e.target)) {
                toggleSidebar();
            }
        });
        
        // Close sidebar with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
                toggleSidebar();
            }
        });
        
        // Initialize on load
        initSidebar();
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                initSidebar();
            }, 250);
        });
        
        // Close sidebar when clicking nav items on mobile
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    setTimeout(toggleSidebar, 300);
                }
            });
        });
        
        console.log('Sidebar initialization complete!');
    });
    
    </script>
</body>

</html>

