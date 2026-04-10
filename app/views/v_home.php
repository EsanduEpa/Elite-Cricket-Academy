<?php require_once APPROOT . '/views/inc/components/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Elite Cricket Academy</h1>
            <p style="max-width: 760px; margin: 0 auto; font-size: 1.1rem; line-height: 1.8;">
                <?php echo (int)($data['homeStats']['program_count'] ?? 0); ?> active programs,
                <?php echo (int)($data['homeStats']['coach_count'] ?? 0); ?> coaches,
                <?php echo (int)($data['homeStats']['facility_count'] ?? 0); ?> facilities, and
                <?php echo (int)($data['homeStats']['upcoming_event_count'] ?? 0); ?> upcoming events.
            </p>
            <button class="explore-btn">Explore Programs</button>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <h2>About Elite Cricket Academy</h2>
        <p>Elite Cricket Academy is dedicated to nurture the next generation of cricket stars. Our comprehensive training programs, led by experienced coaches, focus on skill development, tactical understanding, and physical conditioning. We provide state-of-the-art facilities and a supportive environment to help players reach their full potential.</p>
        
        <p>Founded with a vision to create world-class cricketers, our academy combines traditional cricket values with modern training methodologies. We believe in developing not just skilled players, but also individuals with strong character, leadership qualities, and sportsmanship.</p>
        
        <p>Our state-of-the-art facilities include indoor and outdoor training nets, professional-grade equipment, and a dedicated fitness center. We offer year-round programs for players of all skill levels, from beginners to advanced players aspiring to play at the highest levels.</p>
        
        <p>With a track record of producing successful players who have represented their countries and played in major leagues worldwide, Elite Cricket Academy continues to be the preferred choice for serious cricket development. Join us in your journey to cricket excellence.</p>
    </section>

    <!-- Programs Section -->
    <section class="programs" id="programs">
        <div class="programs-container">
            <h2>Our Programs</h2>
            <div class="programs-grid">
                <?php foreach (($data['programs'] ?? []) as $program): ?>
                    <div class="program-card">
                        <div class="program-image <?php echo htmlspecialchars($program['image_class']); ?>"></div>
                        <div class="program-content">
                            <h3><?php echo htmlspecialchars($program['name']); ?></h3>
                            <p><?php echo htmlspecialchars($program['description']); ?></p>
                            <p>
                                <?php echo (int)$program['sessions_per_week']; ?> sessions/week
                                <?php if (!empty($program['private_sessions'])): ?>
                                    • <?php echo (int)$program['private_sessions']; ?> private sessions
                                <?php endif; ?>
                                <?php if (!empty($program['facility_access'])): ?>
                                    • Facility access included
                                <?php endif; ?>
                            </p>
                            <p><strong>Monthly Fee:</strong> Rs. <?php echo number_format((float)$program['monthly_fee'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Coaches Section -->
    <section class="coaches" id="coaches">
        <h2>Meet Our Coaches</h2>
        <div class="coaches-grid">
            <?php foreach (($data['coaches'] ?? []) as $coach): ?>
                <div class="coach-card">
                    <?php if (!empty($coach->image)): ?>
                        <img src="<?php echo URLROOT . '/' . ltrim((string)$coach->image, '/'); ?>" alt="<?php echo htmlspecialchars($coach->name); ?>" class="coach-photo">
                    <?php else: ?>
                        <div class="coach-avatar"></div>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($coach->name); ?></h3>
                    <p>
                        <?php echo htmlspecialchars((string)($coach->specialization ?? 'Coach')); ?>
                        <?php if (!empty($coach->experience_years)): ?>
                            • <?php echo (int)$coach->experience_years; ?> years experience
                        <?php endif; ?>
                        <?php if (!empty($coach->IsHeadCoach)): ?>
                            • Head Coach
                        <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="facilities" id="facilities">
        <div class="facilities-container">
            <h2>Facilities</h2>
            <div class="facilities-grid">
                <?php $facilityClasses = ['indoor-nets', 'outdoor-pitches', 'fitness-center']; ?>
                <?php foreach (($data['facilities'] ?? []) as $index => $facility): ?>
                    <div class="facility-card">
                        <div class="facility-image <?php echo htmlspecialchars($facilityClasses[$index % count($facilityClasses)]); ?>"></div>
                        <div class="facility-content">
                            <h3><?php echo htmlspecialchars($facility->Name); ?></h3>
                            <p>
                                Location: <?php echo htmlspecialchars((string)($facility->Location ?? 'TBA')); ?>
                                <?php if (!empty($facility->Capacity)): ?>
                                    • Capacity: <?php echo (int)$facility->Capacity; ?>
                                <?php endif; ?>
                            </p>
                            <p>
                                Status: <?php echo htmlspecialchars(ucfirst((string)($facility->AvailabilityStatus ?? 'available'))); ?>
                                • Hourly Rate: Rs. <?php echo number_format((float)($facility->HourlyRate ?? 0), 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section class="events">
        <h2>Upcoming Events</h2>
        <?php if (!empty($data['events'])): ?>
            <?php foreach ($data['events'] as $event): ?>
                <div class="event-card" style="margin-bottom: 24px;">
                    <div class="event-content">
                        <h3><?php echo htmlspecialchars((string)($event['title'] ?? 'Upcoming Event')); ?></h3>
                        <p><?php echo htmlspecialchars((string)($event['description'] ?? '')); ?></p>
                        <p>
                            <strong>Date:</strong>
                            <?php echo !empty($event['event_date']) ? date('M d, Y h:i A', strtotime((string)$event['event_date'])) : 'TBA'; ?>
                            <?php if (!empty($event['location'])): ?>
                                • <strong>Location:</strong> <?php echo htmlspecialchars((string)$event['location']); ?>
                            <?php endif; ?>
                        </p>
                        <p>
                            <strong>Type:</strong> <?php echo htmlspecialchars((string)($event['Type'] ?? 'Event')); ?>
                            <?php if (isset($event['RegistrationFee']) && $event['RegistrationFee'] !== null): ?>
                                • <strong>Fee:</strong> Rs. <?php echo number_format((float)$event['RegistrationFee'], 2); ?>
                            <?php endif; ?>
                        </p>
                        <a class="learn-more-btn" href="#contact">Contact Us</a>
                    </div>
                    <div class="event-image"></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="event-card">
                <div class="event-content">
                    <h3>No upcoming events</h3>
                    <p>New academy events will appear here as soon as they are scheduled in the system.</p>
                    <a class="learn-more-btn" href="#contact">Contact Us</a>
                </div>
                <div class="event-image"></div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="testimonials-container">
            <h2>Testimonials</h2>
            <div class="testimonials-grid">
                <?php foreach (($data['testimonials'] ?? []) as $testimonial): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar"></div>
                            <div class="testimonial-info">
                                <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                                <div class="testimonial-date">
                                    <?php echo !empty($testimonial['date']) ? date('Y-m-d', strtotime($testimonial['date'])) : ''; ?>
                                    <?php if (!empty($testimonial['category'])): ?>
                                        • <?php echo htmlspecialchars($testimonial['category']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="stars"><?php echo str_repeat('★', (int)$testimonial['rating']); ?></div>
                        <p class="testimonial-text">"<?php echo htmlspecialchars($testimonial['text']); ?>"</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<!-- Contact Section -->
<section class="contact" id="contact">
    <h2>Contact Us</h2>
    <div class="contact-container">
        <div class="contact-form">
            <h3>Send us a Message</h3>
            <p class="form-subtitle">Ready to start your cricket journey? Get in touch with us!</p>
            <form id="contactForm">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
                </div>
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" placeholder="Tell us about your cricket goals or ask any questions..." required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
        
        <div class="contact-info">
            <h3>Get In Touch</h3>
            <div class="contact-item">
                <div class="contact-icon">📍</div>
                <div class="contact-details">
                    <h4>Address</h4>
                    <p>
                        <?php if (!empty($data['contact']['address_lines'])): ?>
                            <?php echo nl2br(htmlspecialchars(implode("\n", $data['contact']['address_lines']))); ?>
                        <?php else: ?>
                            Address details will appear here once academy location records are available.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div class="contact-details">
                    <h4>Phone</h4>
                    <p>
                        <?php if (!empty($data['contact']['phones'])): ?>
                            <?php echo nl2br(htmlspecialchars(implode("\n", $data['contact']['phones']))); ?>
                        <?php else: ?>
                            Phone details will appear here once event contact records are available.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">✉️</div>
                <div class="contact-details">
                    <h4>Email</h4>
                    <p>
                        <?php if (!empty($data['contact']['emails'])): ?>
                            <?php echo nl2br(htmlspecialchars(implode("\n", $data['contact']['emails']))); ?>
                        <?php else: ?>
                            Email details will appear here once event contact records are available.
                        <?php endif; ?>
                    </p>
                </div>
                </div>
            <div class="contact-item">
                <div class="contact-icon">🕒</div>
                <div class="contact-details">
                    <h4>Operating Hours</h4>
                    <p><?php echo htmlspecialchars((string)($data['contact']['hours'] ?? '')); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.7886584480975!2d79.84312817448678!3d6.914741718382553!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae259684e2f8c75%3A0x2c8f28b3542d9e6e!2sColombo%2003%2C%20Colombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2sus!4v1690360623456!5m2!1sen!2sus" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
<script src="<?php echo URLROOT; ?>/js/home.js"></script>