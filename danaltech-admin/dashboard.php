<?php
session_start();
require_once '../config/database.php';

// Session Protection for Admins Only

if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch Equipment Satts
$totalUsersQuery         = "SELECT COUNT(*) AS totalUsers FROM users WHERE role = 'User'";
$totalUsersResult        = $dbConn->query($totalUsersQuery);
$totalUsersData          = $totalUsersResult->fetch_assoc();

$totalEquipmentQuery         = "SELECT COUNT(*) AS totalEquipment FROM equipment";
$totalEquipmentResult        = $dbConn->query($totalEquipmentQuery);
$totalEquipmentData          = $totalEquipmentResult->fetch_assoc();

$activeRentalsQuery         = "SELECT COUNT(*) AS activeRentals FROM rentals WHERE status = 'Active'";
$activeRentalsResult        = $dbConn->query($activeRentalsQuery);
$activeRentalsData          = $activeRentalsResult->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - Admin Dashboard</title>
    <link rel ="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-wrapper">

        <!-- Sidebar -->
         <div class="admin-sidebar">
            <div class="sidebar-brand">
                <h2>DanalTech</h2>
                <span>Rentals</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link active">
                    Dashboard
                </a>
                <a href="equipment.php" class="nav-link">
                    Equipment
                </a>
                <a href="users.php" class="nav-link">
                    Users
                </a>
                <a href="rentals.php" class="nav-link">
                    Rentals
                </a>
                <a href="../logout.php" class="nav-link logout">
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-content">

            <!-- Top Bar -->
             <div class="admin-topbar">
                <h1>Admin Dashboard</h1>
                <div class="admin-profile">
                    Welcome, <?php echo htmlspecialchars($_SESSION['userFirstName'], ENT_QUOTES, 'UTF-8'); ?>! 
                </div>

            </div>

            <!-- Stats Cards -->
             <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?php echo htmlspecialchars($totalUsersData['totalUsers'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p>Total Users</p>
                    </div>

                </div>

                <div class="stat-card">
                    <div class="stat-icon">🔧</div>
                    <div class="stat-info">
                        <h3><?php echo htmlspecialchars($totalEquipmentData['totalEquipment'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p>Total Equipment</p>
                    </div>

                </div>
                 <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <h3><?php echo htmlspecialchars($activeRentalsData['activeRentals'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p>Active Rentals</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <a href="equipment.php?action=add" class="action-card">
                        ➕ Add Equipment
                    </a>
                    <a href="users.php?action=add" class="action-card">
                        ➕ Add User
                    </a>
                    <a href="rentals.php" class="action-card">
                        📋 View Rentals
                    </a>
                </div>
            </div>

        </div>
    </div>
 
</body>
</html>
