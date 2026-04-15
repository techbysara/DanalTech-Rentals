<?php session_start(); 

// Generate CSRF token if not already set
if (empty($_SESSION['csrfToken'])) {
    $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
}
// Error Message config
$registerMessage = "";

if (isset($_GET['error'])) {
    if($_GET['error'] == 'emptyfields') {
        $registerMessage = "Please fill in all fields.";
    
    } elseif ($_GET['error'] == 'passwordmatch') {
        $registerMessage = " Passwords do not match. Please try again.";
    
    } elseif ($_GET['error'] == 'emailexists') {
        $registerMessage = " User already exist. Please login.";
    
    } elseif ($_GET['error'] == 'registerfield') {
        $registerMessage = " Registration failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Theme Toggle -->
    <div style="position: fixed; top: 16px; right: 16px; z-index: 1000;">
        <button class="theme-toggle" id="themeToggleBtn" onclick="toggleTheme()">Dark</button>
    </div>

    <div class="login-wrapper">
        <!-- Brand Header -->
        <div class="brand-header">
            <h1>DanalTech Rentals</h1>
            <p class="brand-tagline">Rent smart. Work better. Zero stress.</p>
        </div>

        <!-- Register Box -->
        <div class="login-box">
            <h3>Create Account</h3>

            <!-- Registration Message -->
             <?php if (!empty($registerMessage)) { ?>
                  <p class="auth-message"><?php echo $registerMessage; ?></p>
            <?php } ?>

            <form method="POST" action="includes/auth.php">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="firstName" 
                    placeholder="Enter your first name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lastName" 
                    placeholder="Enter your last name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="userEmail" 
                    placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="userPassword" 
                    placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirmPassword" 
                    placeholder="Confirm your password" required>
                </div>

                <!-- CSRF Token -->
                <input type="hidden" name="csrfToken" 
                value="<?php echo htmlspecialchars($_SESSION['csrfToken'], ENT_QUOTES, 'UTF-8'); ?>">

                <button type="submit" 
                name="registerBtn" 
                class="login-btn">Create Account</button>
            </form>
            <!-- Link to Login -->
            <p class="auth-switch">Already have an account? 
                <a href="login.php">Login here</a>
            </p>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <p>&copy; 2026 DanalTech Rentals. All rights reserved.</p>
        </div>
    </div>
    <?php include 'includes/theme.php'; ?>
</body>
</html>