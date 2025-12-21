<?php
require_once "../../_base.php";
$_title = 'Fix & GO | About Us';
include_once "../../_head.php";
?>
<link rel="stylesheet" href="<?= $rootDir ?>/css/aboutUs.css" />
<main class="about-us-main">
    <!-- Hero Section -->
    <section class="about-us-hero">
        <div class="hero-content">
            <h1 class="about-us-title hero-title">About <span class="highlight">Fix&Go</span></h1>
            <p class="hero-subtitle">Transforming Maintenance into Excellence Since 2010</p>
        </div>
        <div class="hero-image">
            <img src="<?= $rootDir ?>/images/About_US_Banner.png" alt="Fix&Go professional team" />
        </div>
    </section>

    <!-- Main Content -->
    <div class="about-us-container">
        <!-- Introduction -->
        <section class="content-section">
            <div class="section-header">
                <div class="icon-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h2 class="section-title">Our Story</h2>
            </div>
            <div class="section-content">
                <p class="about-us-p">Fix&Go is a dedicated service provider offering a wide range of repair, maintenance, and technical support solutions for both residential and commercial clients. Established with the aim of simplifying maintenance needs, our company focuses on delivering service excellence through professional workmanship, efficient operations, and strong customer engagement.</p>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">15K+</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Satisfaction Rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Expert Technicians</div>
                    </div>
                </div>
                
                <p class="about-us-p">Over the years, Fix&Go has built a reputation for reliability, quality, and trust, making us a preferred choice among households, offices, and businesses seeking dependable maintenance services.</p>
            </div>
        </section>

        <!-- Expertise Section -->
        <section class="content-section highlighted">
            <div class="section-header">
                <div class="icon-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="section-title">Our Expertise</h2>
            </div>
            <div class="section-content">
                <p class="about-us-p">Our operations are supported by a team of qualified and experienced technicians who possess in-depth knowledge across various areas of repair and maintenance. We ensure our personnel are continuously trained and updated on the latest tools, technologies, and industry best practices to guarantee that every job is completed with precision and adherence to safety standards.</p>
                
                <div class="features-grid">
                    <div class="feature">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">Certified & Insured Professionals</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">Latest Tools & Equipment</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">24/7 Emergency Service</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">Quality Guarantee on All Work</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision Cards -->
        <div class="cards-grid">
            <div class="card mission-card">
                <div class="card-header">
                    <div class="card-icon">🎯</div>
                    <h3 class="card-title">Our Mission</h3>
                </div>
                <p class="card-text">To deliver superior repair and maintenance services that prioritise safety, effectiveness, and long-term value. We aim to simplify the customer experience by offering reliable support, timely responses, and solutions that enhance comfort, functionality, and peace of mind.</p>
            </div>
            
            <div class="card vision-card">
                <div class="card-header">
                    <div class="card-icon">🚀</div>
                    <h3 class="card-title">Our Vision</h3>
                </div>
                <p class="card-text">To become a leading service provider recognised for outstanding quality, innovation, and professionalism in the repair and maintenance industry. We aspire to set new benchmarks for service standards and consistently exceed customer expectations.</p>
            </div>
        </div>

        <!-- Core Values -->
        <section class="content-section">
            <div class="section-header">
                <div class="icon-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="section-title">Our Core Values</h2>
            </div>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-number">01</div>
                    <h3 class="value-title">Professionalism</h3>
                    <p class="value-text">We conduct our business with discipline, responsibility, and respect. Every interaction reflects our commitment to ethical behavior and high service standards.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-number">02</div>
                    <h3 class="value-title">Reliability</h3>
                    <p class="value-text">We strive to be dependable in every task we undertake. Clients trust us because we deliver on time, respond quickly, and ensure consistent service quality.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-number">03</div>
                    <h3 class="value-title">Quality Assurance</h3>
                    <p class="value-text">We focus on delivering durable results by using proper tools, following correct procedures, and maintaining strict quality control in all service activities.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-number">04</div>
                    <h3 class="value-title">Customer-Centric Approach</h3>
                    <p class="value-text">We place our customers at the center of our operations. We listen attentively, provide personalized solutions, and ensure every client feels valued and supported.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <h2 class="cta-title">Ready to Experience the Fix&Go Difference?</h2>
            <p class="cta-text">Contact us today for reliable, professional service you can trust.</p>
            <a href="<?= homePageURL() ?>" class="cta-button">Get Your Free Quote</a>
        </section>
    </div>
</main>
<?php
include_once "../../_foot.php";
