<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_name("employerSession");
session_start();

// Include the database connection file
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['employer_email'])) {
    header("Location: ../employer-login-signup.php?type=error&message=You must log in to update your password.");
    exit;
}

// Get the current email from the session
$currentEmail = $_SESSION['employer_email'];

// Get the form data
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Validate the form data
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    header("Location: ../employer-settings.php?type=error&message=All fields are required.");
    exit;
}

if ($newPassword !== $confirmPassword) {
    header("Location: ../employer-settings.php?type=error&message=New password and confirmation password do not match.");
    exit;
}

// Validate password strength
if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
    header("Location: ../employer-settings.php?type=error&message=Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.");
    exit;
}

// Fetch the current password hash from the database
$query = "SELECT password, company FROM employer WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $currentEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../employer-settings.php?type=error&message=User not found.");
    exit;
}

$row = $result->fetch_assoc();
$hashedPassword = $row['password'];
$companyName = $row['company'];

// Verify the current password (hashed with md5)
if (md5($currentPassword) !== $hashedPassword) {
    header("Location: ../employer-settings.php?type=error&message=Current password is incorrect.");
    exit;
}

// Hash the new password using md5
$newHashedPassword = md5($newPassword);

// Update the password in the database
$updateQuery = "UPDATE employer SET password = ?, secret = ? WHERE email = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("sss", $newHashedPassword, $newPassword, $currentEmail);

if ($updateStmt->execute()) {
    // Log the action in the action table
    $actionQuery = "INSERT INTO action (company, date, action, ip, time) VALUES (?, ?, ?, ?, ?)";
    $actionStmt = $conn->prepare($actionQuery);

    $date = date("Y-m-d");
    $time = date("H:i:s");
    $action = "Employer - Update Password";
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    $actionStmt->bind_param("sssss", $companyName, $date, $action, $ipAddress, $time);
    $actionStmt->execute();
    $actionStmt->close();
    
    header("Location: ../employer-settings.php?type=success&message=Password updated successfully.");
} else {
    header("Location: ../employer-settings.php?type=error&message=Failed to update password.");
}

// Close the statements and connection
$stmt->close();
$updateStmt->close();
$conn->close();
?>