// Enhanced Tournaments Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize button ripple effects
    initializeButtonRipples();
    
    // Initialize card animations
    initializeCardAnimations();
    
    // Initialize enhanced interactions
    initializeEnhancedInteractions();
    
    // Initialize stat counter animations
    initializeStatCounters();
});

// Button Ripple Effects
function initializeButtonRipples() {
    const enhancedButtons = document.querySelectorAll('.enhanced-btn, .enhanced-action-btn');
    
    enhancedButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Create ripple element
            const ripple = document.createElement('span');
            ripple.classList.add('btn-ripple');
            this.appendChild(ripple);
            
            // Position ripple at click location
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            // Remove ripple after animation
            setTimeout(() => {
                if (ripple.parentNode) {
                    ripple.parentNode.removeChild(ripple);
                }
            }, 600);
        });
    });
}

// Card Hover Animations
function initializeCardAnimations() {
    const tournamentCards = document.querySelectorAll('.enhanced-tournament-card');
    
    tournamentCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Add glow effect
            this.style.transform = 'translateY(-10px) scale(1.02)';
            
            // Add subtle rotation
            const randomRotation = (Math.random() - 0.5) * 2; // Random rotation between -1 and 1 degree
            this.style.transform += ` rotate(${randomRotation}deg)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1) rotate(0deg)';
        });
    });
    
    // Statistics cards animation
    const statCards = document.querySelectorAll('.tournament-stat-card');
    
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// Enhanced Interactions
function initializeEnhancedInteractions() {
    // Glass card hover effects
    const glassCards = document.querySelectorAll('.glass-card');
    
    glassCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.backdropFilter = 'blur(25px)';
            this.style.background = 'rgba(255, 255, 255, 0.98)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.backdropFilter = 'blur(20px)';
            this.style.background = 'rgba(255, 255, 255, 0.95)';
        });
    });
    
    // Enhanced button hover effects
    const actionButtons = document.querySelectorAll('.enhanced-action-btn');
    
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            // Add pulse effect
            this.style.animation = 'pulse 0.6s ease-in-out';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.animation = '';
        });
    });
    
    // Date circle hover effects
    const dateCircles = document.querySelectorAll('.date-circle');
    
    dateCircles.forEach(circle => {
        circle.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
            this.style.boxShadow = '0 12px 35px rgba(59, 130, 246, 0.4)';
        });
        
        circle.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.boxShadow = '0 8px 25px rgba(59, 130, 246, 0.3)';
        });
    });
}

// Animated Stat Counters
function initializeStatCounters() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const animateCounter = (element, target) => {
        const duration = 2000; // 2 seconds
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Handle different number formats
            if (target.toString().includes('%')) {
                element.textContent = Math.floor(current) + '%';
            } else if (target.toString().includes('.')) {
                element.textContent = current.toFixed(1);
            } else {
                element.textContent = Math.floor(current);
            }
        }, duration / steps);
    };
    
    // Intersection Observer for counter animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                entry.target.classList.add('counted');
                const target = parseInt(entry.target.textContent) || parseFloat(entry.target.textContent);
                entry.target.textContent = '0';
                animateCounter(entry.target, target);
            }
        });
    }, { threshold: 0.5 });
    
    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
}

// Add CSS animations dynamically
function addEnhancedStyles() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .glass-card {
            animation: slideInUp 0.6s ease-out forwards;
        }
        
        .glass-card:nth-child(2) {
            animation-delay: 0.1s;
        }
        
        .glass-card:nth-child(3) {
            animation-delay: 0.2s;
        }
        
        .enhanced-tournament-card {
            animation: fadeInScale 0.5s ease-out forwards;
        }
        
        .enhanced-tournament-card:nth-child(odd) {
            animation-delay: 0.1s;
        }
        
        .enhanced-tournament-card:nth-child(even) {
            animation-delay: 0.2s;
        }
        
        .tournament-stat-card {
            animation: slideInUp 0.4s ease-out forwards;
        }
        
        .tournament-stat-card:nth-child(1) { animation-delay: 0.1s; }
        .tournament-stat-card:nth-child(2) { animation-delay: 0.2s; }
        .tournament-stat-card:nth-child(3) { animation-delay: 0.3s; }
        .tournament-stat-card:nth-child(4) { animation-delay: 0.4s; }
    `;
    document.head.appendChild(style);
}

// Initialize enhanced styles when DOM is ready
document.addEventListener('DOMContentLoaded', addEnhancedStyles);

// Smooth scroll behavior for tournament navigation
function smoothScrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Tournament preparation modal functionality
function showPreparationModal(tournamentId) {
    // Create modal for tournament preparation
    const modal = document.createElement('div');
    modal.className = 'tournament-preparation-modal';
    modal.innerHTML = `
        <div class="modal-overlay"></div>
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h3><i class="fas fa-chess-knight"></i> Tournament Preparation</h3>
                <button class="modal-close" onclick="closePreparationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="preparation-checklist">
                    <h4>Preparation Checklist</h4>
                    <div class="checklist-item">
                        <input type="checkbox" id="fitness-check">
                        <label for="fitness-check">Physical Fitness Assessment</label>
                    </div>
                    <div class="checklist-item">
                        <input type="checkbox" id="strategy-review">
                        <label for="strategy-review">Strategy Review Session</label>
                    </div>
                    <div class="checklist-item">
                        <input type="checkbox" id="equipment-check">
                        <label for="equipment-check">Equipment Inspection</label>
                    </div>
                    <div class="checklist-item">
                        <input type="checkbox" id="nutrition-plan">
                        <label for="nutrition-plan">Nutrition Plan Setup</label>
                    </div>
                </div>
                <div class="preparation-notes">
                    <h4>Preparation Notes</h4>
                    <textarea placeholder="Add your preparation notes here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="enhanced-btn secondary-btn" onclick="closePreparationModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="enhanced-btn primary-btn" onclick="savePreparation()">
                    <i class="fas fa-save"></i> Save Preparation
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('active'), 10);
}

function closePreparationModal() {
    const modal = document.querySelector('.tournament-preparation-modal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.remove(), 300);
    }
}

function savePreparation() {
    // Save preparation logic here
    console.log('Preparation saved!');
    closePreparationModal();
    
    // Show success notification
    showNotification('Preparation saved successfully!', 'success');
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}