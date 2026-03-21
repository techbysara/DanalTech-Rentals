
<?php
    // DanalTech Workspace Equipment Rentals
    // Database Configuration

    $serverName = "localhost";
    $dbUser = "root";
    $dbPassword = "";
    $dbName = "danaltech_rentals";
    

    // Create connection
    $dbConn = new mysqli($serverName, $dbUser, $dbPassword, $dbName);

    // Test connection
if ($dbConn->connect_error) {
    die("Connection failed: " . $dbConn->connect_error());
}
?>