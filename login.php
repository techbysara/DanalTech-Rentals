<?php session_start(); ?>
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
            <p class="brand-tagline">Stop stressing about gear. We've got you covered.</p>
        </div>

        <!-- Login Box -->
        <div class="login-box">
            <h3>Welcome Back</h3>
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
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <p>&copy; 2026 DanalTech Rentals. All rights reserved.</p>
        </div>
    </div>
</body>
</html>