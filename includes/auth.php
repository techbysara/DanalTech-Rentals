<?php
session_start();

  // Connection to Datatbase
require_once '../config/database.php';

  // DanalTech Rentals - Authentication Setup

    // Handle Login
if(isset($_POST['loginBtn'])) {
    $userEmail = trim($_POST['userEmail']);
    $userPassword = trim($_POST['userPassword']);
    $userRole = trim($_POST['userRole']);

    // Validate
    if(empty($userEmail) || empty ($userPassword)) {
        header("Location: ../login.php?error=emptyfields");
        exit();
    }

    // Check the user exists in  the database 
    $loginQuery = "SELECT * FROM users WHERE email = ? AND role = ?";
    $loginPrepared = $dbConn->prepare($loginQuery);
    $loginPrepared->bind_param("ss", $userEmail, $userRole);
    $loginPrepared->execute();
    $loginQueryResult = $loginPrepared->get_result();

    if($loginQueryResult->num_rows ===1) {
        $userData = $loginQueryResult->fetch_assoc();

        // Lockout Check
        if ($userData['locked_until'] && new DateTime() < new DateTime($userData['locked_until'])) {
            header("Location: ../login.php?error=accountlocked");
            exit();
        }

        // Password Verification
        if(password_verify($userPassword, $userData['password'])) {

            // Reset failed attempts on successful login
            $resetAttemptsQuery    = "UPDATE users SET failed_attempts = 0, 
                                     locked_until = NULL WHERE id = ?";
            $resetAttemptsPrepared = $dbConn->prepare($resetAttemptsQuery);
            $resetAttemptsPrepared->bind_param("i", $userData['id']);
            $resetAttemptsPrepared->execute();

            // Set Session Variable
            $_SESSION['userID']             =$userData['id'];
            $_SESSION['userFirstName']      =$userData['firstName'];
            $_SESSION['userLastName']       =$userData['lastName'];
            $_SESSION['userEmail']          =$userData['email'];
            $_SESSION['userRole']           =$userData['role'];

            // Last login timestamp update
            $lastLoginQuery = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $lastLoginUpdate = $dbConn->prepare($lastLoginQuery);
            $lastLoginUpdate->bind_param("i", $userData['id']);
            $lastLoginUpdate->execute();

            // Redirect based on user role
            if($userData['role'] === 'Admin') {
                header("Location: ../danaltech-admin/dashboard.php");
                exit();

            } else {
                header("Location: ../user-hub/dashboard.php");
                exit();
            }

        } else {
            // Wrong password — increment failed attempts
             $newAttempts = $userData['failed_attempts'] + 1;

            if ($newAttempts >= 5) {
                $lockQuery    = "UPDATE users SET failed_attempts = ?, 
                                locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) 
                                WHERE id = ?";
                $lockPrepared = $dbConn->prepare($lockQuery);
                $lockPrepared->bind_param("ii", $newAttempts, $userData['id']);
                $lockPrepared->execute();
                header("Location: ../login.php?error=accountlocked");
                exit();

            } else {
                $incrementQuery    = "UPDATE users SET failed_attempts = ? WHERE id = ?";
                $incrementPrepared = $dbConn->prepare($incrementQuery);
                $incrementPrepared->bind_param("ii", $newAttempts, $userData['id']);
                $incrementPrepared->execute();   
                header("Location: ../login.php?error=wrongpassword");
                exit();
            }

        } 

    } else {
        // User not found
        header("Location: ../login.php?error=usernotfound");
        exit();
    }
}

// Handle Registration
if(isset($_POST['registerBtn'])) {
    $firstName          =trim($_POST['firstName']);
    $lastName           =trim($_POST['lastName']);
    $userEmail           =trim($_POST['userEmail']);
    $userPassword        =trim($_POST['userPassword']);
    $confirmPassword      =trim($_POST['confirmPassword']);

    // Validate
    if (empty($firstName) || empty($lastName) || 
        empty($userEmail) || empty($userPassword)) {
        header("Location: ../register.php?error=emptyfields");
        exit();
    }

    // Check password match
    if ($userPassword !== $confirmPassword) {
        header("Location: ../register.php?error=passwordmatch");
        exit();
    }

    // Verify if email is already registered
    $emailCheckQuery    = "SELECT id FROM users WHERE email = ?";
    $emailCheckPrepared = $dbConn->prepare($emailCheckQuery);
    $emailCheckPrepared->bind_param("s", $userEmail);
    $emailCheckPrepared->execute();
    $emailCheckResult   = $emailCheckPrepared->get_result();

    if ($emailCheckResult->num_rows > 0) {
        header("Location: ../register.php?error=emailexists");
        exit();
    }

    // Securely Hash password
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    // Insert new user into database
    $registerQuery      = "INSERT INTO users (firstName, lastName, email, password, role)
                            VALUES (?, ?, ?, ?, 'User')";
    $registerPrepared   = $dbConn->prepare($registerQuery);
    $registerPrepared->bind_param(
        "ssss",
        $firstName,
        $lastName,
        $userEmail,
        $hashedPassword
    );

    if ($registerPrepared->execute()) {
        header("Location: ../login.php?success=registered");
        exit();
    
        } else {
        header("Location: ../register.php?error=registerfailed");
        exit();
    }
}

?>