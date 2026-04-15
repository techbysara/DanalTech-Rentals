<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals — About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="landing-page">

    <!-- Navbar -->
    <nav class="landing-nav">
        <a href="index.php" class="dtr-logo">
            <div class="logo-badge">
                <span class="logo-d">D</span>
                <span class="logo-t">T</span>
                <span class="logo-r">R</span>
            </div>
            <div class="logo-text-block">
                <span class="logo-name">DanalTech</span>
                <span class="logo-sub">Rentals</span>
            </div>
        </a>
        <div class="nav-right">
            <a href="about.php" class="nav-link-landing">About Us</a>
            <a href="contact.php" class="nav-link-landing">Contact Us</a>
            <button class="theme-toggle" id="themeToggleBtn" onclick="toggleTheme()">Dark</button>
            <a href="login.php" class="btn-nav-login">Login</a>
            <a href="register.php" class="btn-nav-register">Register With Us</a>
        </div>
    </nav>

    <!-- Page Hero -->
    <section class="about-hero">
        <div class="about-hero-inner">
            <span class="section-label">About DanalTech</span>
            <h1 class="about-hero-title">We are more than a<br>rental company.</h1>
            <p class="about-hero-tagline">
                We are a community built on trust, quality, and the belief 
                that everyone deserves access to the tools they need to thrive.
            </p>
        </div>
    </section>

    <!-- Our Story -->
    <section class="about-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <span class="section-label">Our Story</span>
                    <h2 class="section-title">Born with one aim.<br>Built for everyone.</h2>
                    <p class="section-text">
                        DanalTech Rentals was born with one aim in mind, to make 
                        high-spec equipment accessible to anyone who needs it, 
                        without the burden of ownership.

                    </p>
                    <p class="section-text mt-4">
                        We watched students spend hours searching for affordable 
                        high-spec laptops for video rendering, graphic design, and game 
                        development — only to settle for something slow that held them back. 
                        We watched remote workers lose job opportunities simply because they 
                        didn't have the right device. And we saw people buying expensive 
                        equipment upfront, only to be stuck with it when they no longer 
                        needed it — unable to sell it for a fair price.
                    </p>
                    <p class="section-text mt-4">
                        DanalTech exists to change that. Whether you are a student, 
                        a creative, a gamer, or a professional — you deserve access to 
                        top-of-the-range equipment without the financial burden of ownership. 
                        Rent what you need, return when you are done, and never settle for less again.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="mission-card">
                        <span class="section-label">Our Mission</span>
                        <h3 class="mission-title">Empowering people<br>through access.</h3>
                        <p class="section-text">
                            To provide individuals with access to carefully curated, 
                            high-specification equipment — empowering them to work, create, 
                            and thrive without the pressure of upfront costs 
                            or long-term commitments.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mission-card">
                        <span class="section-label">Our Vision</span>
                        <h3 class="mission-title">Growing together<br>as a community.</h3>
                        <p class="section-text">
                            To grow into a trusted membership platform where our community 
                            helps shape our inventory. We envision a future where long-term 
                            members enjoy expanded rental limits, where members can request 
                            specific devices, and where every user feels not just like a 
                            customer — but like they belong.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="features-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Our Values</span>
                <h2 class="section-title">What we stand for.</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="landing-feature-card">
                        <span class="feature-number">01</span>
                        <h4>People First</h4>
                        <p>
                            Every decision we make starts with one question — 
                            does this make life easier for our members? 
                            You are at the centre of everything we do.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="landing-feature-card">
                        <span class="feature-number">02</span>
                        <h4>Quality Without Compromise</h4>
                        <p>
                            Every device in our inventory is carefully selected 
                            and quality-assured. When you rent from DanalTech, 
                            you rent with confidence.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="landing-feature-card">
                        <span class="feature-number">03</span>
                        <h4>Growth Together</h4>
                        <p>
                            We grow as our members grow. Your feedback shapes 
                            our inventory, our policies, and our future. 
                            This is your platform as much as ours.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="about-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Why Choose Us</span>
                <h2 class="section-title">The DanalTech difference.</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="why-card">
                        <h4>Quality Assured Equipment</h4>
                        <p>
                            Every device in our inventory is carefully inspected 
                            and maintained to the highest standard. When you rent 
                            from DanalTech, you receive equipment that is fast, 
                            reliable, and ready to perform — because your time 
                            is too valuable for anything less.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="why-card">
                        <h4>Flexible Rental Periods</h4>
                        <p>
                            Rent for as long as you need and extend whenever 
                            you want. We work around your schedule, not the 
                            other way around. Return when you're ready — 
                            no pressure, no penalties.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="why-card">
                        <h4>Dedicated Support</h4>
                        <p>
                            Our support team is here when you need us. 
                            Whether you have a question, notice an issue, 
                            or simply need guidance — we are available to help. 
                            Because renting from DanalTech means you are 
                            never on your own.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <h2>Ready to get started?</h2>
                <p>
                    You are one click away from becoming a DTR member. 
                    Join us by creating your free account today and start 
                    browsing our latest, industry-leading equipment. 
                    Rent what you need, return when you're done.
                </p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn-hero-primary">Register With Us</a>
                    <a href="login.php"    class="btn-cta-secondary">Login</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
     <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php include 'includes/theme.php'; ?>
</body>
</html>