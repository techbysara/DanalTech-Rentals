<?php
session_start();

// User Dashboard Session Protection for Users Only
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

// Auto-update overdue rentals
$autoOverdueQuery    = "UPDATE rentals SET status = 'Overdue' 
                        WHERE user_id = ? AND status = 'Active' 
                        AND due_date < NOW()";

$autoOverduePrepared = $dbConn->prepare($autoOverdueQuery);
$autoOverduePrepared->bind_param("i", $_SESSION['userID']);
$autoOverduePrepared->execute();

// Retrieve available equipment
$equipmentQuery  = "SELECT * FROM equipment WHERE availability_status != 'Unavailable' AND quantity > 0 
                    ORDER BY created_at DESC";

$equipmentResult = $dbConn->query($equipmentQuery);

// Retrieve User's active rentals
$userRentalsQuery  = "SELECT rentals.*, equipment.name AS equipmentName, equipment.category AS equipmentCategory FROM rentals 
                      JOIN equipment ON rentals.equipment_id = equipment.id
                      WHERE rentals.user_id = ? AND rentals.status = 'Active' ORDER BY rentals.rent_date DESC";
$userRentalsPrepared = $dbConn->prepare($userRentalsQuery);
$userRentalsPrepared->bind_param("i", $_SESSION['userID']);
$userRentalsPrepared->execute();
$userRentalsResult = $userRentalsPrepared->get_result();

// Retrieve User's rental stats
$totalRentalsQuery  = "SELECT COUNT(*) AS totalRentals FROM rentals WHERE user_id = ?";

$totalRentalsPrepared = $dbConn->prepare($totalRentalsQuery);
$totalRentalsPrepared->bind_param("i", $_SESSION['userID']);
$totalRentalsPrepared->execute();
$totalRentalsResult = $totalRentalsPrepared->get_result();
$totalRentalsData   = $totalRentalsResult->fetch_assoc();

$activeRentalsQuery  = "SELECT COUNT(*) AS activeRentals FROM rentals WHERE user_id = ? 
                        AND status = 'Active'";

$activeRentalsPrepared = $dbConn->prepare($activeRentalsQuery);
$activeRentalsPrepared->bind_param("i", $_SESSION['userID']);
$activeRentalsPrepared->execute();
$activeRentalsResult = $activeRentalsPrepared->get_result();
$activeRentalsData   = $activeRentalsResult->fetch_assoc();

$overdueRentalsQuery  = "SELECT COUNT(*) AS overdueRentals FROM rentals WHERE user_id = ? 
                         AND status = 'Overdue'";

$overdueRentalsPrepared = $dbConn->prepare($overdueRentalsQuery);
$overdueRentalsPrepared->bind_param("i", $_SESSION['userID']);
$overdueRentalsPrepared->execute();
$overdueRentalsResult = $overdueRentalsPrepared->get_result();
$overdueRentalsData   = $overdueRentalsResult->fetch_assoc();

// Messages session
$userDashMessage     = "";
$userDashMessageType = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'rented') {
        $userDashMessage     = "Equipment rented successfully!";
        $userDashMessageType = "success";

    } elseif ($_GET['success'] == 'returned') {
        $userDashMessage     = "Equipment returned successfully!";
        $userDashMessageType = "success";
    }
}
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'rentfailed') {
        $userDashMessage     = "Failed to rent equipment. Please try again.";
        $userDashMessageType = "error";

    } elseif ($_GET['error'] == 'unavailable') {
        $userDashMessage     = "Sorry, this equipment is currently unvailable.";
        $userDashMessageType = "error";

    } elseif ($_GET['error'] == 'rentalLimit') {
        $userDashMessage      = "You have reached the maximum rental Limit of 7 items!";
        $userDashMessageType  = "error"; 
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - My Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!--  Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-wrapper">

        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-brand">
                <a href="../index.php" class="dtr-logo" style="justify-content:center; padding: 0 0 10px 0;">
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
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link active">
                     My Dashboard
                </a>
                <a href="browse.php" class="nav-link">
                     Browse Equipment
                </a>
                <a href="cart.php" class="nav-link">
                    My Cart
                </a>
                <a href="myRentals.php" class="nav-link">
                     My Rentals
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
                <h1>My Dashboard</h1>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button class="theme-toggle" id="themeToggleBtn" onclick="toggleTheme()">Dark</button>
                    <div class="admin-profile">
                        Welcome, <?php echo htmlspecialchars($_SESSION['userFirstName'], ENT_QUOTES, 'UTF-8'); ?>!
                    </div>
                </div>
            </div>

            <!-- User Message -->
            <?php if (!empty($userDashMessage)) { ?>
                <div class="alert alert-<?php echo ($userDashMessageType == 'error') ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $userDashMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    
                    <div class="stat-info">
                        <h3><?php echo $totalRentalsData['totalRentals']; ?></h3>
                        <p>Total Rentals</p>
                    </div>
                </div>
                <div class="stat-card">
                    
                    <div class="stat-info">
                        <h3><?php echo $activeRentalsData['activeRentals']; ?></h3>
                        <p>Active Rentals</p>
                    </div>
                </div>
                <div class="stat-card">
                    
                    <div class="stat-info">
                        <h3><?php echo $overdueRentalsData['overdueRentals']; ?></h3>
                        <p>Overdue Rentals</p>
                    </div>
                </div>
            </div>

            <!-- Available Equipment Section -->
            <div class="admin-table-card">
                <div class="user-section-header">
                    <h2>Available Equipment</h2>
                </div>
                <div style="text-align:center; padding: 40px 20px;">
                    <p style="color: #A08070; margin-bottom: 20px; font-size: 1rem;">
                        Browse our full catalogue of available equipment, 
                        filter by category and condition, and add items to your cart.
                    </p>
                    <a href="browse.php" class="btn-hero-primary" style="display:inline-block;">
                        Browse All Equipment
                    </a>
                </div>
            </div>

            <!-- Overdue Warning Banner -->
            <?php if ($overdueRentalsData['overdueRentals'] > 0) { ?>
                <div class="overdue-warning">
                        You have <?php echo $overdueRentalsData['overdueRentals']; ?> overdue rental(s)! 
                        Additional charges may apply. Please return your equipment immediately.
                </div>
                
            <?php } ?>

            <!-- My Active Rentals Section -->
            <div class="admin-table-card">
                <h2>My Active Rentals</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Equipment</th>
                            <th>Category</th>
                            <th>Rented On</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($userRentalsResult->num_rows > 0) { ?>
                            <?php while ($rentalRow = $userRentalsResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rentalRow['equipmentName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($rentalRow['equipmentCategory'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo date('d M Y', strtotime($rentalRow['rent_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($rentalRow['due_date'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo htmlspecialchars(strtolower($rentalRow['status']), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($rentalRow['status'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="return-equipment.php?rental=<?php echo $rentalRow['id']; ?>"
                                        class="btn-return"
                                        onclick="return confirm('Are you sure you want to return this equipment?')">
                                            Return
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="empty-table">
                                    You have no active rentals!
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>



        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   
    <script>
        // Search and filter equipment
        function searchEquipment() {
            var searchInput    = document.getElementById('equipmentSearchInput').value.toLowerCase();
            var categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            var equipmentTable = document.getElementById('equipmentTable');
            var tableRows      = equipmentTable.getElementsByTagName('tr');

            for (var rowIndex = 1; rowIndex < tableRows.length; rowIndex++) {
                var tableRow      = tableRows[rowIndex];
                var equipmentName = tableRow.cells[0].textContent.toLowerCase();
                var equipmentCat  = tableRow.cells[1].textContent.toLowerCase();

                var matchesSearch   = equipmentName.includes(searchInput);
                var matchesCategory = categoryFilter === '' || equipmentCat.includes(categoryFilter);

                if (matchesSearch && matchesCategory) {
                    tableRow.style.display = '';
                } else {
                    tableRow.style.display = 'none';
                }
            }
        }
    </script>
    <?php include '../includes/theme.php'; ?>

</body>
</html>