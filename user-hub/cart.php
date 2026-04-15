<?php
session_start();
require_once '../config/database.php';

// Session Protection for Users Only
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

// Initialise cart in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if (isset($_POST['addToCartBtn'])) {
    $cartEquipmentID   = intval($_POST['cartEquipmentID']);
    $cartEquipmentName = htmlspecialchars(trim($_POST['cartEquipmentName']), ENT_QUOTES, 'UTF-8');
    $cartPrice         = floatval($_POST['cartPrice']);
    $cartQuantity      = intval($_POST['cartQuantity']);
    $cartDueDate       = htmlspecialchars(trim($_POST['cartDueDate']), ENT_QUOTES, 'UTF-8');

    // Add item to cart
    $_SESSION['cart'][] = [
        'equipmentID'   => $cartEquipmentID,
        'equipmentName' => $cartEquipmentName,
        'price'         => $cartPrice,
        'quantity'      => $cartQuantity,
        'dueDate'       => $cartDueDate
    ];

    header("Location: cart.php?success=added");
    exit();
}

// Remove from cart
if (isset($_GET['remove'])) {
    $removeIndex = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$removeIndex])) {
        array_splice($_SESSION['cart'], $removeIndex, 1);
    }
    header("Location: cart.php");
    exit();
}

// Checkout — process all cart items
if (isset($_POST['checkoutBtn'])) {
    $checkoutDueDate = htmlspecialchars(trim($_POST['cartDueDate']), ENT_QUOTES, 'UTF-8');
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $cartItem) {
            $checkoutEquipmentID = $cartItem['equipmentID'];
            $checkoutQuantity    = $cartItem['quantity'];
            $checkoutUserID      = $_SESSION['userID'];

            // Check availability
            $checkAvailQuery    = "SELECT quantity, availability_status FROM equipment WHERE id = ?";
            $checkAvailPrepared = $dbConn->prepare($checkAvailQuery);
            $checkAvailPrepared->bind_param("i", $checkoutEquipmentID);
            $checkAvailPrepared->execute();
            $checkAvailResult   = $checkAvailPrepared->get_result();
            $checkAvailData     = $checkAvailResult->fetch_assoc();

            if ($checkAvailData && $checkAvailData['quantity'] >= $checkoutQuantity) {
                // Insert rental
                $insertRentalQuery    = "INSERT INTO rentals (user_id, equipment_id, rent_date, due_date, status, quantity)
                                        VALUES (?, ?, NOW(), ?, 'Active', ?)";
                $insertRentalPrepared = $dbConn->prepare($insertRentalQuery);
                $insertRentalPrepared->bind_param(
                    "iisi",
                    $checkoutUserID,
                    $checkoutEquipmentID,
                    $checkoutDueDate,
                    $checkoutQuantity
                );
                $insertRentalPrepared->execute();

                // Update equipment quantity
                $newQuantity = $checkAvailData['quantity'] - $checkoutQuantity;

                if ($newQuantity === 0) {
                    $newStatus = 'Unavailable';
                } elseif ($newQuantity <= 3) {
                    $newStatus = 'Limited';
                } else {
                    $newStatus = 'Available';
                }

                $updateEquipQuery    = "UPDATE equipment SET quantity = ?, availability_status = ? WHERE id = ?";
                $updateEquipPrepared = $dbConn->prepare($updateEquipQuery);
                $updateEquipPrepared->bind_param("isi", $newQuantity, $newStatus, $checkoutEquipmentID);
                $updateEquipPrepared->execute();
            }
        }

        // Clear cart after checkout
        $_SESSION['cart'] = [];
        header("Location: cart.php?success=checkedout");
        exit();
    }
}

// Messages
$cartMessage     = "";
$cartMessageType = "";
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'added') {
        $cartMessage     = "Item added to cart!";
        $cartMessageType = "success";
    } elseif ($_GET['success'] == 'checkedout') {
        $cartMessage     = "Rental confirmed! Your items are now active.";
        $cartMessageType = "success";
    }
}

// Calculate cart total
$cartTotal = 0;
foreach ($_SESSION['cart'] as $cartItem) {
    $cartTotal += $cartItem['price'] * $cartItem['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanalTech Rentals - My Cart</title>
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
                <a href="dashboard.php" class="nav-link">
                    My Dashboard
                </a>
                <a href="browse.php" class="nav-link">
                    Browse Equipment
                </a>
                <a href="cart.php" class="nav-link active">
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
                <h1>My Cart</h1>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button class="theme-toggle" id="themeToggleBtn" onclick="toggleTheme()">Dark</button>
                    <div class="admin-profile">
                        Welcome, <?php echo htmlspecialchars($_SESSION['userFirstName'], ENT_QUOTES, 'UTF-8'); ?>!
                    </div>
                </div>
            </div>

            <!-- Cart Message -->
            <?php if (!empty($cartMessage)) { ?>
                <div class="alert alert-<?php echo ($cartMessageType == 'error') ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $cartMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <!-- Cart Table -->
            <div class="admin-table-card">
                <h2>Cart Items</h2>

                <?php if (!empty($_SESSION['cart'])) { ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Equipment</th>
                                <th>Quantity</th>
                                <th>Price Per Day</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart'] as $cartIndex => $cartItem) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cartItem['equipmentName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($cartItem['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>£<?php echo number_format($cartItem['price'], 2); ?></td>
                                    <td>
                                        <a href="cart.php?remove=<?php echo $cartIndex; ?>"
                                        class="btn-delete"
                                        onclick="return confirm('Remove this item from cart?')">
                                            Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Cart Total & Checkout -->
                    <div class="cart-summary">
                        <div class="cart-total">
                            <span>Estimated Total Per Day:</span>
                            <strong>£<?php echo number_format($cartTotal, 2); ?></strong>
                        </div>
                        <p class="cart-note">
                            Review your items above and confirm your rental.
                            Our team will be in touch to finalise your order.
                        </p>
                        <form method="POST" action="cart.php">
                            <div class="form-group">
                                <label>Due Date</label>
                                 <input type="date" name="cartDueDate" required
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            <input type="hidden" name="csrfToken" 
                            value="<?php echo htmlspecialchars($_SESSION['csrfToken'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" name="checkoutBtn" class="login-btn">
                                Confirm Rental
                            </button>
                        </form>
                    </div>

                <?php } else { ?>
                    <div class="empty-table" style="padding: 40px; text-align:center;">
                        <p>Your cart is empty.</p>
                        <a href="browse.php" class="btn-edit" style="margin-top:12px; display:inline-block;">
                            Browse Equipment
                        </a>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../includes/theme.php'; ?>
</body>
</html>