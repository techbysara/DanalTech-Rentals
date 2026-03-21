
<?php
require_once 'config/database.php';

if ($dbConn) {
    echo "Welcome to DanalTech Workspace Equipment Rentals!";
} else {
    echo "Connection failed!";
}
?>