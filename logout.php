<?php
// Start the session to access session variables
session_start();

// Destroy the session
session_unset();  // Clear all session variables
session_destroy();  // Destroy the session

// Redirect to the login page (or any page you want)
header("Location: login.php");
exit();
?>
