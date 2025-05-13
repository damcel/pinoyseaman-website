<?php
session_name("employerSession");
session_start();

// Include the database connection file
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['employer_email'])) {
    header("Location: ../employer-login-signup.php?type=error&message=You must log in to update your email.");
    exit;
}

// Get the current email from the session
$currentEmail = $_SESSION['employer_email'];

// Get the form data
$newEmail = $_POST['newEmail'] ?? '';
$confirmEmail = $_POST['confirmEmail'] ?? '';
$submittedCurrentEmail = $_POST['currentEmail'] ?? '';

// Validate the form data
if (empty($newEmail) || empty($confirmEmail) || empty($submittedCurrentEmail)) {
    header("Location: ../employer-settings.php?type=error&message=All fields are required.");
    exit;
}

if ($submittedCurrentEmail !== $currentEmail) {
    header("Location: ../employer-settings.php?type=error&message=Current email does not match.");
    exit;
}

if ($newEmail !== $confirmEmail) {
    header("Location: ../employer-settings.php?type=error&message=New email and confirmation email do not match.");
    exit;
}

// Check if the new email already exists in the database
$query = "SELECT email FROM employer WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $newEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: ../employer-settings.php?type=error&message=The new email is already in use.");
    exit;
}

// Update the email in the database
$updateQuery = "UPDATE employer SET email = ? WHERE email = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("ss", $newEmail, $currentEmail);

if ($updateStmt->execute()) {
    // Update the session email
    $_SESSION['employer_email'] = $newEmail;

    header("Location: ../employer-settings.php?type=success&message=Email updated successfully.");
} else {
    header("Location: ../employer-settings.php?type=error&message=Failed to update email.");
}

// Close the statements and connection
$stmt->close();
$updateStmt->close();
$conn->close();
?>