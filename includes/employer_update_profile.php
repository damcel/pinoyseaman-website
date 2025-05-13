<?php
session_name("employerSession");
session_start();

// Include the database connection file
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['employer_email'])) {
    header("Location: ../employer-login-signup.php?type=error&message=You must log in to update your profile.");
    exit;
}

// Get the employer's email from the session
$employerEmail = $_SESSION['employer_email'];

// Initialize variables
$companyName = $_POST['companyName'] ?? '';
$companyWebsite = $_POST['companyWebsite'] ?? '';
$contactPerson = $_POST['contactPerson'] ?? '';
$companyPhone = $_POST['companyPhone'] ?? '';
$companyAddress = $_POST['companyAddress'] ?? '';
$aboutCompany = $_POST['aboutCompany'] ?? '';
$logoFileName = null;

// Handle the logo upload
if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../company-logo/';
    $fileTmpPath = $_FILES['company_logo']['tmp_name'];
    $fileOriginalName = $_FILES['company_logo']['name'];
    $fileExtension = pathinfo($fileOriginalName, PATHINFO_EXTENSION);

    // Allowed file extensions
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (!in_array($fileExtension, $allowedExtensions)) {
        header("Location: ../employer-settings.php?type=error&message=Invalid file type. Only JPG, JPEG, and PNG files are allowed.");
        exit;
    }

    // Generate a unique file name
    $uniqueFileName = uniqid('logo_', true) . '.' . $fileExtension;

    // Move the uploaded file to the target directory
    $destinationPath = $uploadDir . $uniqueFileName;
    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
        $logoFileName = $uniqueFileName; // Save the unique file name to the database
    } else {
        header("Location: ../employer-settings.php?type=error&message=Failed to upload logo.");
        exit;
    }
}

// Prepare the SQL query to update the employer profile
$query = "UPDATE employer 
          SET company = ?, website = ?, contact = ?, phone = ?, address = ?, company_profile = ?, logo = ?
          WHERE email = ?";
$stmt = $conn->prepare($query);

// Bind the parameters
$stmt->bind_param(
    "ssssssss",
    $companyName,
    $companyWebsite,
    $contactPerson,
    $companyPhone,
    $companyAddress,
    $aboutCompany,
    $logoFileName,
    $employerEmail
);

// Execute the query
if ($stmt->execute()) {
    header("Location: ../employer-settings.php?type=success&message=Profile updated successfully.");
} else {
    header("Location: ../employer-settings.php?type=error&message=Failed to update profile.");
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>