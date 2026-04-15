<?php
session_start();

$contactMessage = "";
$contactSuccess = false;

if (isset($_POST['contactBtn'])) {
    $contactName  = htmlspecialchars(trim($_POST['contactName']), ENT_QUOTES, 'UTF-8');
    $contactEmail = htmlspecialchars(trim($_POST['contactEmail']), ENT_QUOTES, 'UTF-8');
    $enquiryType  = htmlspecialchars(trim($_POST['enquiryType']), ENT_QUOTES, 'UTF-8');
    $contactMsg   = htmlspecialchars(trim($_POST['contactMsg']), ENT_QUOTES, 'UTF-8');

    if (empty($contactName) || empty($contactEmail) || empty($enquiryType) || empty($contactMsg)) {
        $contactMessage = "Please fill in all fields.";
    } else {
        $contactSuccess = true;
        $contactMessage = "Thank you " . $contactName . "! We'll get back to you within 2 business days.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals — Contact Us</title>
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

    <!-- Contact Form Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">

                    <?php if ($contactSuccess) { ?>

                        <!-- Success Message -->
                        <div class="contact-success-box">
                            <h3 class="contact-success-title">Message Sent!</h3>
                            <p class="contact-success-text">
                                <?php echo $contactMessage; ?>
                            </p>
                            <a href="contact.php" class="btn-hero-primary"
                            style="display:inline-block; margin-top:20px;">
                                Send Another Message
                            </a>
                        </div>

                    <?php } else { ?>

                        <!-- Form Card -->
                        <div class="mission-card">

                            <h2 class="contact-form-title">Leave us a message</h2>
                            <p class="contact-form-subtitle">
                                Our team is here to help and provide quality support. 
                                We'll get back to you within 2 business days.
                            </p>

                            <!-- Error Message -->
                            <?php if (!empty($contactMessage)) { ?>
                                <div class="auth-message" style="margin-top:16px;">
                                    <?php echo $contactMessage; ?>
                                </div>
                            <?php } ?>

                            <form method="POST" action="contact.php" style="margin-top:24px;">

                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="contactName"
                                    placeholder="Enter your full name"
                                    value="<?php echo isset($_POST['contactName']) ? htmlspecialchars($_POST['contactName'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                    required>
                                </div>

                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="contactEmail"
                                    placeholder="Enter your email address"
                                    value="<?php echo isset($_POST['contactEmail']) ? htmlspecialchars($_POST['contactEmail'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                    required>
                                </div>

                                <div class="form-group">
                                    <label>Enquiry Type</label>
                                    <select name="enquiryType" required>
                                        <option value="">Select enquiry type</option>
                                        <option value="General" <?php echo (isset($_POST['enquiryType']) && $_POST['enquiryType'] == 'General') ? 'selected' : ''; ?>>General Enquiry</option>
                                        <option value="Sales" <?php echo (isset($_POST['enquiryType']) && $_POST['enquiryType'] == 'Sales') ? 'selected' : ''; ?>>Sales Enquiry</option>
                                        <option value="Support" <?php echo (isset($_POST['enquiryType']) && $_POST['enquiryType'] == 'Support') ? 'selected' : ''; ?>>Support Request</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea name="contactMsg"
                                    rows="6"
                                    placeholder="Type your message here..."
                                    required><?php echo isset($_POST['contactMsg']) ? htmlspecialchars($_POST['contactMsg'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                                </div>

                                <button type="submit" name="contactBtn" class="login-btn">
                                    Send Message
                                </button>

                            </form>
                        </div>

                    <?php } ?>

                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'includes/theme.php'; ?>

</body>
</html>