<?php
session_start();
require_once '../config/database.php';

// Admin Rentals Management Session Protection - Admins Only

if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Mark As Overdue Session
if (isset($_GET['overdue'])) {
    $overdueRentalID       = intval($_GET['overdue']);
    $overdueRentalQuery    = "UPDATE rentals SET status = 'Overdue' WHERE id = ?";
    $overdueRentalPrepared = $dbConn->prepare($overdueRentalQuery);
    $overdueRentalPrepared->bind_param("i", $overdueRentalID);
    $overdueRentalPrepared->execute();
    header("Location: rentals.php?success=markedoverdue");
    exit();
}

// Retrieve all rentals with user and equipment details
$allRentalsQuery  = "SELECT rentals.*,
                    users.firstName AS userFirstName,
                    users.lastName AS userLastName,
                    users.email AS userEmail,
                    equipment.name AS equipmentName,
                    equipment.category AS equipmentCategory
                    FROM rentals JOIN users ON rentals.user_id = users.id
                    JOIN equipment ON rentals.equipment_id = equipment.id
                    ORDER BY rentals.rent_date DESC";

$allRentalsResult = $dbConn->query($allRentalsQuery);

// Retrieve rental stats
$totalRentalsQuery    = "SELECT COUNT(*) AS totalRentals FROM rentals";
$totalRentalsResult   = $dbConn->query($totalRentalsQuery);
$totalRentalsData     = $totalRentalsResult->fetch_assoc();

$activeRentalsQuery   = "SELECT COUNT(*) AS activeRentals FROM rentals WHERE status = 'Active'";
$activeRentalsResult  = $dbConn->query($activeRentalsQuery);
$activeRentalsData    = $activeRentalsResult->fetch_assoc();

$overdueRentalsQuery  = "SELECT COUNT(*) AS overdueRentals FROM rentals WHERE status = 'Overdue'";
$overdueRentalsResult = $dbConn->query($overdueRentalsQuery);
$overdueRentalsData   = $overdueRentalsResult->fetch_assoc();

$returnedRentalsQuery  = "SELECT COUNT(*) AS returnedRentals FROM rentals WHERE status = 'Returned'";
$returnedRentalsResult = $dbConn->query($returnedRentalsQuery);
$returnedRentalsData   = $returnedRentalsResult->fetch_assoc();

// Messages
$rentalsMessage     = "";
$rentalsMessageType = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'markedoverdue') {
        $rentalsMessage     = "Rental marked as overdue!";
        $rentalsMessageType = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - Rentals Management</title>

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
                <a href="dashboard.php" class="nav-link">
                    Dashboard
                </a>
                <a href="equipment.php" class="nav-link">
                    Equipment
                </a>
                <a href="users.php" class="nav-link">
                    Users
                </a>
                <a href="rentals.php" class="nav-link active">
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
                <h1>Rentals Management</h1>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button class="theme-toggle" id="themeToggleBtn" onclick="toggleTheme()">Dark</button>
                    <div class="admin-profile">
                        Welcome, <?php echo htmlspecialchars($_SESSION['userFirstName'], ENT_QUOTES, 'UTF-8'); ?>!
                    </div>
                </div>
            </div>

            <!-- Rentals Message -->
            <?php if (!empty($rentalsMessage)) { ?>
                <div class="alert alert-<?php echo ($rentalsMessageType == 'error') ? 'danger' : 'success'; ?> 
                            alert-dismissible fade show" role="alert">
                    <?php echo $rentalsMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <!-- Rental Stats -->
            <div class="stats-grid-4">
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
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $returnedRentalsData['returnedRentals']; ?></h3>
                        <p>Returned Rentals</p>
                    </div>
                </div>
            </div>

            <!-- All Rentals Table -->
            <div class="admin-table-card">
                <h2>All Rentals</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
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
                        <?php if ($allRentalsResult->num_rows > 0) { ?>
                            <?php while ($rentalRow = $allRentalsResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rentalRow['userFirstName'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($rentalRow['userLastName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($rentalRow['userEmail'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($rentalRow['equipmentName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($rentalRow['equipmentCategory'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo date('d M Y', strtotime($rentalRow['rent_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($rentalRow['due_date'])); ?></td>
                                    <td>
                                        <?php echo $rentalRow['return_date'] 
                                        ? date('d M Y', strtotime($rentalRow['return_date'])) 
                                        : '-'; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo htmlspecialchars(strtolower($rentalRow['status']), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($rentalRow['status'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($rentalRow['status'] === 'Active') { ?>
                                            <a href="rentals.php?overdue=<?php echo $rentalRow['id']; ?>"
                                            class="btn-delete"
                                            onclick="return confirm('Mark this rental as overdue?')">
                                                Mark Overdue
                                            </a>
                                        <?php } else { ?>
                                            <span class="text-muted" style="font-size: 0.8rem;">
                                                <?php echo htmlspecialchars($rentalRow['status'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="9" class="empty-table">
                                    No rentals found!
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

    <?php include '../includes/theme.php'; ?>

</body>
</html>