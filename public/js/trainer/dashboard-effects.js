// Dashboard Interactive Effects
document.addEventListener('DOMContentLoaded', function() {
    
    // Add ripple effect to buttons and interactive elements
    function createRipple(event) {
        const button = event.currentTarget;
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            pointer-events: none;
        `;
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
    
    // Add ripple effect to buttons
    const rippleElements = document.querySelectorAll('.action-btn, .nav-link, .assign-btn, .view-btn');
    rippleElements.forEach(element => {
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.addEventListener('click', createRipple);
    });
    
    // Add floating animation to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        // Stagger the animation
        card.style.animationDelay = `${index * 0.2}s`;
        card.classList.add('float-animation');
    });
    
    // Add glow effect to upcoming sessions
    const upcomingSessions = document.querySelectorAll('.session-item.upcoming');
    upcomingSessions.forEach(session => {
        session.classList.add('glow-effect');
    });
    
    // Add hover sound effect (optional - can be enabled/disabled)
    const interactiveElements = document.querySelectorAll('.nav-link, .action-btn, .stat-card');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            // You can add sound effects here if desired
            this.style.transform = this.style.transform + ' scale(1.02)';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = this.style.transform.replace(' scale(1.02)', '');
        });
    });
    
    // Parallax effect for background elements
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.stat-card, .session-item');
        
        parallaxElements.forEach((element, index) => {
            const rate = scrolled * -0.1 * (index % 3 + 1);
            element.style.transform += ` translateY(${rate}px)`;
        });
    });
    
    // Add shimmer effect to loading states
    function addShimmerEffect(element) {
        element.classList.add('shimmer');
        setTimeout(() => {
            element.classList.remove('shimmer');
        }, 2000);
    }
    
    // Animate elements on scroll into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                entry.target.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animatedElements = document.querySelectorAll('.stat-card, .session-item, .action-btn');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        observer.observe(element);
    });
    
    // Add click feedback to all interactive elements
    document.addEventListener('click', function(e) {
        if (e.target.closest('.action-btn, .nav-link, .stat-card')) {
            const element = e.target.closest('.action-btn, .nav-link, .stat-card');
            element.style.transform += ' scale(0.95)';
            
            setTimeout(() => {
                element.style.transform = element.style.transform.replace(' scale(0.95)', '');
            }, 150);
        }
    });
    
    // Dynamic color changes based on time of day
    function updateThemeByTime() {
        const hour = new Date().getHours();
        const root = document.documentElement;
        
        if (hour >= 6 && hour < 12) {
            // Morning theme - lighter, energetic
            root.style.setProperty('--background-gradient', 'linear-gradient(135deg, #74b9ff 0%, #0984e3 100%)');
        } else if (hour >= 12 && hour < 18) {
            // Afternoon theme - vibrant
            root.style.setProperty('--background-gradient', 'linear-gradient(135deg, #fd79a8 0%, #e84393 100%)');
        } else {
            // Evening theme - calm, purple
            root.style.setProperty('--background-gradient', 'linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%)');
        }
    }
    
    updateThemeByTime();
    
    console.log('Dashboard interactive effects loaded successfully! 🎉');
});
