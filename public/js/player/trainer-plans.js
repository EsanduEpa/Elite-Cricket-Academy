// Trainer Plans JavaScript

// Tab Switching Function
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(`${tabName}-tab`).classList.add('active');
    
    // Add active class to clicked tab
    event.target.classList.add('active');
}

// View Workout Plan Details
function viewWorkoutPlan(planId) {
    const plan = workoutPlans.find(p => p.PlanID == planId);
    if (!plan) return;
    
    const modal = document.getElementById('workoutModal');
    const detailsContainer = document.getElementById('workoutDetails');
    
    detailsContainer.innerHTML = `
        <div style="display: grid; gap: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <h3 style="color: #667eea; margin-bottom: 10px; font-size: 24px;">${escapeHtml(plan.workoutname)}</h3>
                    <div style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
                        <span class="table-badge" style="background: rgba(74, 144, 226, 0.1); color: #4A90E2; border: 1px solid rgba(74, 144, 226, 0.3); padding: 6px 12px; border-radius: 12px; font-size: 12px;">
                            <i class="fas fa-calendar-alt"></i> ${escapeHtml(plan.frequency)}
                        </span>
                        ${plan.Intensity ? `
                            <span class="table-badge" style="${getIntensityStyle(plan.Intensity)} padding: 6px 12px; border-radius: 12px; font-size: 12px;">
                                <i class="fas fa-fire"></i> ${escapeHtml(plan.Intensity)} Intensity
                            </span>
                        ` : ''}
                        <span class="table-badge" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6; border: 1px solid rgba(155, 89, 182, 0.3); padding: 6px 12px; border-radius: 12px; font-size: 12px;">
                            <i class="fas fa-clock"></i> ${escapeHtml(plan.Duration)} minutes
                        </span>
                        ${plan.durationdays ? `
                            <span class="table-badge" style="background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3); padding: 6px 12px; border-radius: 12px; font-size: 12px;">
                                <i class="fas fa-calendar-week"></i> ${escapeHtml(plan.durationdays)} days program
                            </span>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <div style="background: rgba(102, 126, 234, 0.05); padding: 15px; border-radius: 8px; border-left: 4px solid #667eea;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                    <i class="fas fa-user-tie" style="color: #667eea; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 16px; color: #333;">
                            ${escapeHtml(plan.trainer_name || 'Elite Trainer')}
                        </div>
                        <div style="font-size: 12px; color: #666;">
                            <i class="fas fa-calendar-plus"></i> Created on ${formatDate(plan.CreatedDate)}
                        </div>
                    </div>
                </div>
            </div>
            
            ${plan.Benefits ? `
                <div>
                    <h4 style="color: #667eea; border-bottom: 2px solid rgba(102, 126, 234, 0.3); padding-bottom: 8px; margin-bottom: 12px;">
                        <i class="fas fa-star"></i> Key Benefits
                    </h4>
                    <p style="line-height: 1.8; color: #555;">${escapeHtml(plan.Benefits)}</p>
                </div>
            ` : ''}
            
            ${plan.NotSuitableFor ? `
                <div style="background: rgba(255, 107, 107, 0.05); padding: 15px; border-radius: 8px; border-left: 4px solid #ff6b6b;">
                    <h4 style="color: #ff6b6b; margin-bottom: 10px;">
                        <i class="fas fa-exclamation-triangle"></i> Not Suitable For
                    </h4>
                    <p style="line-height: 1.8; color: #555;">${escapeHtml(plan.NotSuitableFor)}</p>
                </div>
            ` : ''}
            
            ${plan.VideoLink ? `
                <div>
                    <h4 style="color: #667eea; border-bottom: 2px solid rgba(102, 126, 234, 0.3); padding-bottom: 8px; margin-bottom: 12px;">
                        <i class="fas fa-video"></i> Video Tutorial
                    </h4>
                    <a href="${escapeHtml(plan.VideoLink)}" target="_blank" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-play"></i> Watch Video Tutorial
                    </a>
                </div>
            ` : ''}
        </div>
    `;
    
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('show'), 10);
}

// View Nutrition Guide Details
function viewNutritionGuide(guideId) {
    const guide = nutritionGuides.find(g => g.id == guideId);
    if (!guide) return;
    
    const modal = document.getElementById('nutritionModal');
    const detailsContainer = document.getElementById('nutritionDetails');
    
    detailsContainer.innerHTML = `
        <div style="display: grid; gap: 20px;">
            <div>
                <h3 style="color: #2ecc71; margin-bottom: 10px; font-size: 24px;">${escapeHtml(guide.title)}</h3>
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <span class="table-badge" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3); padding: 6px 12px; border-radius: 12px;">
                        ${escapeHtml(guide.category)}
                    </span>
                    <span class="table-badge" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6; border: 1px solid rgba(155, 89, 182, 0.3); padding: 6px 12px; border-radius: 12px;">
                        ${escapeHtml(guide.target_audience)}
                    </span>
                </div>
                <p style="line-height: 1.8; color: #555; font-size: 15px;">${escapeHtml(guide.description)}</p>
            </div>
            
            <div style="background: rgba(46, 204, 113, 0.05); padding: 15px; border-radius: 8px; border-left: 4px solid #2ecc71;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-md" style="color: #2ecc71; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 16px; color: #333;">
                            ${escapeHtml(guide.trainer_name)}
                        </div>
                        <div style="font-size: 13px; color: #666;">
                            ${escapeHtml(guide.trainer_specialization)}
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 style="color: #2ecc71; border-bottom: 2px solid rgba(46, 204, 113, 0.3); padding-bottom: 8px; margin-bottom: 12px;">
                    <i class="fas fa-list"></i> What You'll Learn
                </h4>
                <ul style="list-style: none; padding: 0;">
                    ${guide.content.map(item => `
                        <li style="padding: 10px; margin: 8px 0; background: rgba(46, 204, 113, 0.05); border-radius: 6px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #2ecc71;"></i>
                            <span style="color: #333;">${escapeHtml(item)}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>
            
            <div style="display: flex; align-items: center; gap: 20px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); font-size: 13px; color: #999;">
                <span><i class="fas fa-eye"></i> ${guide.view_count} views</span>
                <span><i class="fas fa-calendar"></i> ${formatDate(guide.created_date)}</span>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('show'), 10);
}

// View Supplement Info Details
function viewSupplementInfo(supplementId) {
    const supplement = supplementInfo.find(s => s.id == supplementId);
    if (!supplement) return;
    
    const modal = document.getElementById('supplementModal');
    const detailsContainer = document.getElementById('supplementDetails');
    
    detailsContainer.innerHTML = `
        <div style="display: grid; gap: 20px;">
            <div>
                <h3 style="color: #8e44ad; margin-bottom: 10px; font-size: 24px;">${escapeHtml(supplement.supplement_name)}</h3>
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <span class="table-badge" style="background: rgba(142, 68, 173, 0.1); color: #8e44ad; border: 1px solid rgba(142, 68, 173, 0.3); padding: 6px 12px; border-radius: 12px;">
                        ${escapeHtml(supplement.category)}
                    </span>
                    <span class="table-badge" style="${getSafetyStyle(supplement.safety_rating)} padding: 6px 12px; border-radius: 12px;">
                        <i class="fas fa-shield-alt"></i> ${escapeHtml(supplement.safety_rating)}
                    </span>
                </div>
                <p style="line-height: 1.8; color: #555; font-size: 15px;">${escapeHtml(supplement.description)}</p>
            </div>
            
            <div style="background: rgba(142, 68, 173, 0.05); padding: 15px; border-radius: 8px; border-left: 4px solid #8e44ad;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-tie" style="color: #8e44ad; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 16px; color: #333;">
                            ${escapeHtml(supplement.trainer_name)}
                        </div>
                        <div style="font-size: 13px; color: #666;">
                            ${escapeHtml(supplement.trainer_specialization)}
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="background: rgba(52, 152, 219, 0.05); padding: 20px; border-radius: 8px; border-left: 4px solid #3498db;">
                <h4 style="color: #3498db; margin-bottom: 12px; font-size: 16px;">
                    <i class="fas fa-prescription-bottle"></i> Recommended Dosage
                </h4>
                <p style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 8px;">
                    ${escapeHtml(supplement.recommended_dosage)}
                </p>
                <p style="color: #666; font-size: 14px; line-height: 1.6;">
                    <strong>Usage:</strong> ${escapeHtml(supplement.usage)}
                </p>
            </div>
            
            <div>
                <h4 style="color: #8e44ad; border-bottom: 2px solid rgba(142, 68, 173, 0.3); padding-bottom: 8px; margin-bottom: 12px;">
                    <i class="fas fa-heart"></i> Benefits
                </h4>
                <p style="line-height: 1.8; color: #555;">${escapeHtml(supplement.benefits)}</p>
            </div>
            
            <div style="display: flex; align-items: center; gap: 20px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); font-size: 13px; color: #999;">
                <span><i class="fas fa-eye"></i> ${supplement.view_count} views</span>
                <span><i class="fas fa-calendar"></i> ${formatDate(supplement.created_date)}</span>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('show'), 10);
}

// Close Modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Helper Functions
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

function getIntensityStyle(intensity) {
    const styles = {
        'High': 'background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 107, 107, 0.3);',
        'Moderate': 'background: rgba(255, 159, 67, 0.1); color: #ff9f43; border: 1px solid rgba(255, 159, 67, 0.3);',
        'Low': 'background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3);'
    };
    return styles[intensity] || styles['Moderate'];
}

function getSafetyStyle(safety) {
    const styles = {
        'Very Safe': 'background: rgba(76, 175, 80, 0.1); color: #4caf50; border: 1px solid rgba(76, 175, 80, 0.3);',
        'Generally Safe': 'background: rgba(255, 193, 7, 0.1); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.3);',
        'Use With Caution': 'background: rgba(255, 152, 0, 0.1); color: #ff9800; border: 1px solid rgba(255, 152, 0, 0.3);'
    };
    return styles[safety] || styles['Generally Safe'];
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = ['workoutModal', 'nutritionModal', 'supplementModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            closeModal(modalId);
        }
    });
};

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('workoutModal');
        closeModal('nutritionModal');
        closeModal('supplementModal');
    }
});

// Add hover effects to stat cards
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
        });
    });
});
