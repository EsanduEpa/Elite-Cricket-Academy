// Profile Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeProfilePage();
    highlightActiveSidebarLink();
});

function initializeProfilePage() {
    // Initialize profile page functionality
    addSmoothScrolling();
    
    // Add table row hover effects
    enhanceTableInteractions();
    
    // Add statistics counter animation
    animateStatistics();
    
    // Add profile picture interaction
    enhanceProfilePicture();
    
    // Add responsive table functionality
    makeTablesResponsive();
}

function highlightActiveSidebarLink() {
    const links = document.querySelectorAll('.profile-sidebar .sidebar-link');
    const currentPath = window.location.pathname.replace(/\/+$/, '');
    links.forEach(link => {
        const hrefPath = link.getAttribute('href').replace(window.location.origin, '').replace(/\/+$/, '');
        if (hrefPath === currentPath) {
            link.classList.add('active');
        }
    });
}

function addSmoothScrolling() {
    // Smooth scroll to sections when clicking on navigation
    const navLinks = document.querySelectorAll('nav a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

function enhanceTableInteractions() {
    // Add click functionality to table rows
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            // Remove active class from all rows
            tableRows.forEach(r => r.classList.remove('active-row'));
            
            // Add active class to clicked row
            this.classList.add('active-row');
            
            // Add subtle animation
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
}

function animateStatistics() {
    // Animate statistics numbers on scroll
    const statCards = document.querySelectorAll('.stat-card');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statValue = entry.target.querySelector('.stat-value');
                const finalValue = statValue.textContent;
                
                // Animate the number counting up
                animateNumber(statValue, finalValue);
                
                // Stop observing after animation
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    statCards.forEach(card => {
        observer.observe(card);
    });
}

function animateNumber(element, finalValue) {
    const isDecimal = finalValue.includes('.');
    const finalNumber = parseFloat(finalValue.replace(/,/g, ''));
    const duration = 2000; // 2 seconds
    const steps = 60;
    const stepValue = finalNumber / steps;
    let currentStep = 0;
    
    const timer = setInterval(() => {
        currentStep++;
        const currentValue = stepValue * currentStep;
        
        if (currentStep >= steps) {
            element.textContent = finalValue;
            clearInterval(timer);
        } else {
            if (isDecimal) {
                element.textContent = currentValue.toFixed(2);
            } else {
                element.textContent = Math.floor(currentValue).toLocaleString();
            }
        }
    }, duration / steps);
}

function enhanceProfilePicture() {
    const profilePic = document.querySelector('.profile-picture img');
    
    if (profilePic) {
        // Add click to enlarge functionality
        profilePic.addEventListener('click', function() {
            createImageModal(this.src, this.alt);
        });
        
        // Add hover effect
        profilePic.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        profilePic.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
}

function createImageModal(imageSrc, imageAlt) {
    // Remove existing modal if any
    const existingModal = document.querySelector('.image-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'image-modal';
    modal.innerHTML = `
        <div class="modal-overlay">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <img src="${imageSrc}" alt="${imageAlt}">
            </div>
        </div>
    `;
    
    // Add modal styles
    const modalStyles = document.createElement('style');
    modalStyles.textContent = `
        .image-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }
        
        .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        .close-modal {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 30px;
            cursor: pointer;
            background: none;
            border: none;
            font-weight: bold;
        }
        
        .close-modal:hover {
            color: #ddd;
        }
    `;
    
    document.head.appendChild(modalStyles);
    document.body.appendChild(modal);
    
    // Add close functionality
    const closeBtn = modal.querySelector('.close-modal');
    const overlay = modal.querySelector('.modal-overlay');
    
    closeBtn.addEventListener('click', () => modal.remove());
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            modal.remove();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modal.remove();
        }
    });
}

function makeTablesResponsive() {
    // Add horizontal scroll for mobile tables
    const tables = document.querySelectorAll('table');
    
    tables.forEach(table => {
        const wrapper = table.parentElement;
        
        // Check if table needs horizontal scroll
        function checkTableScroll() {
            if (table.scrollWidth > wrapper.clientWidth) {
                wrapper.style.overflowX = 'auto';
            } else {
                wrapper.style.overflowX = 'hidden';
            }
        }
        
        // Check on load and resize
        checkTableScroll();
        window.addEventListener('resize', checkTableScroll);
    });
}

// Add loading animation for profile picture
function addProfilePictureLoading() {
    const profilePic = document.querySelector('.profile-picture img');
    
    if (profilePic) {
        profilePic.addEventListener('load', function() {
            this.style.opacity = '1';
            this.style.transform = 'scale(1)';
        });
        
        profilePic.addEventListener('error', function() {
            // Handle image loading error
            this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgdmlld0JveD0iMCAwIDE1MCAxNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik03NSA3NUM3NSA3NSA3NSA3NSA3NSA3NVoiIGZpbGw9IiNEN0Q3RDciLz4KPHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIwIDIwQzIwIDIwIDIwIDIwIDIwIDIwWiIgZmlsbD0iI0Q3RDdENyIvPgo8L3N2Zz4KPC9zdmc+';
        });
    }
}

// Initialize additional features when page is fully loaded
window.addEventListener('load', function() {
    addProfilePictureLoading();
    
    // Add fade-in effect for sections
    const sections = document.querySelectorAll('section');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, index * 200);
    });
});
