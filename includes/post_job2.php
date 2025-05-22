<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_name("employerSession");
session_start();

// Include the database connection file
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['employer_email'])) {
    header("Location: ../employer-login-signup.php?type=error&message=You must log in to post a job.");
    exit;
}

// Get the employer's email from the session
$employerEmail = $_SESSION['employer_email'];

// Fetch company details using the employer's email
$companyQuery = "SELECT company, email, company_code FROM employer WHERE email = ?";
$companyStmt = $conn->prepare($companyQuery);
$companyStmt->bind_param("s", $employerEmail);
$companyStmt->execute();
$companyResult = $companyStmt->get_result();

if ($companyResult->num_rows === 0) {
    header("Location: ../employer-posting.php?type=error&message=Company details not found.");
    exit;
}

$companyRow = $companyResult->fetch_assoc();
$companyName = $companyRow['company'];
$companyEmail = $companyRow['email'];
$companyCode = $companyRow['company_code'];

// Get the form data
$jobTitle = $_POST['jobPostName'] ?? '';
$rank = $_POST['rank'] ?? '';
$contractLength = $_POST['contractLength'] ?? '';
$vesselType = $_POST['vesselType'] ?? '';
$jobRequirements = $_POST['jobRequirements'] ?? '';
$jobDescription = $_POST['jobDescription'] ?? '';

// Validate the form data
if (empty($jobTitle) || empty($rank) || empty($contractLength) || empty($vesselType) || empty($jobRequirements) || empty($jobDescription)) {
    header("Location: ../employer-posting.php?type=error&message=All fields are required.");
    exit;
}

// Insert the job post into the database
$insertQuery = "INSERT INTO jobs (job_title, rank, contract, vessel, requirements, job_description, company_name, email, company_code, date_posted, expiry) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))";
$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param(
    "sssssssss",
    $jobTitle,
    $rank,
    $contractLength,
    $vesselType,
    $jobRequirements,
    $jobDescription,
    $companyName,
    $companyEmail,
    $companyCode
);

if ($insertStmt->execute()) {
    header("Location: ../employer-posting.php?type=success&message=Job posted successfully.");
} else {
    header("Location: ../employer-posting.php?type=error&message=Failed to post job.");
}

// Close the statements and connection
$companyStmt->close();
$insertStmt->close();
$conn->close();
?>