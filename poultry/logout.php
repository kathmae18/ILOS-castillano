<?php
session_start(); // Start the session

// Destroy all session data
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session itself

// Optional: redirect to login page with a message
session_start(); // Restart session to set message
$_SESSION['message'] = "You have been logged out successfully.";
header("Location: login.php");
exit();
?>
