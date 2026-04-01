<?php
session_start();
require_once '../config/database.php';

// User Dashboard Session Protection for Users Only
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

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
                <h2>DanalTech</h2>
                <span>Rentals</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link active">
                     My Dashboard
                </a>
                <a href="browse.php" class="nav-link">
                     Browse Equipment
                </a>
                <a href="my-rentals.php" class="nav-link">
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
                <div class="admin-profile">
                     Welcome, <?php echo $_SESSION['userFirstName']; ?>!
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

                    <!-- Search Bar -->
                    <div class="user-search-bar">
                        <input type="text" 
                        id="equipmentSearchInput" 
                        placeholder="Search equipment..."
                        onkeyup="searchEquipment()">
                        <select id="categoryFilter" onchange="searchEquipment()">
                            <option value="">All Categories</option>
                            <option value="Laptops & PCs">Laptops & PCs</option>
                            <option value="Monitors">Monitors</option>
                            <option value="Desks & Chairs">Desks & Chairs</option>
                            <option value="Accessories">Accessories</option>
                            <option value="Bundles & Kits">Bundles & Kits</option>
                            <option value="Printing & Wi-Fi">Printing & Wi-Fi</option>
                            <option value="Cameras & AV">Cameras & AV</option>
                            <option value="Storage & Backup">Storage & Backup</option>
                            <option value="Power & Cables">Power & Cables</option>
                            <option value="Study Essentials">Study Essentials</option>
                        </select>
                    </div>
                </div>

                <table class="admin-table" id="equipmentTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Condition</th>
                            <th>Available</th>
                            <th>Status</th>
                            <th>Deal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($equipmentResult->num_rows > 0) { ?>
                            <?php while ($equipmentRow = $equipmentResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $equipmentRow['name']; ?></td>
                                    <td><?php echo $equipmentRow['category']; ?></td>
                                    <td><?php echo $equipmentRow['equip_condition']; ?></td>
                                    <td><?php echo $equipmentRow['quantity']; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($equipmentRow['availability_status']); ?>">
                                            <?php echo $equipmentRow['availability_status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($equipmentRow['featured_deal'] != 'None') { ?>
                                            <span class="deal-badge">
                                                 <?php echo $equipmentRow['featured_deal']; ?>
                                                <?php if ($equipmentRow['deal_discount'] > 0) { ?>
                                                    - <?php echo $equipmentRow['deal_discount']; ?>% off!
                                                <?php } ?>
                                            </span>
                                        <?php } else { ?>
                                            <span class="text-muted">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button type="button"
                                        class="btn-rent"
                                        style="background: rgba(0, 255, 136, 0.1); color: #00ff88; padding: 5px 12px; border-radius: 5px; border: 1px solid rgba(0, 255, 136, 0.2); font-size: 0.8rem; cursor: pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rentEquipmentModal"
                                        data-id="<?php echo $equipmentRow['id']; ?>"
                                        data-name="<?php echo $equipmentRow['name']; ?>"
                                        data-quantity="<?php echo $equipmentRow['quantity']; ?>">
                                            Rent
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="empty-table">
                                    No equipment available at the moment!
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

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
                                    <td><?php echo $rentalRow['equipmentName']; ?></td>
                                    <td><?php echo $rentalRow['equipmentCategory']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($rentalRow['rent_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($rentalRow['due_date'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($rentalRow['status']); ?>">
                                            <?php echo $rentalRow['status']; ?>
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

            <!-- RENT EQUIPMENT MODAL -->
            <div class="modal fade" id="rentEquipmentModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content dtr-modal">
                        <div class="modal-header dtr-modal-header">
                            <h5 class="modal-title">Rent Equipment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="rent-equipment.php">
                                <input type="hidden" name="rentEquipmentID" id="rentEquipmentID">
                                <div class="form-group">
                                    <label>Equipment</label>
                                    <input type="text" id="rentEquipmentName" 
                                    readonly style="background: #2a2a4a; color: #fff;">
                                </div>
                                <div class="form-group">
                                    <label>Available Quantity</label>
                                    <input type="text" id="rentEquipmentQuantity" 
                                    readonly style="background: #2a2a4a; color: #fff;">
                                </div>
                                <div class="form-group">
                                    <label>Quantity To Rent</label>
                                    <input type="number" name="rentQuantity"
                                    placeholder="Enter quantity" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="date" name="rentDueDate" required>
                                </div>
                                <div class="modal-footer dtr-modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="rentBtn" class="btn btn-primary">Confirm Rental</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Populate rent modal
        var rentModal = document.getElementById('rentEquipmentModal');
        rentModal.addEventListener('show.bs.modal', function(event) {
            var rentBtn           = event.relatedTarget;
            var equipmentID       = rentBtn.getAttribute('data-id');
            var equipmentName     = rentBtn.getAttribute('data-name');
            var equipmentQuantity = rentBtn.getAttribute('data-quantity');

            document.getElementById('rentEquipmentID').value       = equipmentID;
            document.getElementById('rentEquipmentName').value     = equipmentName;
            document.getElementById('rentEquipmentQuantity').value = equipmentQuantity;
        });

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

</body>
</html>