<?php
session_start();
require_once '../config/database.php';

//  Rent Equipment Session Protection for Users Only

if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['rentBtn'])) {
    $rentEquipmentID = intval($_POST['rentEquipmentID']);
    $rentQuantity    = intval($_POST['rentQuantity']);
    $rentDueDate     = trim($_POST['rentDueDate']);
    $rentUserID      = $_SESSION['userID'];

    // Check  the equipment availablilty
    $availabilityCheckQuery    = "SELECT * FROM equipment WHERE id = ? AND quantity >= ? 
                                  AND availability_status != 'Unavailable'";

    $availabilityCheckPrepared = $dbConn->prepare($availabilityCheckQuery);
    $availabilityCheckPrepared->bind_param("ii", $rentEquipmentID, $rentQuantity);
    $availabilityCheckPrepared->execute();
    $availabilityCheckResult   = $availabilityCheckPrepared->get_result();

    if ($availabilityCheckResult->num_rows === 0) {
        header("Location: dashboard.php?error=unavailable");
        exit();
    }

    // Insert rental record
    $insertRentalQuery    = "INSERT INTO rentals (user_id, equipment_id, due_date, status) 
                            VALUES (?, ?, ?, 'Active')";

    $insertRentalPrepared = $dbConn->prepare($insertRentalQuery);
    $insertRentalPrepared->bind_param(
        "iis",
        $rentUserID,
        $rentEquipmentID,
        $rentDueDate
    );

    if ($insertRentalPrepared->execute()) {
        // Update equipment quantity
        $newQuantity = $availabilityCheckResult->fetch_assoc()['quantity'] - $rentQuantity;

        // Update availability status based on new quantity
        if ($newQuantity === 0) {
            $newAvailabilityStatus = 'Unavailable';
        } elseif ($newQuantity <= 3) {
            $newAvailabilityStatus = 'Limited';
        } else {
            $newAvailabilityStatus = 'Available';
        }

        $updateEquipmentQuery    = "UPDATE equipment SET quantity = ?, availability_status = ? 
                                   WHERE id = ?";

        $updateEquipmentPrepared = $dbConn->prepare($updateEquipmentQuery);
        $updateEquipmentPrepared->bind_param(
            "isi",
            $newQuantity,
            $newAvailabilityStatus,
            $rentEquipmentID
        );
        $updateEquipmentPrepared->execute();

        header("Location: dashboard.php?success=rented");
        exit();

    } else {
        header("Location: dashboard.php?error=rentfailed");
        exit();
    }
}
?>