<?php
session_start(); // Start the session

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page with a success message
header("Location: ../user-login-signup.php?type=success&message=You have been logged out successfully.");
exit;
?>