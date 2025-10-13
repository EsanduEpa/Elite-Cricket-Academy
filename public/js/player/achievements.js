// Achievements Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize achievements page
    initializeAchievements();

    function initializeAchievements() {
        initializeProgressRings();
        initializeTrophyAnimations();
        initializeCertificateActions();
        initializeBadgeProgress();
        updateAchievementStats();
        initializeFilterSystem();
        handleInitialFilter();
    }

    // Handle initial filter based on URL hash
    function handleInitialFilter() {
        const hash = window.location.hash.substring(1);
        const validCategories = ['all', 'trophies', 'records', 'certificates', 'badges'];
        
        if (hash && validCategories.includes(hash)) {
            // Find and activate the corresponding tab
            const targetTab = document.querySelector(`[data-category="${hash}"]`);
            if (targetTab) {
                document.querySelectorAll('.achievement-tab').forEach(tab => tab.classList.remove('active'));
                targetTab.classList.add('active');
                filterAchievements(hash);
            }
        } else {
            // Default to showing all achievements
            filterAchievements('all');
        }
    }

    // Initialize progress rings for personal records
    function initializeProgressRings() {
        const progressRings = document.querySelectorAll('.progress-ring');
        
        progressRings.forEach((ring, index) => {
            const progressElement = ring.querySelector('.progress');
            const valueElement = ring.querySelector('.current-value');
            const targetElement = ring.querySelector('.target-value');
            
            if (progressElement && valueElement && targetElement) {
                const current = parseInt(valueElement.textContent);
                const target = parseInt(targetElement.textContent.replace('Target: ', ''));
                const percentage = Math.min((current / target) * 100, 100);
                
                const circumference = 2 * Math.PI * 40; // radius = 40
                
                progressElement.style.strokeDasharray = circumference;
                progressElement.style.strokeDashoffset = circumference;
                
                // Animate progress with delay for each ring
                setTimeout(() => {
                    const offset = circumference - (percentage / 100) * circumference;
                    progressElement.style.strokeDashoffset = offset;
                    
                    // Update progress color based on completion
                    if (percentage >= 90) {
                        progressElement.style.stroke = '#22c55e';
                    } else if (percentage >= 70) {
                        progressElement.style.stroke = '#f59e0b';
                    } else {
                        progressElement.style.stroke = '#4A90E2';
                    }
                }, 500 + (index * 200));
            }
        });
    }

    // Initialize trophy animations
    function initializeTrophyAnimations() {
        const trophyCards = document.querySelectorAll('.trophy-card');
        
        // Intersection Observer for trophy animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0) scale(1)';
                    
                    // Add sparkle effect for gold trophies
                    const trophyIcon = entry.target.querySelector('.trophy-icon.gold');
                    if (trophyIcon) {
                        addSparkleEffect(trophyIcon);
                    }
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        trophyCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px) scale(0.9)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    }

    // Add sparkle effect to gold trophies
    function addSparkleEffect(element) {
        const sparkles = document.createElement('div');
        sparkles.className = 'sparkles';
        
        for (let i = 0; i < 6; i++) {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            sparkle.style.animationDelay = `${i * 0.2}s`;
            sparkles.appendChild(sparkle);
        }
        
        element.style.position = 'relative';
        element.appendChild(sparkles);
        
        // Remove sparkles after animation
        setTimeout(() => {
            if (element.contains(sparkles)) {
                element.removeChild(sparkles);
            }
        }, 3000);
    }

    // Initialize certificate actions
    function initializeCertificateActions() {
        // View certificate buttons
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                const certificateCard = this.closest('.certificate-card');
                const certificateName = certificateCard.querySelector('.certificate-info h3').textContent;
                const issuer = certificateCard.querySelector('.certificate-issuer').textContent;
                const description = certificateCard.querySelector('.certificate-description').textContent;
                const issueDate = certificateCard.querySelector('.issue-date').textContent;
                const certificateId = certificateCard.querySelector('.certificate-id').textContent;
                
                showCertificateModal(certificateName, issuer, description, issueDate, certificateId);
            });
        });

        // Download certificate buttons
        document.querySelectorAll('.btn-download').forEach(btn => {
            btn.addEventListener('click', function() {
                const certificateCard = this.closest('.certificate-card');
                const certificateName = certificateCard.querySelector('.certificate-info h3').textContent;
                
                downloadCertificate(certificateName);
            });
        });
    }

    // Initialize badge progress
    function initializeBadgeProgress() {
        const badgeCards = document.querySelectorAll('.badge-card');
        
        badgeCards.forEach((card, index) => {
            const progressBar = card.querySelector('.badge-progress-fill');
            const progressText = card.querySelector('.badge-progress-text');
            const badgeIcon = card.querySelector('.badge-icon');
            
            if (progressBar && progressText) {
                const progressMatch = progressText.textContent.match(/(\d+)\/(\d+)/);
                if (progressMatch) {
                    const current = parseInt(progressMatch[1]);
                    const total = parseInt(progressMatch[2]);
                    const percentage = (current / total) * 100;
                    
                    // Animate progress bar
                    setTimeout(() => {
                        progressBar.style.width = `${percentage}%`;
                        
                        // Update badge appearance based on progress
                        if (percentage === 100 && badgeIcon.classList.contains('locked')) {
                            badgeIcon.classList.remove('locked');
                            badgeIcon.classList.add('earned');
                            showBadgeEarnedAnimation(card);
                        }
                    }, 300 + (index * 100));
                }
            }
        });
    }

    // Show badge earned animation
    function showBadgeEarnedAnimation(badgeCard) {
        const overlay = document.createElement('div');
        overlay.className = 'badge-earned-overlay';
        overlay.innerHTML = `
            <div class="badge-earned-content">
                <div class="badge-earned-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Badge Earned!</h3>
                <p>${badgeCard.querySelector('.badge-name').textContent}</p>
                <button class="btn btn-primary" onclick="closeBadgeAnimation()">Awesome!</button>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        setTimeout(() => {
            overlay.classList.add('show');
        }, 100);
    }

    // Update achievement statistics
    function updateAchievementStats() {
        // Count achievements
        const trophies = document.querySelectorAll('.trophy-card').length;
        const medals = document.querySelectorAll('.trophy-icon.silver, .trophy-icon.bronze').length;
        const certificates = document.querySelectorAll('.certificate-card').length;
        const earnedBadges = document.querySelectorAll('.badge-icon.earned').length;
        
        // Update stat cards with animation
        animateStatValue('.stat-icon.trophies + .stat-content .stat-number', trophies);
        animateStatValue('.stat-icon.medals + .stat-content .stat-number', medals);
        animateStatValue('.stat-icon.certificates + .stat-content .stat-number', certificates);
        animateStatValue('.stat-icon.badges + .stat-content .stat-number', earnedBadges);
        
        // Update total achievements
        const totalAchievements = trophies + certificates + earnedBadges;
        animateStatValue('.total-achievements', totalAchievements);
    }

    // Animate stat values
    function animateStatValue(selector, targetValue) {
        const element = document.querySelector(selector);
        if (element) {
            animateCountUp(element, 0, targetValue, 1500);
        }
    }

    // Initialize filter system
    function initializeFilterSystem() {
        // Achievement type filters
        const filterBtns = document.querySelectorAll('.achievement-tab');
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filterType = this.getAttribute('data-category');
                
                // Update active button
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filter achievements
                filterAchievements(filterType);
            });
        });
        
        // Search functionality
        const searchInput = document.querySelector('.achievement-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                searchAchievements(searchTerm);
            });
        }
    }

    // Filter achievements by type
    function filterAchievements(filterType) {
        const sections = {
            'all': ['.trophy-gallery', '.personal-records', '.certificates', '.skill-badges'],
            'trophies': ['.trophy-gallery'],
            'records': ['.personal-records'],
            'certificates': ['.certificates'],
            'badges': ['.skill-badges']
        };
        
        // Hide all sections first
        const allSections = document.querySelectorAll('.trophy-gallery, .personal-records, .certificates, .skill-badges');
        allSections.forEach(section => {
            section.style.display = 'none';
            section.style.opacity = '0';
        });
        
        // Show selected sections with animation
        if (sections[filterType]) {
            sections[filterType].forEach(selector => {
                const section = document.querySelector(selector);
                if (section) {
                    section.style.display = 'block';
                    
                    // Trigger reflow
                    section.offsetHeight;
                    
                    // Animate in
                    setTimeout(() => {
                        section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        section.style.opacity = '1';
                        section.style.transform = 'translateY(0)';
                    }, 50);
                }
            });
        }
        
        // Update URL hash for bookmarking
        if (filterType !== 'all') {
            window.location.hash = filterType;
        } else {
            history.replaceState(null, null, ' ');
        }
    }

    // Search achievements
    function searchAchievements(searchTerm) {
        const achievementElements = [
            ...document.querySelectorAll('.trophy-card'),
            ...document.querySelectorAll('.record-card'),
            ...document.querySelectorAll('.certificate-card'),
            ...document.querySelectorAll('.badge-card')
        ];
        
        achievementElements.forEach(element => {
            const text = element.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                element.style.display = 'block';
                element.style.opacity = '1';
            } else {
                element.style.opacity = '0';
                setTimeout(() => {
                    if (!element.textContent.toLowerCase().includes(searchTerm)) {
                        element.style.display = 'none';
                    }
                }, 300);
            }
        });
    }

    // Show certificate modal
    function showCertificateModal(name, issuer, description, date, id) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content modal-certificate">
                <div class="modal-header">
                    <h3>Certificate Details</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="certificate-preview">
                        <div class="certificate-border">
                            <div class="certificate-content">
                                <div class="certificate-header-preview">
                                    <h2>Certificate of Achievement</h2>
                                    <div class="certificate-logo">
                                        <i class="fas fa-award"></i>
                                    </div>
                                </div>
                                <div class="certificate-body-preview">
                                    <p class="certificate-text">This is to certify that</p>
                                    <h3 class="recipient-name">John Doe</h3>
                                    <p class="certificate-text">has successfully completed</p>
                                    <h4 class="course-name">${name}</h4>
                                    <p class="certificate-description">${description}</p>
                                </div>
                                <div class="certificate-footer-preview">
                                    <div class="signature-section">
                                        <div class="signature">
                                            <div class="signature-line"></div>
                                            <p>Authorized Signature</p>
                                        </div>
                                        <div class="date-section">
                                            <p><strong>Date:</strong> ${date}</p>
                                            <p><strong>Certificate ID:</strong> ${id}</p>
                                        </div>
                                    </div>
                                    <div class="issuer-info">
                                        <p><strong>Issued by:</strong> ${issuer}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="downloadCertificate('${name}')">Download PDF</button>
                    <button class="btn btn-outline" onclick="shareAchievement('${name}')">Share</button>
                    <button class="btn btn-outline" onclick="closeModal()">Close</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    // Download certificate
    function downloadCertificate(certificateName) {
        showNotification(`Downloading ${certificateName} certificate...`, 'info');
        
        // Simulate download process
        setTimeout(() => {
            showNotification(`${certificateName} certificate downloaded successfully!`, 'success');
        }, 2000);
    }

    // Share achievement
    function shareAchievement(achievementName) {
        const shareModal = document.createElement('div');
        shareModal.className = 'modal-overlay';
        shareModal.innerHTML = `
            <div class="modal-content modal-share">
                <div class="modal-header">
                    <h3>Share Achievement</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Share your achievement: <strong>${achievementName}</strong></p>
                    <div class="share-options">
                        <button class="share-btn facebook" onclick="shareToFacebook('${achievementName}')">
                            <i class="fab fa-facebook"></i> Facebook
                        </button>
                        <button class="share-btn twitter" onclick="shareToTwitter('${achievementName}')">
                            <i class="fab fa-twitter"></i> Twitter
                        </button>
                        <button class="share-btn linkedin" onclick="shareToLinkedIn('${achievementName}')">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </button>
                        <button class="share-btn copy" onclick="copyShareLink('${achievementName}')">
                            <i class="fas fa-link"></i> Copy Link
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline" onclick="closeShareModal()">Close</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(shareModal);
        
        shareModal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(shareModal);
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Animate count up
    function animateCountUp(element, start, end, duration) {
        const startTime = performance.now();
        
        function updateCount(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.floor(progress * (end - start) + start);
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(updateCount);
            }
        }
        
        requestAnimationFrame(updateCount);
    }

    // Global functions for modal actions
    window.closeModal = function() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            document.body.removeChild(modal);
        }
    };

    window.closeBadgeAnimation = function() {
        const overlay = document.querySelector('.badge-earned-overlay');
        if (overlay) {
            overlay.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(overlay)) {
                    document.body.removeChild(overlay);
                }
            }, 300);
        }
    };

    window.closeShareModal = function() {
        const shareModal = document.querySelector('.modal-share').closest('.modal-overlay');
        if (shareModal) {
            document.body.removeChild(shareModal);
        }
    };

    window.shareToFacebook = function(achievementName) {
        showNotification(`Sharing ${achievementName} to Facebook...`, 'info');
        // Implement actual Facebook sharing logic
    };

    window.shareToTwitter = function(achievementName) {
        showNotification(`Sharing ${achievementName} to Twitter...`, 'info');
        // Implement actual Twitter sharing logic
    };

    window.shareToLinkedIn = function(achievementName) {
        showNotification(`Sharing ${achievementName} to LinkedIn...`, 'info');
        // Implement actual LinkedIn sharing logic
    };

    window.copyShareLink = function(achievementName) {
        // Simulate copying to clipboard
        showNotification('Share link copied to clipboard!', 'success');
    };

    // Achievement milestones check
    function checkMilestones() {
        const totalAchievements = document.querySelectorAll('.trophy-card, .certificate-card, .badge-icon.earned').length;
        
        const milestones = [5, 10, 25, 50, 100];
        const reachedMilestone = milestones.find(milestone => totalAchievements === milestone);
        
        if (reachedMilestone) {
            showMilestoneReached(reachedMilestone);
        }
    }

    // Show milestone reached notification
    function showMilestoneReached(milestone) {
        const milestoneModal = document.createElement('div');
        milestoneModal.className = 'modal-overlay milestone-modal';
        milestoneModal.innerHTML = `
            <div class="modal-content">
                <div class="milestone-content">
                    <div class="milestone-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h2>Milestone Reached!</h2>
                    <p>Congratulations! You've reached ${milestone} achievements!</p>
                    <div class="milestone-reward">
                        <p>You've earned a special badge!</p>
                        <div class="special-badge">
                            <i class="fas fa-medal"></i>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="closeMilestoneModal()">Continue</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(milestoneModal);
        
        setTimeout(() => {
            milestoneModal.classList.add('show');
        }, 100);
    }

    window.closeMilestoneModal = function() {
        const modal = document.querySelector('.milestone-modal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(modal)) {
                    document.body.removeChild(modal);
                }
            }, 300);
        }
    };

    // Check for milestones on page load
    setTimeout(checkMilestones, 2000);

    // Initialize comparison feature
    const compareBtn = document.querySelector('.btn-compare');
    if (compareBtn) {
        compareBtn.addEventListener('click', function() {
            showComparisonModal();
        });
    }

    function showComparisonModal() {
        // Implementation for comparing achievements with other players
        showNotification('Comparison feature coming soon!', 'info');
    }

    // Enhanced Purple Shimmer Effects
    function initializePurpleShimmers() {
        const achievementTitle = document.querySelector('.content-header h1');
        if (achievementTitle) {
            achievementTitle.classList.add('achievement-title');
        }

        // Add shimmer to trophy cards on hover
        document.querySelectorAll('.trophy-card.gold').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('purple-shimmer');
            });
            
            card.addEventListener('mouseleave', function() {
                this.classList.remove('purple-shimmer');
            });
        });

        // Add shimmer to achievement score displays
        document.querySelectorAll('.stat-number').forEach(statNumber => {
            setInterval(() => {
                statNumber.classList.add('purple-shimmer');
                setTimeout(() => {
                    statNumber.classList.remove('purple-shimmer');
                }, 2000);
            }, 8000);
        });
    }

    // Enhanced Particle System
    function createFloatingParticles() {
        const particles = ['✨', '🏆', '⭐', '💫', '🌟'];
        
        setInterval(() => {
            const particle = document.createElement('div');
            particle.className = 'floating-particle';
            particle.textContent = particles[Math.floor(Math.random() * particles.length)];
            particle.style.cssText = `
                position: fixed;
                font-size: ${Math.random() * 20 + 15}px;
                left: ${Math.random() * window.innerWidth}px;
                top: ${window.innerHeight + 50}px;
                pointer-events: none;
                z-index: 1000;
                opacity: 0.7;
                animation: floatUp 6s linear forwards;
            `;
            
            document.body.appendChild(particle);
            
            setTimeout(() => {
                if (document.body.contains(particle)) {
                    document.body.removeChild(particle);
                }
            }, 6000);
        }, 3000);
    }

    // Add CSS for floating particles
    const particleStyle = document.createElement('style');
    particleStyle.textContent = `
        @keyframes floatUp {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.7;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-${window.innerHeight + 100}px) rotate(360deg);
                opacity: 0;
            }
        }
        
        .floating-particle {
            filter: drop-shadow(0 0 10px rgba(99, 102, 241, 0.6));
        }
    `;
    document.head.appendChild(particleStyle);

    // Enhanced Trophy Hover Effects
    function enhanceTrophyInteractions() {
        document.querySelectorAll('.trophy-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const trophyIcon = this.querySelector('.trophy-icon');
                if (trophyIcon && trophyIcon.classList.contains('gold')) {
                    // Create purple trail effect
                    for (let i = 0; i < 5; i++) {
                        setTimeout(() => {
                            const spark = document.createElement('div');
                            spark.className = 'purple-spark';
                            spark.style.cssText = `
                                position: absolute;
                                width: 4px;
                                height: 4px;
                                background: #6366f1;
                                border-radius: 50%;
                                top: ${Math.random() * 100}%;
                                left: ${Math.random() * 100}%;
                                pointer-events: none;
                                animation: sparkFade 1s ease-out forwards;
                                box-shadow: 0 0 6px #6366f1;
                            `;
                            this.appendChild(spark);
                            
                            setTimeout(() => {
                                if (this.contains(spark)) {
                                    this.removeChild(spark);
                                }
                            }, 1000);
                        }, i * 100);
                    }
                }
            });
        });
    }

    // Add CSS for spark effects
    const sparkStyle = document.createElement('style');
    sparkStyle.textContent = `
        @keyframes sparkFade {
            0% {
                opacity: 1;
                transform: scale(1);
            }
            100% {
                opacity: 0;
                transform: scale(0) translateY(-20px);
            }
        }
    `;
    document.head.appendChild(sparkStyle);

    // Initialize all new effects
    initializePurpleShimmers();
    createFloatingParticles();
    enhanceTrophyInteractions();
});