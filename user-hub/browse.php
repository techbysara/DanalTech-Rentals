<?php
session_start();
require_once '../config/database.php';

// Browse Equipment Session Protection for Users Only
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

// Retrieve all available equipment
$browseEquipmentQuery  = "SELECT * FROM equipment WHERE availability_status != 'Unavailable' 
                          AND quantity > 0 ORDER BY created_at DESC";
$browseEquipmentResult = $dbConn->query($browseEquipmentQuery);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - Browse Equipment</title>

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
                <a href="dashboard.php" class="nav-link">
                    My Dashboard
                </a>
                <a href="browse.php" class="nav-link active">
                    Browse Equipment
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
                <h1>Browse Equipment</h1>
                <div class="admin-profile">
                    Welcome, <?php echo $_SESSION['userFirstName']; ?>!
                </div>
            </div>

            <!-- Equipment Section -->
            <div class="admin-table-card">
                <div class="user-section-header">
                    <h2>Available Equipment</h2>
                    <!-- Search Bar -->
                    <div class="user-search-bar">
                        <input type="text"
                        id="browseSearchInput"
                        placeholder="Search equipment..."
                        onkeyup="browseSearch()">
                        <select id="browseCategoryFilter" onchange="browseSearch()">
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
                        <select id="browseConditionFilter" onchange="browseSearch()">
                            <option value="">All Conditions</option>
                            <option value="Excellent">Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                </div>

                <table class="admin-table" id="browseTable">
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
                        <?php if ($browseEquipmentResult->num_rows > 0) { ?>
                            <?php while ($browseRow = $browseEquipmentResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $browseRow['name']; ?></td>
                                    <td><?php echo $browseRow['category']; ?></td>
                                    <td><?php echo $browseRow['equip_condition']; ?></td>
                                    <td><?php echo $browseRow['quantity']; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($browseRow['availability_status']); ?>">
                                            <?php echo $browseRow['availability_status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($browseRow['featured_deal'] != 'None') { ?>
                                            <span class="deal-badge">
                                                <?php echo $browseRow['featured_deal']; ?>
                                                <?php if ($browseRow['deal_discount'] > 0) { ?>
                                                    - <?php echo $browseRow['deal_discount']; ?>% off!
                                                <?php } ?>
                                            </span>
                                        <?php } else { ?>
                                            <span style="color: #4a4a6a;">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <button type="button"
                                        class="btn-rent"
                                        style="background: rgba(0, 255, 136, 0.1); color: #00ff88; padding: 5px 12px; border-radius: 5px; 
                                                            border: 1px solid rgba(0, 255, 136, 0.2); font-size: 0.8rem; cursor: pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rentEquipmentModal"
                                        data-id="<?php echo $browseRow['id']; ?>"
                                        data-name="<?php echo $browseRow['name']; ?>"
                                        data-quantity="<?php echo $browseRow['quantity']; ?>">
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

        // Browse search and filter
        function browseSearch() {
            var searchInput     = document.getElementById('browseSearchInput').value.toLowerCase();
            var categoryFilter  = document.getElementById('browseCategoryFilter').value.toLowerCase();
            var conditionFilter = document.getElementById('browseConditionFilter').value.toLowerCase();
            var browseTable     = document.getElementById('browseTable');
            var tableRows       = browseTable.getElementsByTagName('tr');

            for (var rowIndex = 1; rowIndex < tableRows.length; rowIndex++) {
                var tableRow           = tableRows[rowIndex];
                var equipmentName      = tableRow.cells[0].textContent.toLowerCase();
                var equipmentCategory  = tableRow.cells[1].textContent.toLowerCase();
                var equipmentCondition = tableRow.cells[2].textContent.toLowerCase();

                var matchesSearch    = equipmentName.includes(searchInput);
                var matchesCategory  = categoryFilter === '' || equipmentCategory.includes(categoryFilter);
                var matchesCondition = conditionFilter === '' || equipmentCondition.includes(conditionFilter);

                if (matchesSearch && matchesCategory && matchesCondition) {
                    tableRow.style.display = '';
                } else {
                    tableRow.style.display = 'none';
                }
            }
        }
    </script>

</body>
</html>