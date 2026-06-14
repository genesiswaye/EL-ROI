<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StudentLancer – Connect. Work. Earn. Grow.</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>

    <!-- ===== MOBILE NAV ===== -->
    <div id="mobile-nav">
        <button id="mobile-nav-close" onclick="closeMobileNav()" aria-label="Close menu">&#10005;</button>
        <a href="#hero" onclick="closeMobileNav()">Home</a>
        <a href="#about" onclick="closeMobileNav()">About</a>
        <a href="#features" onclick="closeMobileNav()">Features</a>
        <a href="#how-it-works" onclick="closeMobileNav()">How It Works</a>
        <a href="#cta" onclick="closeMobileNav()">Contact</a>
        <div class="mobile-nav-btns">
            <a href="auth/login_selector.php" class="btn-login" style="border:1px solid #e2e8f0;border-radius:12px;width:100%;">Login</a>
            <a href="auth/register.php" class="btn-signup" style="width:100%;padding:12px 0;">Sign Up</a>
        </div>
    </div>

    <!-- ===== NAVBAR ===== -->
    <nav id="navbar">
        <div class="container">
            <div class="nav-inner">
                <a href="#hero" class="nav-logo">
                    <div class="nav-logo-icon">S</div>
                    <span class="nav-logo-text gradient-text">StudentLancer</span>
                </a>
                <div class="nav-links">
                    <a href="#hero">Home</a>
                    <a href="#about">About</a>
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#cta">Contact</a>
                </div>
                <div class="nav-actions">
                    <a href="auth/login_selector.php" class="btn-login">Login</a>
                    <a href="auth/register.php" class="btn-signup">Sign Up</a>
                    <button class="nav-hamburger" onclick="openMobileNav()" aria-label="Open menu">&#9776;</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section id="hero">
        <div class="hero-bg">
            <img src="https://images.unsplash.com/photo-1758270705172-07b53627dfcb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1920" alt="Students collaborating on campus" loading="eager" />
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="hero-inner">
                <!-- Left -->
                <div>
                    <h1 class="hero-headline">Connect. Work.<br>Earn. Grow.</h1>
                    <p class="hero-sub">
                        StudentLancer is an AI-powered university freelancing platform that connects students,
                        lecturers, organizations, and companies with verified campus talent.
                    </p>
                    <div class="hero-btns">
                        <a href="auth/register.php" class="btn-primary">Get Started <i class="fa-solid fa-arrow-right"></i></a>
                        <button class="btn-secondary"><i class="fa-solid fa-circle-play"></i> Learn More</button>
                    </div>
                    <div class="hero-badges">
                        <span class="hero-badge">&#127979; Trusted by Universities</span>
                        <span class="hero-badge">&#128101; 500+ Partner Organizations</span>
                        <span class="hero-badge">&#9733; 10K+ Student Community</span>
                    </div>
                </div>
                <!-- Right – Dashboard Mockup -->
                <div class="hero-dashboard">
                    <div class="dash-header">
                        <span class="dash-header-title">Dashboard</span>
                        <div class="dash-avatar"></div>
                    </div>
                    <div class="dash-wallet">
                        <div class="dash-wallet-label">Wallet Balance</div>
                        <div class="dash-wallet-amount">₦25,000</div>
                    </div>
                    <div class="dash-section-label">Recommended Jobs</div>
                    <div class="dash-job">
                        <div>
                            <div class="dash-job-title">UI/UX Design Project</div>
                            <div class="dash-job-price">₦15,000 – ₦30,000</div>
                        </div>
                        <span class="dash-match">AI Match</span>
                    </div>
                    <div class="dash-job">
                        <div>
                            <div class="dash-job-title">Mobile App Development</div>
                            <div class="dash-job-price">₦80,000 – ₦150,000</div>
                        </div>
                        <span class="dash-match">95%</span>
                    </div>
                    <div class="dash-escrow">
                        <div>
                            <div class="dash-escrow-label">Active Escrow</div>
                            <div class="dash-escrow-amount">₦50,000</div>
                        </div>
                        <span class="dash-escrow-status">&#128274; Protected</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURES ===== -->
    <section id="features">
        <div class="container">
            <div style="text-align:center;">
                <span class="section-tag">&#10024; Core Features</span>
                <h2 class="section-title gradient-text">Everything You Need to<br>Freelance Securely</h2>
                <p class="section-sub">Powerful features designed to make your freelancing experience smooth, secure, and successful.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)">&#128737;</div>
                    <div class="feature-title">Verified University Users</div>
                    <p class="feature-desc">Secure institutional verification for trusted interactions across the platform.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)">&#129504;</div>
                    <div class="feature-title">AI-Powered Recommendations</div>
                    <p class="feature-desc">Smart matching between jobs, skills, and applicants for optimal connections.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background:linear-gradient(135deg,#EC4899,#DB2777)">&#128274;</div>
                    <div class="feature-title">Secure Escrow Payments</div>
                    <p class="feature-desc">Protected project payments held until approval, ensuring safety for all parties.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background:linear-gradient(135deg,#14B8A6,#0D9488)">&#128172;</div>
                    <div class="feature-title">AI Chat Assistant</div>
                    <p class="feature-desc">Instant guidance and support across the platform with intelligent assistance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== HOW IT WORKS ===== -->
    <section id="how-it-works">
        <div class="container">
            <div style="text-align:center;">
                <span class="section-tag">&#128736; Process</span>
                <h2 class="section-title gradient-text">How It Works</h2>
                <p class="section-sub">Get started in five simple steps and begin your freelancing journey today.</p>
            </div>

            <!-- Desktop Steps -->
            <div class="steps-desktop">
                <div class="steps-line"></div>
                <div class="step">
                    <div class="step-icon-wrap">&#128100;</div>
                    <div class="step-card">
                        <div class="step-num">1</div>
                        <div class="step-title">Create Account</div>
                        <p class="step-desc">Sign up with your university email for instant verification.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon-wrap">&#128203;</div>
                    <div class="step-card">
                        <div class="step-num">2</div>
                        <div class="step-title">Complete Profile</div>
                        <p class="step-desc">Showcase your skills, experience, and portfolio.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon-wrap">&#128188;</div>
                    <div class="step-card">
                        <div class="step-num">3</div>
                        <div class="step-title">Post or Apply</div>
                        <p class="step-desc">Find opportunities or post projects that match your needs.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon-wrap">&#129309;</div>
                    <div class="step-card">
                        <div class="step-num">4</div>
                        <div class="step-title">Collaborate & Deliver</div>
                        <p class="step-desc">Work together using our integrated communication tools.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon-wrap">&#128176;</div>
                    <div class="step-card">
                        <div class="step-num">5</div>
                        <div class="step-title">Receive Payment</div>
                        <p class="step-desc">Get paid through our protected escrow system.</p>
                    </div>
                </div>
            </div>

            <!-- Mobile Steps -->
            <div class="steps-mobile">
                <style>
                    .step-mobile {
                        display: flex;
                        align-items: flex-start;
                        gap: 16px;
                    }

                    .step-mobile-icon {
                        width: 56px;
                        height: 56px;
                        border-radius: 50%;
                        background: var(--gradient);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.4rem;
                        flex-shrink: 0;
                    }

                    .step-mobile-body {
                        background: #fff;
                        border-radius: var(--radius-xl);
                        padding: 18px 20px;
                        box-shadow: var(--shadow-lg);
                        border: 1px solid rgba(0, 0, 0, .06);
                        flex: 1;
                    }

                    .step-mobile-title {
                        font-weight: 700;
                        margin-bottom: 6px;
                    }

                    .step-mobile-desc {
                        font-size: .85rem;
                        color: var(--gray-600);
                    }
                </style>
                <div class="step-mobile">
                    <div class="step-mobile-icon">&#128100;</div>
                    <div class="step-mobile-body">
                        <div class="step-mobile-title">1. Create Account</div>
                        <p class="step-mobile-desc">Sign up with your university email for instant verification.</p>
                    </div>
                </div>
                <div class="step-mobile">
                    <div class="step-mobile-icon">&#128203;</div>
                    <div class="step-mobile-body">
                        <div class="step-mobile-title">2. Complete Profile</div>
                        <p class="step-mobile-desc">Showcase your skills, experience, and portfolio.</p>
                    </div>
                </div>
                <div class="step-mobile">
                    <div class="step-mobile-icon">&#128188;</div>
                    <div class="step-mobile-body">
                        <div class="step-mobile-title">3. Post or Apply</div>
                        <p class="step-mobile-desc">Find opportunities or post projects that match your needs.</p>
                    </div>
                </div>
                <div class="step-mobile">
                    <div class="step-mobile-icon">&#129309;</div>
                    <div class="step-mobile-body">
                        <div class="step-mobile-title">4. Collaborate & Deliver</div>
                        <p class="step-mobile-desc">Work together using our integrated communication tools.</p>
                    </div>
                </div>
                <div class="step-mobile">
                    <div class="step-mobile-icon">&#128176;</div>
                    <div class="step-mobile-body">
                        <div class="step-mobile-title">5. Receive Payment</div>
                        <p class="step-mobile-desc">Get paid through our protected escrow system.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ABOUT ===== -->
    <section id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-img-wrap">
                    <div class="about-img-glow"></div>
                    <img class="about-img" src="https://images.unsplash.com/photo-1739292774739-ee38cd9a5735?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080" alt="Students collaborating" loading="lazy" />
                    <div class="about-float">
                        <div class="about-float-icon">&#128200;</div>
                        <div>
                            <div class="about-float-value gradient-text">10K+</div>
                            <div class="about-float-label">Active Users</div>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="section-tag">&#127979; About Us</span>
                    <h2 class="about-title gradient-text">About StudentLancer</h2>
                    <p class="about-text">StudentLancer is a university-focused freelancing platform designed to help students gain real-world experience, earn income, and connect with trusted employers.</p>
                    <p class="about-text">Through AI-powered recommendations, secure escrow payments, institutional verification, and intelligent communication tools, StudentLancer creates a safe and productive digital workspace for campus talent.</p>
                    <div class="about-stats">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)">&#127942;</div>
                            <div class="stat-value gradient-text">100+</div>
                            <div class="stat-label">Skills Supported</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)">&#129504;</div>
                            <div class="stat-value gradient-text">AI-Powered</div>
                            <div class="stat-label">Smart Matching</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:linear-gradient(135deg,#EC4899,#DB2777)">&#128274;</div>
                            <div class="stat-value gradient-text">Secure</div>
                            <div class="stat-label">Escrow Transactions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:linear-gradient(135deg,#14B8A6,#0D9488)">&#10003;</div>
                            <div class="stat-value gradient-text">Verified</div>
                            <div class="stat-label">Campus Community</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== BENEFITS ===== -->
    <section id="benefits">
        <div class="container">
            <div style="text-align:center;">
                <span class="section-tag">&#127881; Benefits</span>
                <h2 class="section-title gradient-text">Benefits for Everyone</h2>
                <p class="section-sub">StudentLancer empowers every member of the university ecosystem.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)">&#127979;</div>
                    <h3 class="benefit-title">For Students</h3>
                    <ul class="benefit-list">
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)"></span>Earn income while studying</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)"></span>Build professional portfolios</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)"></span>Gain real-world experience</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)"></span>Network with industry professionals</li>
                    </ul>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)">&#128218;</div>
                    <h3 class="benefit-title">For Lecturers</h3>
                    <ul class="benefit-list">
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)"></span>Hire trusted student talent</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)"></span>Manage research projects</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)"></span>Monitor progress in real-time</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)"></span>Support student development</li>
                    </ul>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon" style="background:linear-gradient(135deg,#EC4899,#DB2777)">&#127970;</div>
                    <h3 class="benefit-title">For Companies</h3>
                    <ul class="benefit-list">
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#EC4899,#DB2777)"></span>Access verified students</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#EC4899,#DB2777)"></span>Reduce hiring costs significantly</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#EC4899,#DB2777)"></span>Discover emerging talent early</li>
                        <li><span class="benefit-dot" style="background:linear-gradient(135deg,#EC4899,#DB2777)"></span>Build campus partnerships</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== AI & ESCROW ===== -->
    <section id="ai-escrow">
        <div class="container">
            <div style="text-align:center;">
                <span class="section-tag">&#129504; Technology</span>
                <h2 class="section-title gradient-text">AI-Powered &amp; Secure</h2>
                <p class="section-sub">Intelligent matching meets bulletproof payment protection.</p>
            </div>
            <div class="ai-escrow-grid">
                <!-- AI Card -->
                <div class="ai-card">
                    <div class="ai-card-header">
                        <div class="ai-card-icon">&#129504;</div>
                        <h3 class="ai-card-title">AI Recommendation Engine</h3>
                    </div>
                    <div class="ai-features-box">
                        <div class="ai-feature-item"><span class="ai-dot"></span>Analyzes your skills and experience</div>
                        <div class="ai-feature-item"><span class="ai-dot"></span>Matches you with relevant opportunities</div>
                        <div class="ai-feature-item"><span class="ai-dot"></span>Learns from your preferences over time</div>
                        <div class="ai-feature-item"><span class="ai-dot"></span>Suggests optimal pricing for projects</div>
                    </div>
                    <div class="ai-match-box">
                        <div class="ai-match-row">
                            <span class="ai-match-label">Recommended for You</span>
                            <span class="ai-match-badge">95% Match</span>
                        </div>
                        <p class="ai-match-job">Mobile App Development</p>
                        <p class="ai-match-price">₦80,000 – ₦150,000</p>
                    </div>
                </div>
                <!-- Escrow Card -->
                <div class="escrow-card">
                    <h3 class="escrow-title">Escrow Payment Workflow</h3>
                    <div class="escrow-step">
                        <div class="escrow-step-icon" style="background:linear-gradient(135deg,#60A5FA,#3B82F6)">&#128179;</div>
                        <span class="escrow-step-label">Wallet Funding</span>
                    </div>
                    <div class="escrow-arrow"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="escrow-step">
                        <div class="escrow-step-icon" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)">&#128274;</div>
                        <span class="escrow-step-label">Escrow Created</span>
                    </div>
                    <div class="escrow-arrow"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="escrow-step">
                        <div class="escrow-step-icon" style="background:linear-gradient(135deg,#EC4899,#DB2777)">&#128196;</div>
                        <span class="escrow-step-label">Work Submitted</span>
                    </div>
                    <div class="escrow-arrow"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="escrow-step">
                        <div class="escrow-step-icon" style="background:linear-gradient(135deg,#14B8A6,#0D9488)">&#128269;</div>
                        <span class="escrow-step-label">Employer Review</span>
                    </div>
                    <div class="escrow-arrow"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="escrow-step">
                        <div class="escrow-step-icon" style="background:linear-gradient(135deg,#10B981,#059669)">&#9989;</div>
                        <span class="escrow-step-label">Payment Released</span>
                    </div>
                    <div class="escrow-note">
                        <p class="escrow-note-small">Your funds are always protected</p>
                        <p class="escrow-note-big gradient-text">100% Secure</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== TESTIMONIALS ===== -->
    <section id="testimonials">
        <div class="container">
            <div style="text-align:center;">
                <span class="section-tag">&#128172; Testimonials</span>
                <h2 class="section-title gradient-text">Trusted by Thousands</h2>
                <p class="section-sub">See what our community has to say about their experience.</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-quote-bg">"</div>
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="testimonial-text">"StudentLancer helped me earn ₦200,000 in my first semester while building a strong portfolio. The AI recommendations are incredibly accurate!"</p>
                    <div class="testimonial-profile">
                        <img class="testimonial-avatar" src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=200" alt="Chioma Okafor" loading="lazy" />
                        <div>
                            <div class="testimonial-name">Chioma Okafor</div>
                            <div class="testimonial-role">Computer Science Student</div>
                            <div class="testimonial-uni">University of Lagos</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-quote-bg">"</div>
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="testimonial-text">"The platform makes it easy to find talented students for research projects. The escrow system ensures quality work and timely delivery."</p>
                    <div class="testimonial-profile">
                        <img class="testimonial-avatar" src="https://images.unsplash.com/photo-1548810020-ea2f1da35cff?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=200" alt="Dr. Adebayo Johnson" loading="lazy" />
                        <div>
                            <div class="testimonial-name">Dr. Adebayo Johnson</div>
                            <div class="testimonial-role">Senior Lecturer</div>
                            <div class="testimonial-uni">Covenant University</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-quote-bg">"</div>
                    <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="testimonial-text">"We've hired 15 students through StudentLancer. The verification system gives us confidence that we're working with genuine university talent."</p>
                    <div class="testimonial-profile">
                        <img class="testimonial-avatar" src="https://images.unsplash.com/photo-1544168190-79c17527004f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=200" alt="Sarah Adeleke" loading="lazy" />
                        <div>
                            <div class="testimonial-name">Sarah Adeleke</div>
                            <div class="testimonial-role">HR Manager</div>
                            <div class="testimonial-uni">TechStart Nigeria</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section id="cta">
        <div class="cta-glow"></div>
        <div class="container">
            <div class="cta-inner">
                <h2 class="cta-title">Ready to Start Your<br>Freelancing Journey?</h2>
                <p class="cta-text">Join a trusted community of students and employers building opportunities together.</p>
                <div class="cta-btns">
                    <a href="auth/register.php" class="btn-cta-primary">Sign Up <i class="fa-solid fa-arrow-right"></i></a>
                    <!-- <button class="btn-cta-secondary"><i class="fa-solid fa-magnifying-glass"></i> Browse Opportunities</button> -->
                </div>
                <div class="cta-stats">
                    <div class="cta-stat">
                        <div class="cta-stat-value">10+</div>
                        <div class="cta-stat-label">Active Students</div>
                    </div>
                    <div class="cta-stat">
                        <div class="cta-stat-value">5+</div>
                        <div class="cta-stat-label">Organizations</div>
                    </div>
                    <div class="cta-stat">
                        <div class="cta-stat-value">₦5M+</div>
                        <div class="cta-stat-label">Earned</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <div class="footer-logo-icon">S</div>
                        <span class="footer-logo-text gradient-text">StudentLancer</span>
                    </div>
                    <p class="footer-desc">Connecting university talent with opportunities across Nigeria through AI-powered matching and secure payments.</p>
                    <div class="footer-socials">
                        <a href="#" class="footer-social"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="footer-social"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="footer-social"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="footer-social"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="footer-col-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#hero">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#cta">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-col-title">Platform</h4>
                    <ul class="footer-links">
                        <li><a href="#">Browse Jobs</a></li>
                        <li><a href="#">AI Recommendations</a></li>
                        <li><a href="#">Escrow Payments</a></li>
                        <li><a href="#">Chat Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-col-title">Contact</h4>
                    <div class="footer-contact-item"><i class="fa-solid fa-envelope"></i> genesisighomwaye@gmail.com</div>
                    <div class="footer-contact-item"><i class="fa-solid fa-phone"></i> +234 902 684 0678</div>
                    <div class="footer-contact-item"><i class="fa-solid fa-location-dot"></i> Lagos, Nigeria</div>
                    <div class="footer-partner">
                        <div class="footer-partner-label">University Partnerships</div>
                        <a href="auth/company_register.php" class="footer-partner-link">Partner with us &rarr;</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copy">© 2026 StudentLancer. All Rights Reserved.</p>
                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        /* Navbar scroll */
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        /* Mobile nav */
        function openMobileNav() {
            document.getElementById('mobile-nav').classList.add('open');
        }

        function closeMobileNav() {
            document.getElementById('mobile-nav').classList.remove('open');
        }
    </script>
</body>

</html>