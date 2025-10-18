<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/shopping.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/facilities.css">
    
<div class="player-layout">
    <!-- Sidebar -->
    <div class="player-sidebar" id="playerSidebar">
        <div class="sidebar-header">
            <div class="player-logo">
                <i class="fas fa-user-graduate"></i>
                <h3>Player Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Training</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                        <i class="fas fa-medal"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shopping</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
            <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Facility Booking Header -->
        <div class="shopping-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-building"></i> Facility Booking</h1>
                    <p>Book our premium facilities for training, practice, matches, and events</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="btn btn-facilities">
                        <i class="fas fa-shopping-bag"></i>
                        Back to Shop
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/rentals" class="btn btn-cart">
                        <i class="fas fa-tools"></i>
                        Equipment Rentals
                    </a>
                </div>
            </div>
        </div>

        <!-- Facilities Stats -->
        <div class="shopping-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Facilities Available</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">16</div>
                    <div class="stat-label">Hours Daily</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Capacity</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">5★</div>
                    <div class="stat-label">Premium Quality</div>
                </div>
            </div>
        </div>

        <!-- Facility Navigation -->
        <div class="shop-navigation">
            <button class="nav-btn active" onclick="filterFacilitiesByType('all')">
                <i class="fas fa-th-large"></i>
                All Facilities
            </button>
            <button class="nav-btn" onclick="filterFacilitiesByType('indoor')">
                <i class="fas fa-home"></i>
                Indoor
            </button>
            <button class="nav-btn" onclick="filterFacilitiesByType('outdoor')">
                <i class="fas fa-sun"></i>
                Outdoor
            </button>
            <button class="nav-btn" onclick="filterFacilitiesByType('training')">
                <i class="fas fa-dumbbell"></i>
                Training
            </button>
        </div>

        <!-- Facility Booking Content -->
        <div class="section-header">
            <div>
                <h2>Available Facilities</h2>
                <p>World-class facilities for all your cricket training and event needs</p>
            </div>
            <div class="shop-filters">
                <select class="filter-select" id="facility-type-filter">
                    <option value="all">All Facilities</option>
                    <option value="indoor">Indoor Facilities</option>
                    <option value="outdoor">Outdoor Facilities</option>
                    <option value="training">Training Areas</option>
                    <option value="event">Event Spaces</option>
                </select>
                <select class="filter-select" id="facility-capacity-filter">
                    <option value="all">All Capacities</option>
                    <option value="small">1-10 People</option>
                    <option value="medium">11-25 People</option>
                    <option value="large">26+ People</option>
                </select>
            </div>
        </div>
        
        <div class="products-grid" id="facilities-grid">
            <!-- Indoor Practice Nets -->
            <div class="product-card" data-type="indoor" data-capacity="small">
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/facilities/indoor-nets.jpg" alt="Indoor Practice Nets" onerror="this.src='https://via.placeholder.com/300x200?text=Indoor+Practice+Nets'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Indoor Facility</div>
                    <h3 class="product-title">Indoor Practice Nets</h3>
                    <p class="product-description">Climate-controlled indoor nets with professional lighting and bowling machine compatibility</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <span class="rating-text">(Premium facility)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Climate Controlled</span>
                        <span class="feature-tag">Professional Lighting</span>
                        <span class="feature-tag">Machine Compatible</span>
                    </div>
                    <div class="facility-details" style="margin: 1rem 0; font-size: 0.9rem; color: #666;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-users" style="color: #27ae60; width: 16px;"></i>
                            <span>Capacity: Up to 8 players</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-clock" style="color: #27ae60; width: 16px;"></i>
                            <span>Available: 6 AM - 10 PM</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-wifi" style="color: #27ae60; width: 16px;"></i>
                            <span>Free Wi-Fi Included</span>
                        </div>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$35/hour</span>
                    </div>
                    <div class="product-stock">✓ Available for booking</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewFacilityDetails('indoor-nets')">View Details</button>
                        <button class="btn btn-cart book-facility" data-facility="indoor-nets" data-name="Indoor Practice Nets" data-hourly="35" data-capacity="8">Book Facility</button>
                    </div>
                </div>
            </div>

            <!-- Main Cricket Ground -->
            <div class="product-card" data-type="outdoor" data-capacity="large">
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/facilities/main-ground.jpg" alt="Main Cricket Ground" onerror="this.src='https://via.placeholder.com/300x200?text=Main+Cricket+Ground'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Outdoor Facility</div>
                    <h3 class="product-title">Main Cricket Ground</h3>
                    <p class="product-description">Full-size cricket ground with professional pitch and seating for matches and tournaments</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <span class="rating-text">(Professional ground)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Professional Pitch</span>
                        <span class="feature-tag">Spectator Seating</span>
                        <span class="feature-tag">Match Ready</span>
                    </div>
                    <div class="facility-details" style="margin: 1rem 0; font-size: 0.9rem; color: #666;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-users" style="color: #27ae60; width: 16px;"></i>
                            <span>Capacity: 22 players + 100 spectators</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-clock" style="color: #27ae60; width: 16px;"></i>
                            <span>Available: 7 AM - 8 PM</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-trophy" style="color: #27ae60; width: 16px;"></i>
                            <span>Tournament Ready</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-parking" style="color: #27ae60; width: 16px;"></i>
                            <span>Parking Available</span>
                        </div>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$200/half-day</span>
                        <small style="color: #666; margin-left: 10px;">($350/full-day)</small>
                    </div>
                    <div class="product-stock">✓ Available for booking</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewFacilityDetails('main-ground')">View Details</button>
                        <button class="btn btn-cart book-facility" data-facility="main-ground" data-name="Main Cricket Ground" data-half="200" data-full="350">Book Facility</button>
                    </div>
                </div>
            </div>

            <!-- Cricket Fitness Center -->
            <div class="product-card" data-type="training" data-capacity="medium">
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/facilities/fitness-center.jpg" alt="Fitness Center" onerror="this.src='https://via.placeholder.com/300x200?text=Fitness+Center'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Training Facility</div>
                    <h3 class="product-title">Cricket Fitness Center</h3>
                    <p class="product-description">Specialized fitness center with cricket-specific training equipment and recovery facilities</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <span class="rating-text">(Modern facility)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">Cricket-Specific Equipment</span>
                        <span class="feature-tag">Recovery Suite</span>
                        <span class="feature-tag">Personal Training Available</span>
                    </div>
                    <div class="facility-details" style="margin: 1rem 0; font-size: 0.9rem; color: #666;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-users" style="color: #27ae60; width: 16px;"></i>
                            <span>Capacity: Up to 12 players</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-clock" style="color: #27ae60; width: 16px;"></i>
                            <span>Available: 5 AM - 11 PM</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-dumbbell" style="color: #27ae60; width: 16px;"></i>
                            <span>Professional Equipment</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-spa" style="color: #27ae60; width: 16px;"></i>
                            <span>Recovery Facilities</span>
                        </div>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$25/hour</span>
                    </div>
                    <div class="product-stock">✓ Available for booking</div>
                    <div class="product-actions">
                        <button class="btn btn-view" onclick="viewFacilityDetails('fitness-center')">View Details</button>
                        <button class="btn btn-cart book-facility" data-facility="fitness-center" data-name="Cricket Fitness Center" data-hourly="25" data-capacity="12">Book Facility</button>
                    </div>
                </div>
            </div>

            <!-- Team Conference Room -->
            <div class="product-card" data-type="event" data-capacity="medium">
                <div class="product-image">
                    <img src="<?php echo URLROOT; ?>/img/facilities/conference-room.jpg" alt="Conference Room" onerror="this.src='https://via.placeholder.com/300x200?text=Conference+Room'" />
                </div>
                <div class="product-info">
                    <div class="product-brand">Event Facility</div>
                    <h3 class="product-title">Team Conference Room</h3>
                    <p class="product-description">Professional meeting space with AV equipment for team meetings, analysis, and presentations</p>
                    <div class="product-rating">
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <i class="fas fa-star active"></i>
                        <span class="rating-text">(Professional space)</span>
                    </div>
                    <div class="product-features">
                        <span class="feature-tag">AV Equipment</span>
                        <span class="feature-tag">Video Analysis</span>
                        <span class="feature-tag">Presentation Ready</span>
                    </div>
                    <div class="facility-details" style="margin: 1rem 0; font-size: 0.9rem; color: #666;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-users" style="color: #27ae60; width: 16px;"></i>
                            <span>Capacity: Up to 25 people</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-clock" style="color: #27ae60; width: 16px;"></i>
                            <span>Available: 8 AM - 8 PM</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-tv"></i>
                            <span>AV Equipment Included</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-coffee"></i>
                            <span>Refreshments Available</span>
                        </div>
                    </div>
                    <div class="facility-pricing">
                        <div class="price-option">
                            <span class="price-label">Hourly Rate:</span>
                            <span class="price-value">$40/hour</span>
                        </div>
                    </div>
                    <button class="btn btn-book book-facility" data-facility="conference-room" data-name="Team Conference Room" data-hourly="40" data-capacity="25">Book Facility</button>
                </div>
            </div>

            <!-- Outdoor Training Area -->
            <div class="facility-card" data-type="outdoor" data-capacity="medium">
                <div class="facility-image">
                    <img src="<?php echo URLROOT; ?>/img/facilities/outdoor-training.jpg" alt="Outdoor Training Area" onerror="this.src='https://via.placeholder.com/300x200?text=Outdoor+Training+Area'" />
                </div>
                <div class="facility-info">
                    <h3>Outdoor Training Area</h3>
                    <p>Open field area perfect for fitness training, conditioning, and team building activities</p>
                    <div class="facility-features">
                        <span class="feature-tag">Open Field</span>
                        <span class="feature-tag">Natural Grass</span>
                        <span class="feature-tag">Multi-Purpose</span>
                    </div>
                    <div class="facility-details">
                        <div class="detail-item">
                            <i class="fas fa-users"></i>
                            <span>Capacity: Up to 20 players</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span>Available: 6 AM - 9 PM</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-leaf"></i>
                            <span>Natural Grass Surface</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-water"></i>
                            <span>Water Facilities</span>
                        </div>
                    </div>
                    <div class="facility-pricing">
                        <div class="price-option">
                            <span class="price-label">Hourly Rate:</span>
                            <span class="price-value">$20/hour</span>
                        </div>
                    </div>
                    <button class="btn btn-book book-facility" data-facility="outdoor-training" data-name="Outdoor Training Area" data-hourly="20" data-capacity="20">Book Facility</button>
                </div>
            </div>

            <!-- Indoor Sports Hall -->
            <div class="facility-card" data-type="indoor" data-capacity="large">
                <div class="facility-image">
                    <img src="<?php echo URLROOT; ?>/img/facilities/sports-hall.jpg" alt="Indoor Sports Hall" onerror="this.src='https://via.placeholder.com/300x200?text=Indoor+Sports+Hall'" />
                </div>
                <div class="facility-info">
                    <h3>Indoor Sports Hall</h3>
                    <p>Large multi-purpose indoor hall suitable for various sports activities and events</p>
                    <div class="facility-features">
                        <span class="feature-tag">Multi-Purpose</span>
                        <span class="feature-tag">High Ceiling</span>
                        <span class="feature-tag">Sound System</span>
                    </div>
                    <div class="facility-details">
                        <div class="detail-item">
                            <i class="fas fa-users"></i>
                            <span>Capacity: Up to 40 people</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span>Available: 7 AM - 10 PM</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-volume-up"></i>
                            <span>Sound System Included</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-restroom"></i>
                            <span>Changing Rooms Available</span>
                        </div>
                    </div>
                    <div class="facility-pricing">
                        <div class="price-option">
                            <span class="price-label">Hourly Rate:</span>
                            <span class="price-value">$45/hour</span>
                        </div>
                    </div>
                    <button class="btn btn-book book-facility" data-facility="sports-hall" data-name="Indoor Sports Hall" data-hourly="45" data-capacity="40">Book Facility</button>
                </div>
            </div>
        </div>

        <!-- Booking Information -->
        <div class="booking-info-section">
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Easy Online Booking</h4>
                        <p>Book facilities online with our simple booking system. Choose your date, time, and duration with instant confirmation.</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h4>Flexible Scheduling</h4>
                        <p>Our facilities are available from early morning to late evening. Book for as little as 1 hour or reserve for full days.</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="info-content">
                        <h4>Equipment Included</h4>
                        <p>Basic equipment and facilities maintenance are included in all bookings. Additional equipment available for rent.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Facility Booking Modal -->
<div id="facilityModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Facility Booking</h3>
            <button class="close-btn" onclick="closeFacilityModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="facility-details"></div>
            <form id="facility-form">
                <div class="form-group">
                    <label for="booking-date">Date:</label>
                    <input type="date" id="booking-date" required>
                </div>
                <div class="form-group">
                    <label for="booking-time">Time:</label>
                    <select id="booking-time" required>
                        <option value="06:00">6:00 AM</option>
                        <option value="07:00">7:00 AM</option>
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                        <option value="18:00">6:00 PM</option>
                        <option value="19:00">7:00 PM</option>
                        <option value="20:00">8:00 PM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="booking-duration">Duration (hours):</label>
                    <select id="booking-duration" required>
                        <option value="1">1 hour</option>
                        <option value="2">2 hours</option>
                        <option value="3">3 hours</option>
                        <option value="4">4 hours (Half Day)</option>
                        <option value="8">8 hours (Full Day)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="booking-purpose">Purpose:</label>
                    <select id="booking-purpose" required>
                        <option value="">Select purpose...</option>
                        <option value="training">Training Session</option>
                        <option value="practice">Practice Match</option>
                        <option value="meeting">Team Meeting</option>
                        <option value="event">Special Event</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </form>
            <div class="booking-total">
                <strong>Total: $<span id="booking-total">0.00</span></strong>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeFacilityModal()">Cancel</button>
            <button class="btn btn-primary" onclick="confirmBooking()">Confirm Booking</button>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/player/facilities.js"></script>

<style>
.facility-features {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.info-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.info-icon {
    font-size: 2.5rem;
    color: #4A90E2;
    margin-bottom: 1rem;
}

.info-content h4 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.info-content p {
    color: #7f8c8d;
    line-height: 1.6;
}

.booking-info-section {
    margin-top: 3rem;
    padding: 2rem 0;
    border-top: 2px solid #eee;
}
</style>

<script src="<?php echo URLROOT; ?>/js/player/shopping.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/facilities.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>