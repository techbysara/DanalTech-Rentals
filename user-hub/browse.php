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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <a href="dashboard.php" class="nav-link">My Dashboard</a>
                <a href="browse.php" class="nav-link active">Browse Equipment</a>
                <a href="cart.php" class="nav-link">My Cart</a>
                <a href="myRentals.php" class="nav-link">My Rentals</a>
                <a href="../logout.php" class="nav-link logout">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-content">

            <!-- Top Bar -->
            <div class="admin-topbar">
                <h1>Browse Equipment</h1>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button class="theme-toggle" id="themeToggleBtn" onclick="toggleTheme()">Dark</button>
                    <div class="admin-profile">
                        Welcome, <?php echo htmlspecialchars($_SESSION['userFirstName'], ENT_QUOTES, 'UTF-8'); ?>!
                    </div>
                </div>
            </div>

            <!-- Equipment Section -->
            <div class="admin-table-card">

                <!-- Search Bar -->
                <div class="user-section-header">
                    <h2>Available Equipment</h2>
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

                <!-- Equipment Cards Grid -->
                <div class="equipment-grid" id="browseTable">
                    <?php if ($browseEquipmentResult->num_rows > 0) { ?>
                        <?php while ($browseRow = $browseEquipmentResult->fetch_assoc()) { ?>
                            <div class="equipment-card"
                                data-name="<?php echo strtolower(htmlspecialchars($browseRow['name'], ENT_QUOTES, 'UTF-8')); ?>"
                                data-category="<?php echo strtolower(htmlspecialchars($browseRow['category'], ENT_QUOTES, 'UTF-8')); ?>"
                                data-condition="<?php echo strtolower(htmlspecialchars($browseRow['equip_condition'], ENT_QUOTES, 'UTF-8')); ?>">

                                <!-- Equipment Image -->
                                <img src="../images/equipment/<?php echo htmlspecialchars($browseRow['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="<?php echo htmlspecialchars($browseRow['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                    class="equipment-card-img"
                                    onerror="this.src='../images/equipment/default.jpg'">

                                <!-- Card Body -->
                                <div class="equipment-card-body">
                                    <p class="equipment-card-category">
                                        <?php echo htmlspecialchars($browseRow['category'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <h4 class="equipment-card-name">
                                        <?php echo htmlspecialchars($browseRow['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h4>
                                    <div class="equipment-card-meta">
                                        <span class="equipment-card-price">
                                            £<?php echo number_format($browseRow['price'], 2); ?>/day
                                        </span>
                                        <span class="status-badge <?php echo htmlspecialchars(strtolower($browseRow['availability_status']), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($browseRow['availability_status'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>
                                    <div class="equipment-card-footer">
                                        <small style="color: #A08070; font-size:0.8rem;">
                                            <?php echo htmlspecialchars($browseRow['equip_condition'], ENT_QUOTES, 'UTF-8'); ?> condition
                                            &nbsp;|&nbsp; <?php echo $browseRow['quantity']; ?> available
                                        </small>
                                        <button type="button"
                                            class="btn-rent"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rentEquipmentModal"
                                            data-id="<?php echo $browseRow['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($browseRow['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-quantity="<?php echo $browseRow['quantity']; ?>"
                                            data-price="<?php echo htmlspecialchars($browseRow['price'], ENT_QUOTES, 'UTF-8'); ?>">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>

                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="empty-table" style="padding:40px; text-align:center; grid-column: 1/-1;">
                            No equipment available at the moment!
                        </div>
                    <?php } ?>
                </div>
                <!-- End Equipment Cards Grid -->

            </div>
            <!-- End Equipment Section -->

            <!-- RENT EQUIPMENT MODAL -->
            <div class="modal fade" id="rentEquipmentModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content dtr-modal">
                        <div class="modal-header dtr-modal-header">
                            <h5 class="modal-title">Rent Equipment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="cartEquipmentID" id="rentEquipmentID">
                                <input type="hidden" name="cartEquipmentName" id="rentEquipmentNameHidden">
                                <input type="hidden" name="cartPrice" id="rentEquipmentPriceHidden">

                                <div class="form-group">
                                    <label>Equipment</label>
                                    <input type="text" id="rentEquipmentName" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Available Quantity</label>
                                    <input type="text" id="rentEquipmentQuantity" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Price Per Day</label>
                                    <input type="text" id="rentEquipmentPrice" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Quantity To Rent</label>
                                    <input type="number" name="cartQuantity"
                                        placeholder="Enter quantity" min="1" required>
                                </div>

                                <!-- CSRF Token -->
                                <input type="hidden" name="csrfToken"
                                    value="<?php echo htmlspecialchars($_SESSION['csrfToken'], ENT_QUOTES, 'UTF-8'); ?>">

                                <div class="modal-footer dtr-modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="addToCartBtn" class="btn btn-primary">Add to Cart</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->

        </div>
        <!-- End Main Content -->

    </div>
    <!-- End Admin Wrapper -->

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
            var equipmentPrice    = rentBtn.getAttribute('data-price');

            document.getElementById('rentEquipmentID').value          = equipmentID;
            document.getElementById('rentEquipmentNameHidden').value  = equipmentName;
            document.getElementById('rentEquipmentName').value        = equipmentName;
            document.getElementById('rentEquipmentQuantity').value    = equipmentQuantity;
            document.getElementById('rentEquipmentPrice').value       = '£' + parseFloat(equipmentPrice).toFixed(2);
            document.getElementById('rentEquipmentPriceHidden').value = equipmentPrice;
        });

        // Browse search and filter
        function browseSearch() {
            var searchInput     = document.getElementById('browseSearchInput').value.toLowerCase();
            var categoryFilter  = document.getElementById('browseCategoryFilter').value.toLowerCase();
            var conditionFilter = document.getElementById('browseConditionFilter').value.toLowerCase();
            var cards           = document.querySelectorAll('.equipment-card');

            cards.forEach(function(card) {
                var cardName      = card.getAttribute('data-name');
                var cardCategory  = card.getAttribute('data-category');
                var cardCondition = card.getAttribute('data-condition');

                var matchesSearch    = cardName.includes(searchInput);
                var matchesCategory  = categoryFilter === '' || cardCategory.includes(categoryFilter);
                var matchesCondition = conditionFilter === '' || cardCondition.includes(conditionFilter);

                if (matchesSearch && matchesCategory && matchesCondition) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

    <?php include '../includes/theme.php'; ?>

</body>
</html>