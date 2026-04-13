<?php 
session_start(); 

// Errot and Success Message config
$loginMessage = "";

if (isset($_GET['error'])) {
    if($_GET['error'] == 'emptyfields') {
        $loginMessage = " Please enter your email and password. ";
    
    } elseif ($_GET['error'] == 'wrongpassword') {
        $loginMessage = " Incorrect password. Please verify and try again.";
    
    } elseif ($_GET['error'] == 'usernotfound') {
        $loginMessage = " User does not exist.";
    
    } elseif ($_GET['error'] == 'accountlocked') {
    $loginMessage = "Account locked due to too many failed attempts. Try again in 15 minutes.";

    }

}

if (isset($_GET['success'])) {
    if  ($_GET['success'] == 'registered') {
        $loginMessage = " Account successfully created! Please login.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <!-- Brand Header -->
        <div class="brand-header">
            <h1>DanalTech Rentals</h1>
            <p class="brand-tagline">Rent the tech you need. Work without limits.</p>
        </div>

        <!-- Login Box -->
        <div class="login-box">
            <h3>Welcome Back</h3>

        <!-- Login Message -->
            <?php if (!empty ($loginMessage)) { ?>
                <p class="auth-message"><?php echo $loginMessage; ?></p>
            <?php } ?>

            <form method="POST" action="includes/auth.php">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="userEmail" 
                    placeholder="Enter your email" required> 
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="userPassword" 
                    placeholder="Enter your password" required>
                </div>
                <div class="form-group">
                    <label>Login As</label>
                    <select name="userRole">
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <button type="submit" 
                name="loginBtn" 
                class="login-btn">Login</button>
                <!-- end of login-box -->

                <!-- Link to Register -->
                <p class="auth-switch">Don't have an account? 
                    <a href="register.php">Register here</a>
                </p>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <p>&copy; 2026 DanalTech Rentals. All rights reserved.</p>
        </div>
    </div>
</body>
</html>