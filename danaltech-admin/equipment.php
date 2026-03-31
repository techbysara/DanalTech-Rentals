<?php
session_start();
require_once '../config/database.php';

// Equipment Management Session Protect for Admins Only

if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Add Equipment Config
if (isset($_POST['addEquipmentBtn'])) {
    $equipmentName          = trim($_POST['equipmentName']);
    $equipmentCategory      = trim($_POST['equipmentCategory']);
    $serialNumber           = trim($_POST['serialNumber']);
    $equipCondition         = trim($_POST['equipCondition']);
    $equipmentQuantity      = trim($_POST['equipmentQuantity']);
    $availabilityStatus     = trim($_POST['availabilityStatus']);
    $featuredDeal            = trim($_POST['featuredDeal']);
    $dealDiscount           = trim($_POST['dealDiscount']);

    $addEquipmentQuery      = "INSERT INTO equipment (name, category, serialNumber, equip_condition, quantity, availability_status, featured_deal, deal_discount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $addEquipmentPrepared = $dbConn->prepare($addEquipmentQuery);
    $addEquipmentPrepared->bind_param(
        "ssssisis",
        $equipmentName,
        $equipmentCategory,
        $serialNumber,
        $equipCondition,
        $equipmentQuantity,
        $availabilityStatus,
        $featuredDeal,
        $dealDiscount
    );

    if ($addEquipmentPrepared->execute()) {
        header("Location: equipment.php?success=equipmentadded");
        exit();

    } else {
        header("Location: equipment.php?error=addfailed");
        exit();
    }
}

// Edit Equipment Config
if (isset($_POST['addEquipmentBtn'])) {
    $editEquipmentID          = trim($_POST['editEquipmentID']);
    $equipmentName           = trim($_POST['equipmentName']);
    $equipmentCategory      = trim($_POST['equipmentCategory']);
    $erialNumber           = trim($_POST['serialNumber']);
    $equipCondition         = trim($_POST['equipCondition']);
    $equipmentQuantity      = trim($_POST['equipmentQuantity']);
    $availabilityStatus     = trim($_POST['availabilityStatus']);
    $featuredDeal            = trim($_POST['featuredDeal']);
    $dealDiscount           = trim($_POST['dealDiscount']);

    $editEquipmentQuery      = "UPDATE equipment SET name = ?, category = ?, serialNumber = ?,
                            equip_condition = ?, quantity = ?,
                            availability_status = ?, featured_deal = ?, deal_discount = ?
                            WHERE id = ?";

    $editEquipmentPrepared = $dbConn->prepare($editEquipmentQuery);
    $editEquipmentPrepared->bind_param(
        "ssssisisi",
        $equipmentName,
        $equipmentCategory,
        $serialNumber,
        $equipCondition,
        $equipmentQuantity,
        $availabilityStatus,
        $featuredDeal,
        $dealDiscount,
        $editEquipmentID
    );

    if ($editEquipmentPrepared->execute()) {
        header("Location: equipment.php?success=equipmentupdated");
        exit();

    } else {
        header("Location: equipment.php?error=editfailed");
        exit();
    }
}

// Delete Equipment Config
if (isset($_GET['delete'])) { 
    $deleteEquipmentID          = intval($_GET['delete']);
    $deleteEquipmentQuery       = "DELETE FROM equipment WHERE id = ?";
    $deleteEquipmentPrepared    = $dbConn->prepare($deleteEquipmentQuery);
    $deleteEquipmentPrepared->bind_param("i", $deleteEquipmentID);
    $deleteEquipmentPrepared->execute();
    header("Location: equipment.php?success=equipmentdeleted");
    exit();
}

// Retrieve all Equipment
$equipmentListQuery      = "SELECT * FROM equipment ORDER BY created_at DESC";
$equipmentListResult     = $dbConn->query($equipmentListQuery);

// Equipment Message
$equipmentMessage   = "";
$equipmentMessageType  = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'equipmentadded') {
        $equipmentMessage = "Equipment added successfully!";
        $equipmentMessageType = "success";

    } elseif ($_GET['success'] =='equipmentdeleted') {
        $equipmentMessage = "Successfully deleted equipment";
        $equipmentMessageType = "success";

    } elseif ($_GET['success'] == 'equipmentupdated') {
        $equipmentMessage = " Update equipment completed";
        $equipmentMessageType = "success";
    }
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'addfailed') {
        $equipmentMessage = "Failed to add equipment. Please try again.";
        $equipmentMessageType = "error";

    } elseif ($_GET['error']  == 'editfailed') {
        $equipmentMessageType = "Failed to update equipment. Please try again.";
        $equipmentMessageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - Equipment Management</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DanalTech Custom CSS -->

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
                    Dashboard
                </a>
                <a href="equipment.php" class="nav-link active">
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
                <h1>Equipment Management</h1>
                <div class="admin-profile">
                    👤 Welcome, <?php echo $_SESSION['userFirstName']; ?>!
                </div>
            </div>

            <!-- Equipment Message -->
            <?php if (!empty($equipmentMessage)) { ?>
                <div class="alert alert-<?php echo ($equipmentMessageType == 'error') ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $equipmentMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <!-- Add Equipment Button -->
            <div class="mb-4">
                <button type="button" 
                class="btn btn-primary" 
                data-bs-toggle="modal" 
                data-bs-target="#addEquipmentModal">
                    Add New Equipment
                </button>
            </div>

            <!-- Equipment Inventory Table -->
            <div class="admin-table-card">
                <h2>Equipment Inventory</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Serial No.</th>
                            <th>Condition</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Deal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($equipmentListResult->num_rows > 0) { ?>
                            <?php while ($equipmentRow = $equipmentListResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $equipmentRow['name']; ?></td>
                                    <td><?php echo $equipmentRow['category']; ?></td>
                                    <td><?php echo $equipmentRow['serialNumber']; ?></td>
                                    <td><?php echo $equipmentRow['equip_condition']; ?></td>
                                    <td><?php echo $equipmentRow['quantity']; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($equipmentRow['availability_status']); ?>">
                                            <?php echo $equipmentRow['availability_status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $equipmentRow['featured_deal']; ?></td>
                                    <td>

                                        <!-- Edit Button -->
                                        <button type="button" 
                                        class="btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editEquipmentModal"
                                        data-id="<?php echo $equipmentRow['id']; ?>"
                                        data-name="<?php echo $equipmentRow['name']; ?>"
                                        data-category="<?php echo $equipmentRow['category']; ?>"
                                        data-serial="<?php echo $equipmentRow['serialNumber']; ?>"
                                        data-condition="<?php echo $equipmentRow['equip_condition']; ?>"
                                        data-quantity="<?php echo $equipmentRow['quantity']; ?>"
                                        data-status="<?php echo $equipmentRow['availability_status']; ?>"
                                        data-deal="<?php echo $equipmentRow['featured_deal']; ?>"
                                        data-discount="<?php echo $equipmentRow['deal_discount']; ?>">
                                            Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <a href="equipment.php?delete=<?php echo $equipmentRow['id']; ?>"
                                        class="btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this equipment?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="empty-table">
                                    No equipment found. Click "Add New Equipment" to get started!
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- ADD EQUIPMENT MODAL -->
            <div class="modal fade" id="addEquipmentModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content dtr-modal">
                        <div class="modal-header dtr-modal-header">
                            <h5 class="modal-title">Add New Equipment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="equipment.php">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Equipment Name</label>
                                        <input type="text" name="equipmentName"
                                        placeholder="Enter equipment name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="equipmentCategory" required>
                                            <option value="">Select Category</option>
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
                                    <div class="form-group">
                                        <label>Serial Number</label>
                                        <input type="text" name="serialNumber"
                                        placeholder="Enter serial number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Condition</label>
                                        <select name="equipCondition" required>
                                            <option value="">Select Condition</option>
                                            <option value="Excellent">Excellent</option>
                                            <option value="Good">Good</option>
                                            <option value="Fair">Fair</option>
                                            <option value="Poor">Poor</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" name="equipmentQuantity"
                                        placeholder="Enter quantity" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Availability Status</label>
                                        <select name="availabilityStatus" required>
                                            <option value="Available">Available</option>
                                            <option value="Limited">Limited</option>
                                            <option value="Unavailable">Unavailable</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Featured Deal</label>
                                        <select name="featuredDeal">
                                            <option value="None">None</option>
                                            <option value="Wednesday Deal">Wednesday Deal</option>
                                            <option value="Weekend Special">Weekend Special</option>
                                            <option value="Flash Sale">Flash Sale</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Deal Discount (%)</label>
                                        <input type="number" name="dealDiscount"
                                        placeholder="Enter discount" min="0" max="100" value="0">
                                    </div>
                                </div>
                                <div class="modal-footer dtr-modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="addEquipmentBtn" class="btn btn-primary">Add Equipment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EDIT EQUIPMENT MODAL Config-->
            <div class="modal fade" id="editEquipmentModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content dtr-modal">
                        <div class="modal-header dtr-modal-header">
                            <h5 class="modal-title">Edit Equipment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="equipment.php">
                                <input type="hidden" name="editEquipmentID" id="editEquipmentID">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Equipment Name</label>
                                        <input type="text" name="equipmentName" id="editEquipmentName"
                                        placeholder="Enter equipment name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="equipmentCategory" id="editEquipmentCategory" required>
                                            <option value="">Select Category</option>
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
                                    <div class="form-group">
                                        <label>Serial Number</label>
                                        <input type="text" name="serialNumber" id="editSerialNumber"
                                        placeholder="Enter serial number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Condition</label>
                                        <select name="equipCondition" id="editEquipCondition" required>
                                            <option value="">Select Condition</option>
                                            <option value="Excellent">Excellent</option>
                                            <option value="Good">Good</option>
                                            <option value="Fair">Fair</option>
                                            <option value="Poor">Poor</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" name="equipmentQuantity" id="editEquipmentQuantity"
                                        placeholder="Enter quantity" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Availability Status</label>
                                        <select name="availabilityStatus" id="editAvailabilityStatus" required>
                                            <option value="Available">Available</option>
                                            <option value="Limited">Limited</option>
                                            <option value="Unavailable">Unavailable</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Featured Deal</label>
                                        <select name="featuredDeal" id="editFeaturedDeal">
                                            <option value="None">None</option>
                                            <option value="Wednesday Deal">Wednesday Deal</option>
                                            <option value="Weekend Special">Weekend Special</option>
                                            <option value="Flash Sale">Flash Sale</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Deal Discount (%)</label>
                                        <input type="number" name="dealDiscount" id="editDealDiscount"
                                        placeholder="Enter discount" min="0" max="100">
                                    </div>
                                </div>
                                <div class="modal-footer dtr-modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="editEquipmentBtn" class="btn btn-primary">Save Changes</button>
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

    <!-- Edit Modal Population Script -->
    <script>
        // Populate edit modal with equipment data
        var editEquipmentModal = document.getElementById('editEquipmentModal');
        editEquipmentModal.addEventListener('show.bs.modal', function(event) {
            var editBtn                 = event.relatedTarget;
            var equipmentID             = editBtn.getAttribute('data-id');
            var equipmentName           = editBtn.getAttribute('data-name');
            var equipmentCategory       = editBtn.getAttribute('data-category');
            var equipmentSerial         = editBtn.getAttribute('data-serial');
            var equipmentCondition      = editBtn.getAttribute('data-condition');
            var equipmentQuantity       = editBtn.getAttribute('data-quantity');
            var equipmentStatus         = editBtn.getAttribute('data-status');
            var equipmentDeal           = editBtn.getAttribute('data-deal');
            var equipmentDiscount       = editBtn.getAttribute('data-discount');

            document.getElementById('editEquipmentID').value           = equipmentID;
            document.getElementById('editEquipmentName').value         = equipmentName;
            document.getElementById('editEquipmentCategory').value     = equipmentCategory;
            document.getElementById('editSerialNumber').value          = equipmentSerial;
            document.getElementById('editEquipCondition').value        = equipmentCondition;
            document.getElementById('editEquipmentQuantity').value     = equipmentQuantity;
            document.getElementById('editAvailabilityStatus').value    = equipmentStatus;
            document.getElementById('editFeaturedDeal').value          = equipmentDeal;
            document.getElementById('editDealDiscount').value          = equipmentDiscount;
        });
    </script>

</body>
</html>

