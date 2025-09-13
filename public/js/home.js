// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, looking for buttons...');
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add scroll effect to header
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header');
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.backdropFilter = 'blur(10px)';
        } else {
            header.style.background = '#fff';
            header.style.backdropFilter = 'none';
        }
    });

    // Button interactions
    const enrollBtn = document.querySelector('.enroll-btn');
    const loginBtn = document.querySelector('.login-btn');
    const exploreBtn = document.querySelector('.explore-btn');
    const learnMoreBtn = document.querySelector('.learn-more-btn');

    console.log('=== BUTTON DEBUG ===');
    console.log('Enroll button found:', enrollBtn);
    console.log('Login button found:', loginBtn);
    console.log('Explore button found:', exploreBtn);
    console.log('Learn more button found:', learnMoreBtn);
    console.log('All buttons with enroll-btn class:', document.querySelectorAll('.enroll-btn'));
    console.log('All buttons with login-btn class:', document.querySelectorAll('.login-btn'));
    console.log('=== END DEBUG ===');

    if (exploreBtn) {
        exploreBtn.addEventListener('click', function() {
            document.querySelector('#programs').scrollIntoView({
                behavior: 'smooth'
            });
        });
    }

    if (enrollBtn) {
        console.log('Enroll button found - using default link behavior');
        // Remove preventDefault to allow natural link behavior
        // The href attribute in HTML will handle navigation
    } else {
       console.error('Enroll button not found!');
    }

    if (loginBtn) {
        console.log('Login button found - using default link behavior');
        // Remove preventDefault to allow natural link behavior
        // The href attribute in HTML will handle navigation
    } else {
        console.error('Login button not found!');
    }

    if (learnMoreBtn) {
        learnMoreBtn.addEventListener('click', function() {
            alert('More information about the Summer Cricket Camp would be displayed here.');
        });
    }
    
    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards for animation
    document.querySelectorAll('.program-card, .coach-card, .facility-card, .testimonial-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
}); 