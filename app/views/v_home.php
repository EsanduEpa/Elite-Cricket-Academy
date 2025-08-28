<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Elite Cricket Academy</h1>
            <button class="explore-btn">Explore Programs</button>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <h2>About Elite Cricket Academy</h2>
        <p>Elite Cricket Academy is dedicated to nurturing the next generation of cricket stars. Our comprehensive training programs, led by experienced coaches, focus on skill development, tactical understanding, and physical conditioning. We provide state-of-the-art facilities and a supportive environment to help players reach their full potential.</p>
        
        <p>Founded with a vision to create world-class cricketers, our academy combines traditional cricket values with modern training methodologies. We believe in developing not just skilled players, but also individuals with strong character, leadership qualities, and sportsmanship.</p>
        
        <p>Our state-of-the-art facilities include indoor and outdoor training nets, professional-grade equipment, and a dedicated fitness center. We offer year-round programs for players of all skill levels, from beginners to advanced players aspiring to play at the highest levels.</p>
        
        <p>With a track record of producing successful players who have represented their countries and played in major leagues worldwide, Elite Cricket Academy continues to be the preferred choice for serious cricket development. Join us in your journey to cricket excellence.</p>
    </section>

    <!-- Programs Section -->
    <section class="programs" id="programs">
        <div class="programs-container">
            <h2>Our Programs</h2>
            <div class="programs-grid">
                <div class="program-card">
                    <div class="program-image youth-program"></div>
                    <div class="program-content">
                        <h3>Youth Cricket Program</h3>
                        <p>Develop fundamental and advanced game essentials in a fun and challenging environment.</p>
                    </div>
                </div>
                <div class="program-card">
                    <div class="program-image advanced-training"></div>
                    <div class="program-content">
                        <h3>Advanced Cricket Training</h3>
                        <p>Intensive training for aspiring professionals, focusing on advanced techniques and strategies.</p>
                    </div>
                </div>
                <div class="program-card">
                    <div class="program-image coaching-camps"></div>
                    <div class="program-content">
                        <h3>Specialized Coaching Camps</h3>
                        <p>Short-term, focused training sessions on specific aspects of the game, led by expert coaches.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Coaches Section -->
    <section class="coaches" id="coaches">
        <h2>Meet Our Coaches</h2>
        <div class="coaches-grid">
            <div class="coach-card">
                <img src="<?php echo URLROOT; ?>/img/coach1.jpg" alt="Coach Alex Turner" class="coach-photo">
                <h3>Kumara Darmasena</h3>
                <p>Former National Team Player</p>
            </div>
            <div class="coach-card">
                <img src="<?php echo URLROOT; ?>/img/coach2.jpg" alt="Coach Ben Carter" class="coach-photo">
                <h3>Ben Carter</h3>
                <p>Certified Cricket Coach</p>
            </div>
            <div class="coach-card">
                <img src="<?php echo URLROOT; ?>/img/coach3.webp" alt="Coach Chris Evans" class="coach-photo">
                <h3>Kumara Sangakkara</h3>
                <p>Specialist Batting Coach</p>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="facilities" id="facilities">
        <div class="facilities-container">
            <h2>Facilities</h2>
            <div class="facilities-grid">
                <div class="facility-card">
                    <div class="facility-image indoor-nets"></div>
                    <div class="facility-content">
                        <h3>Indoor Training Nets</h3>
                        <p>State-of-the-art indoor nets for year-round training, equipped with advanced technology.</p>
                    </div>
                </div>
                <div class="facility-card">
                    <div class="facility-image outdoor-pitches"></div>
                    <div class="facility-content">
                        <h3>Outdoor Practice Pitches</h3>
                        <p>Full-sized outdoor pitches providing realistic match conditions for practice.</p>
                    </div>
                </div>
                <div class="facility-card">
                    <div class="facility-image fitness-center"></div>
                    <div class="facility-content">
                        <h3>Accessories Shop & Rental</h3>
                        <p>We offer a wide range of cricket accessories for purchase and rental.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section class="events">
        <h2>Upcoming Events</h2>
        <div class="event-card">
            <div class="event-content">
                <h3>Summer Cricket Camp</h3>
                <p>Join our intensive summer camp for skill development and match practice.</p>
                <button class="learn-more-btn">Learn More</button>
            </div>
            <div class="event-image"></div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="testimonials-container">
            <h2>Testimonials</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="<?php echo URLROOT; ?>/img/coach1.jpg" alt="Ethan Harper" class="testimonial-photo">
                        <div class="testimonial-info">
                            <h4>Kapila Hewage</h4>
                            <div class="testimonial-date">2025-06-15</div>
                        </div>
                    </div>
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"Elite Cricket Academy has transformed my game. The coaches are incredibly knowledgeable and supportive, and the facilities are top-notch. I've seen significant improvement in my batting and overall performance."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="<?php echo URLROOT; ?>/img/coach2.jpg" alt="Kaveesha Kawindya" class="testimonial-photo">
                        <div class="testimonial-info">
                            <h4>Kaveesha Kawindya</h4>
                            <div class="testimonial-date">2025-07-02</div>
                        </div>
                    </div>
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"The training programs are well-structured and challenging. The coaches push you to be your best, and the competitive environment is great for growth. Highly recommend!"</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="<?php echo URLROOT; ?>/img/coach3.webp" alt="Kumara Dissanayala" class="testimonial-photo">
                        <div class="testimonial-info">
                            <h4>Kumara Dissanayala</h4>
                            <div class="testimonial-date">2025-06-10</div>
                        </div>
                    </div>
                    <div class="stars">★★★★★</div>
                    <p class="testimonial-text">"The specialized coaching camps are fantastic. I attended the batting camp and learned so much in a short amount of time. The coaches provided personalized feedback that helped me refine my technique."</p>
                </div>
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
                    <p>123 Cricket Ground Road<br>Sports Complex, Colombo 03<br>Sri Lanka</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div class="contact-details">
                    <h4>Phone</h4>
                    <p>+94 11 234 5678<br>+94 77 123 4567</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">✉️</div>
                <div class="contact-details">
                    <h4>Email</h4>
                    <p>info@elitecricketacademy.lk<br>admissions@elitecricketacademy.lk</p>
                </div>
                </div>
            <div class="contact-item">
                <div class="contact-icon">🕒</div>
                <div class="contact-details">
                    <h4>Operating Hours</h4>
                    <p>Monday - Friday: 6:00 AM - 10:00 PM<br>Saturday - Sunday: 7:00 AM - 8:00 PM</p>
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