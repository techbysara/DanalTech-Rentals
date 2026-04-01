<?php
session_start();

// Logout Session
session_destroy();
header("Location: login.php");
exit();
?>