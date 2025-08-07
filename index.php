<?php 
require_once 'config.php';
require_once 'header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to <span>Football Club</span> Management System</h1>
            <p class="hero-subtitle">Professional tools to manage your football club with ease</p>
            
            <?php if (!isLoggedIn()): ?>
                <div class="hero-actions">
                    <a href="login.php" class="btn btn-login">Login</a>
                    <a href="register.php" class="btn btn-register">Register</a>
                </div>
            <?php else: ?>
                <div class="hero-actions">
                    <a href="dashboard.php" class="btn btn-dashboard">Go to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="hero-image-container">
            <img src="assets/football-hero.jpg" alt="Football Team Celebration" class="hero-image">
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <h2 class="section-title"><span>Key</span> Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Player Management</h3>
                <p>Comprehensive player profiles with stats, medical records, and performance tracking</p>
                <div class="feature-underline"></div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">👔</div>
                <h3>Staff Management</h3>
                <p>Organize your coaching team, medical staff, and administrative personnel</p>
                <div class="feature-underline"></div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚽</div>
                <h3>Team Organization</h3>
                <p>Manage multiple teams, squads, and training groups efficiently</p>
                <div class="feature-underline"></div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Match Center</h3>
                <p>Schedule matches, track results, and analyze team performance</p>
                <div class="feature-underline"></div>
            </div>
        </div>
    </section>

    <!-- Stats Preview -->
    <section class="stats-section">
        <div class="stat-item">
            <div class="stat-number" data-count="100">0</div>
            <div class="stat-label">Players Managed</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" data-count="50">0</div>
            <div class="stat-label">Matches Scheduled</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" data-count="24">0</div>
            <div class="stat-label">Staff Members</div>
        </div>
    </section>

    <!-- Testimonial -->
    <section class="testimonial-section">
        <div class="testimonial-card">
            <div class="testimonial-quote">"This system revolutionized our club management. Everything we need in one place!"</div>
            <div class="testimonial-author">- John Smith, Club Manager</div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2 class="wh">Ready to Transform Your Club Management?</h2>
        <p>Join hundreds of clubs already using our system</p>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-cta">Get Started Now</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-cta">Continue to Dashboard</a>
        <?php endif; ?>
    </section>
</div>

<style>
    /* Base Styles */
    :root {
        --primary-color: #0066cc;
        --primary-dark: #0055aa;
        --secondary-color: #ff6b00;
        --light-bg: #f8f9fa;
        --dark-text: #2c3e50;
        --light-text: #ffffff;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: var(--dark-text);
        background-color: #ffffff;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        overflow: hidden;
    }
    
    /* Hero Section */
    .hero-section {
        display: flex;
        align-items: center;
        gap: 40px;
        margin: 60px 0;
    }
    
    .hero-content {
        flex: 1;
        animation: fadeInLeft 1s ease;
    }
    
    .hero-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
    }
    
    .hero-title span {
        color: var(--primary-color);
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
        color: #555;
        margin-bottom: 30px;
    }
    .wh{color:white;}
    
    .hero-image-container {
        flex: 1;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow);
        animation: fadeInRight 1s ease;
    }
    
    .hero-image {
        width: 100%;
        height: auto;
        display: block;
        transition: var(--transition);
    }
    
    .hero-image:hover {
        transform: scale(1.02);
    }
    
    /* Buttons */
    .btn {
        display: inline-block;
        padding: 12px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
        margin-right: 15px;
        border: none;
        cursor: pointer;
    }
    
    .btn-login {
        background-color: var(--primary-color);
        color: var(--light-text);
    }
    
    .btn-login:hover {
        background-color: var(--primary-dark);
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 102, 204, 0.2);
    }
    
    .btn-register {
        background-color: transparent;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }
    
    .btn-register:hover {
        background-color: var(--primary-color);
        color: var(--light-text);
        transform: translateY(-3px);
    }
    
    .btn-dashboard, .btn-cta {
        background-color: var(--secondary-color);
        color: var(--light-text);
    }
    
    .btn-dashboard:hover, .btn-cta:hover {
        background-color: #e05e00;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(255, 107, 0, 0.2);
    }
    
    /* Features Section */
    .section-title {
        text-align: center;
        font-size: 2.2rem;
        margin: 80px 0 40px;
        position: relative;
    }
    
    .section-title span {
        color: var(--primary-color);
    }
    
    .section-title::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: var(--primary-color);
        margin: 15px auto;
        border-radius: 2px;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin: 50px 0;
    }
    
    .feature-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: var(--shadow);
        transition: var(--transition);
        text-align: center;
        border-top: 4px solid var(--primary-color);
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        display: inline-block;
    }
    
    .feature-card h3 {
        font-size: 1.4rem;
        margin: 15px 0;
        color: var(--primary-color);
    }
    
    .feature-underline {
        width: 50px;
        height: 3px;
        background: var(--primary-color);
        margin: 20px auto 0;
        border-radius: 3px;
    }
    
    /* Stats Section */
    .stats-section {
        display: flex;
        justify-content: space-around;
        margin: 80px 0;
        padding: 40px 0;
        background: linear-gradient(135deg, var(--primary-color), #004488);
        border-radius: 10px;
        color: white;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .stat-label {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    /* Testimonial Section */
    .testimonial-section {
        margin: 80px 0;
    }
    
    .testimonial-card {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
        background: var(--light-bg);
        border-radius: 10px;
        position: relative;
        box-shadow: var(--shadow);
    }
    
    .testimonial-card::before {
        content: '"';
        font-size: 5rem;
        color: var(--primary-color);
        opacity: 0.2;
        position: absolute;
        top: 20px;
        left: 20px;
    }
    
    .testimonial-quote {
        font-size: 1.3rem;
        font-style: italic;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }
    
    .testimonial-author {
        font-weight: 600;
        text-align: right;
        color: var(--primary-color);
    }
    
    /* CTA Section */
    .cta-section {
        text-align: center;
        padding: 60px 20px;
        margin: 80px 0;
        background: linear-gradient(135deg, var(--primary-color), #004488);
        color: white;
        border-radius: 10px;
        box-shadow: var(--shadow);
    }
    
    .cta-section h2 {
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .cta-section p {
        font-size: 1.1rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }
    
    /* Animations */
    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-section {
            flex-direction: column;
        }
        
        .hero-content, .hero-image-container {
            width: 100%;
        }
        
        .hero-title {
            font-size: 2.2rem;
        }
        
        .stats-section {
            flex-direction: column;
            gap: 30px;
        }
        
        .btn {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
    }
</style>

<script>
    // Simple counter animation for stats
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;
        
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-count');
            const count = +counter.innerText;
            const increment = target / speed;
            
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(updateCount, 1);
            } else {
                counter.innerText = target;
            }
            
            function updateCount() {
                const count = +counter.innerText;
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            }
        });
    });
</script>

<?php require_once 'footer.php'; ?>