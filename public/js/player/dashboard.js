// Simplified Player Dashboard JavaScript - Matching Homepage Simplicity
// Basic interactions only, similar to homepage functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeSimplePlayerDashboard();
});

// Simple dashboard initialization
function initializeSimplePlayerDashboard() {
    // Simple sidebar toggle
    initializeSimpleSidebar();
    
    // Basic navigation
    initializeSimpleNavigation();
    
    // Simple card animations (like homepage)
    setTimeout(animateCards, 300);
    
    // Simple button interactions
    initializeSimpleButtons();
}

// Simple sidebar functionality (similar to homepage button interactions)
function initializeSimpleSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('playerSidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('sidebar-open');
                }
            }
        });
    }
}

// Simple navigation (similar to homepage anchor scrolling)
function initializeSimpleNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const href = this.getAttribute('href');
            
            // Only handle hash links for internal sections
            if (href && href.startsWith('#')) {
                // Remove active class from all items
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.closest('.nav-item').classList.add('active');
            }
            // Let normal links navigate naturally (like homepage buttons)
        });
    });
}

// Simple card animations (similar to homepage card animations)
function animateCards() {
    const cards = document.querySelectorAll('.stat-card, .schedule-section, .quick-actions, .profile-section');
    
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Simple button interactions (like homepage buttons)
function initializeSimpleButtons() {
    const actionButtons = document.querySelectorAll('.action-btn');
    
    actionButtons.forEach(button => {
        // Add simple hover feedback
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Simple click feedback
        button.addEventListener('click', function() {
            this.style.transform = 'translateY(0)';
            setTimeout(() => {
                this.style.transform = 'translateY(-1px)';
            }, 100);
        });
    });
}

// Simple scroll effects (similar to homepage)
window.addEventListener('scroll', function() {
    const header = document.querySelector('.dashboard-header');
    if (header && window.scrollY > 50) {
        header.style.boxShadow = '0 4px 15px rgba(0,0,0,0.15)';
    } else if (header) {
        header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    }
});
