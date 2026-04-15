<?php
session_start();

if (isset($_SESSION['userRole'])) {
    if ($_SESSION['userRole'] === 'Admin') {
        header("Location: danaltech-admin/dashboard.php");
        exit();
    } else {
        header("Location: user-hub/dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals — Stop stressing about gear.</title>
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

    <!-- Hero Page-->
    <section class="hero-section">
        <div class="hero-inner">
            <div class="hero-badge">Rent smart. Work better. Zero stress</div>
            <h1 class="hero-title">
                Making your<br>work & life <span class="accent">easier.</span>
            </h1>
            <p class="hero-tagline">
                DanalTech gives you access to carefully curated, top-of-the-range equipment, designed to power 
                your workflow and elevate your everyday experience.
            </p>
            <div class="hero-buttons">
                <a href="register.php" class="btn-hero-primary">Start Renting Today</a>
                <a href="login.php"    class="btn-hero-secondary">Login to Account</a>
            </div>
        </div>
    </section>

    <!-- About Us -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-label">About DanalTech</span>
                    <h2 class="section-title">We've got you covered.</h2>
                    <p class="section-text">
                        DanalTech Rentals was built for students and remote workers 
                        who need professional equipment without the professional price tag. 
                        Whether you need a laptop for a deadline, a monitor for a project, 
                        or accessories to complete your setup — we have it ready to rent.
                    </p>
                    <p class="section-text mt-3">
                        Our platform is simple, secure, and designed around you. Browse, 
                        rent in seconds, and return when you're done. 
                        No paperwork. No stress. Just gear.
                    </p>
                    <div class="about-stats">
                        <div class="about-stat">
                            <h3>7</h3>
                            <p>Max items per user</p>
                        </div>
                        <div class="about-stat">
                            <h3>10+</h3>
                            <p>Equipment categories</p>
                        </div>
                        <div class="about-stat">
                            <h3>24/7</h3>
                            <p>Platform access</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-card">
                        <h4>Built for your lifestyle</h4>
                        <p>
                            Whether you're a student pulling an all-nighter or a remote 
                            worker racing a deadline, DanalTech has the equipment to keep 
                            you moving. No long queues, no complicated forms, 
                            just browse, rent, and get to work.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Equipment -->
    <section class="features-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">Featured Equipment</span>
                <h2 class="section-title">Popular rentals this week.</h2>
            </div>
            <div class="row g-4">

                <!-- MacBook Pro -->
                <div class="col-md-4">
                    <div class="landing-feature-card" style="padding:0; overflow:hidden;">
                        <img src="images/equipment/default.jpg"
                        alt="MacBook Pro"
                        style="width:100%; height:200px; object-fit:contain; background:#FFF4EE; padding:16px;">
                        <div style="padding: 20px;">
                            <p style="color:#A08070; font-size:0.8rem; margin-bottom:6px;">Laptops & PCs</p>
                            <h4 style="color:#2C1810; font-weight:700; margin-bottom:16px;">MacBook Pro 14"</h4>
                            <a href="register.php" class="btn-hero-primary" style="display:inline-block; font-size:0.85rem; padding:10px 20px;">
                                Start Renting
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Remote Worker Kit -->
                <div class="col-md-4">
                    <div class="landing-feature-card" style="padding:0; overflow:hidden;">
                        <img src="images/equipment/bundles/remote-worker.jpg"
                        alt="Remote Worker Kit"
                        style="width:100%; height:200px; object-fit:contain; background:#FFF4EE; padding:16px;">
                        <div style="padding: 20px;">
                            <p style="color:#A08070; font-size:0.8rem; margin-bottom:6px;">Bundles & Kits</p>
                            <h4 style="color:#2C1810; font-weight:700; margin-bottom:16px;">Remote Worker Kit</h4>
                            <a href="register.php" class="btn-hero-primary" style="display:inline-block; font-size:0.85rem; padding:10px 20px;">
                                Start Renting
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Student Bundle -->
                <div class="col-md-4">
                    <div class="landing-feature-card" style="padding:0; overflow:hidden;">
                        <img src="images/equipment/bundles/study-kit.jpg"
                        alt="Student Bundle"
                        style="width:100%; height:200px; object-fit:contain; background:#FFF4EE; padding:16px;">
                        <div style="padding: 20px;">
                            <p style="color:#A08070; font-size:0.8rem; margin-bottom:6px;">Bundles & Kits</p>
                            <h4 style="color:#2C1810; font-weight:700; margin-bottom:16px;">Student Bundle</h4>
                            <a href="register.php" class="btn-hero-primary" style="display:inline-block; font-size:0.85rem; padding:10px 20px;">
                                Start Renting
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-label">How it works</span>
                <h2 class="section-title">Simple from start to finish.</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="landing-feature-card">
                        <span class="feature-number">01</span>
                        <h4>Browse Equipment</h4>
                        <p>
                            Search across 10 categories including laptops, monitors, 
                            cameras, accessories and more. Filter by condition and 
                            real-time availability.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="landing-feature-card">
                        <span class="feature-number">02</span>
                        <h4>Rent Instantly</h4>
                        <p>
                            Select your equipment, set your due date, and confirm 
                            your rental in seconds. Your dashboard tracks 
                            everything in one place.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="landing-feature-card">
                        <span class="feature-number">03</span>
                        <h4>Return With Ease</h4>
                        <p>
                            Return equipment directly from your dashboard when 
                            you're done. Stock updates automatically so others 
                            can access it right away.
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
                    You're one click away from becoming a DTR member. 
                    Join us by creating your free account today and start browsing 
                    our latest, industry-leading equipment. 
                    Rent what you need, return when you're done.
                </p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn-hero-primary">Create Free Account</a>
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