<?php
session_start();

// Logout Session
session_destroy();
header("Location: index.php");
exit();
?>