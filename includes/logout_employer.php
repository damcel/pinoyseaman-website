<?php
session_name("employerSession");
session_start(); // Start the session

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page with a success message
header("Location: ../employer-login-signup.php?type=success&message=You have been logged out successfully.");
exit;
?>