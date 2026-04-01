<?php
session_start();
require_once '../config/database.php';

// My Rentals History Session Protection for Users Only
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

// Retrieve all user rentals including returned
$myRentalsQuery  = "SELECT rentals.*, equipment.name AS equipmentName,
                   equipment.category AS equipmentCategory FROM rentals
                   JOIN equipment ON rentals.equipment_id = equipment.id
                   WHERE rentals.user_id = ? ORDER BY rentals.rent_date DESC";

$myRentalsPrepared = $dbConn->prepare($myRentalsQuery);
$myRentalsPrepared->bind_param("i", $_SESSION['userID']);
$myRentalsPrepared->execute();
$myRentalsResult = $myRentalsPrepared->get_result();

// Retrieve rental stats
$myTotalRentalsQuery    = "SELECT COUNT(*) AS myTotalRentals 
                           FROM rentals WHERE user_id = ?";
                           
$myTotalRentalsPrepared = $dbConn->prepare($myTotalRentalsQuery);
$myTotalRentalsPrepared->bind_param("i", $_SESSION['userID']);
$myTotalRentalsPrepared->execute();
$myTotalRentalsResult   = $myTotalRentalsPrepared->get_result();
$myTotalRentalsData     = $myTotalRentalsResult->fetch_assoc();

$myActiveRentalsQuery    = "SELECT COUNT(*) AS myActiveRentals 
                            FROM rentals WHERE user_id = ? AND status = 'Active'";

$myActiveRentalsPrepared = $dbConn->prepare($myActiveRentalsQuery);
$myActiveRentalsPrepared->bind_param("i", $_SESSION['userID']);
$myActiveRentalsPrepared->execute();
$myActiveRentalsResult   = $myActiveRentalsPrepared->get_result();
$myActiveRentalsData     = $myActiveRentalsResult->fetch_assoc();

$myReturnedRentalsQuery    = "SELECT COUNT(*) AS myReturnedRentals 
                              FROM rentals WHERE user_id = ? AND status = 'Returned'";

$myReturnedRentalsPrepared = $dbConn->prepare($myReturnedRentalsQuery);
$myReturnedRentalsPrepared->bind_param("i", $_SESSION['userID']);
$myReturnedRentalsPrepared->execute();
$myReturnedRentalsResult   = $myReturnedRentalsPrepared->get_result();
$myReturnedRentalsData     = $myReturnedRentalsResult->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - My Rentals</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
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
                <a href="dashboard.php" class="nav-link">
                    My Dashboard
                </a>
                <a href="browse.php" class="nav-link">
                    Browse Equipment
                </a>
                <a href="myRentals.php" class="nav-link active">
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
                <h1>My Rentals</h1>
                <div class="admin-profile">
                    Welcome, <?php echo $_SESSION['userFirstName']; ?>!
                </div>
            </div>

            <!-- Rental Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $myTotalRentalsData['myTotalRentals']; ?></h3>
                        <p>Total Rentals</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $myActiveRentalsData['myActiveRentals']; ?></h3>
                        <p>Active Rentals</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $myReturnedRentalsData['myReturnedRentals']; ?></h3>
                        <p>Returned Rentals</p>
                    </div>
                </div>
            </div>

            <!-- My Rentals Table -->
            <div class="admin-table-card">
                <h2>My Rental History</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Equipment</th>
                            <th>Category</th>
                            <th>Rented On</th>
                            <th>Due Date</th>
                            <th>Returned On</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($myRentalsResult->num_rows > 0) { ?>
                            <?php while ($myRentalRow = $myRentalsResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $myRentalRow['equipmentName']; ?></td>
                                    <td><?php echo $myRentalRow['equipmentCategory']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($myRentalRow['rent_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($myRentalRow['due_date'])); ?></td>
                                    <td>
                                        <?php echo $myRentalRow['return_date'] 
                                        ? date('d M Y', strtotime($myRentalRow['return_date'])) 
                                        : '-'; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($myRentalRow['status']); ?>">
                                            <?php echo $myRentalRow['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($myRentalRow['status'] === 'Active') { ?>
                                            <a href="return-equipment.php?rental=<?php echo $myRentalRow['id']; ?>"
                                            class="btn-return"
                                            onclick="return confirm('Are you sure you want to return this equipment?')">
                                                Return
                                            </a>
                                        <?php } else { ?>
                                            <span style="color: #4a4a6a; font-size: 0.8rem;">
                                                <?php echo $myRentalRow['status']; ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="empty-table">
                                    You have no rental history yet!
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

</body>
</html>