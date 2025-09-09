// Optimized Dashboard Interactive Effects
document.addEventListener('DOMContentLoaded', function() {
    
    // Simplified ripple effect
    function createRipple(event) {
        const button = event.currentTarget;
        const rect = button.getBoundingClientRect();
        const size = Math.min(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple 0.4s linear;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            pointer-events: none;
        `;
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 400);
    }
    
    // Add ripple effect to important buttons only
    const rippleElements = document.querySelectorAll('.action-btn, .nav-link');
    rippleElements.forEach(element => {
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.addEventListener('click', createRipple);
    });
    
    // Reduce animations - only float effect for stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        if (index < 4) { // Only first 4 cards
            card.style.animationDelay = `${index * 0.5}s`;
            card.classList.add('float-animation');
        }
    });
    
    // Simple scroll animation (reduce performance impact)
    const observerOptions = {
        threshold: 0.3,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target); // Stop observing after animation
            }
        });
    }, observerOptions);
    
    // Only observe important elements
    const animatedElements = document.querySelectorAll('.stat-card');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'all 0.4s ease';
        observer.observe(element);
    });
    
    console.log('Optimized dashboard effects loaded! ⚡');
});
