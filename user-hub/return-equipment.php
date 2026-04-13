<?php
session_start();
require_once '../config/database.php';

// Return Equipment Session Protection for Users Only

if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['rental'])) {
    $rentalID     = intval($_GET['rental']);
    $returnUserID = $_SESSION['userID'];

    // Verify rental belongs to this user
    $rentalCheckQuery    = "SELECT rentals.*, equipment.quantity AS currentQuantity FROM rentals 
                           JOIN equipment ON rentals.equipment_id = equipment.id
                           WHERE rentals.id = ? AND rentals.user_id = ? AND rentals.status = 'Active'";

    $rentalCheckPrepared = $dbConn->prepare($rentalCheckQuery);
    $rentalCheckPrepared->bind_param("ii", $rentalID, $returnUserID);
    $rentalCheckPrepared->execute();
    $rentalCheckResult   = $rentalCheckPrepared->get_result();

    if ($rentalCheckResult->num_rows === 0) {
        header("Location: dashboard.php?error=rentalnotfound");
        exit();
    }

    $rentalData     = $rentalCheckResult->fetch_assoc();
    $equipmentID    = $rentalData['equipment_id'];
    $currentQuantity = $rentalData['currentQuantity'];

    // Update rental status to Returned
    $updateRentalQuery    = "UPDATE rentals SET status = 'Returned', return_date = NOW()
                            WHERE id = ?";

    $updateRentalPrepared = $dbConn->prepare($updateRentalQuery);
    $updateRentalPrepared->bind_param("i", $rentalID);
    $updateRentalPrepared->execute();

    // Increase equipment quantity
    $restoredQuantity = $currentQuantity + $rentalData['quantity'];

    // Update availability status
    if ($restoredQuantity === 0) {
        $restoredAvailabilityStatus = 'Unavailable';
    } elseif ($restoredQuantity <= 3) {
        $restoredAvailabilityStatus = 'Limited';
    } else {
        $restoredAvailabilityStatus = 'Available';
    }

    $updateEquipmentQuery    = "UPDATE equipment SET  quantity = ?, availability_status = ?
                               WHERE id = ?";
                               
    $updateEquipmentPrepared = $dbConn->prepare($updateEquipmentQuery);
    $updateEquipmentPrepared->bind_param(
        "isi",
        $restoredQuantity,
        $restoredAvailabilityStatus,
        $equipmentID
    );
    $updateEquipmentPrepared->execute();

    header("Location: dashboard.php?success=returned");
    exit();
}
?>