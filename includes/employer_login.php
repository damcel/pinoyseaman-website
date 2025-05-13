<?php
session_name("employerSession");
session_start();
require_once "../db.php"; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Retrieve and sanitize input
$employer_email = trim($_POST["employer_email"]);
$employer_password = trim($_POST["employer_password"]);

if (empty($employer_email) || empty($employer_password)) {
    header("Location: ../employer-login-signup.php?type=error&message=Email and Password are required!");
    exit;
}

// Hash the password (assuming passwords are stored as MD5 hashes in the database)
$employer_password_hashed = md5($employer_password);

try {
    // Check if the user exists in the database
    $query = "SELECT * FROM employer WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $employer_email, $employer_password_hashed);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, start session
        $row = $result->fetch_assoc();
        $_SESSION["employer_email"] = $employer_email;
        $_SESSION["employer_pass"] = $employer_password_hashed;
        $_SESSION["company_code"] = $row["company_code"];

        // Update user status to online
        // $update_query = "UPDATE job_seeker SET online = 'y' WHERE email = ?";
        // $update_stmt = $conn->prepare($update_query);
        // $update_stmt->bind_param("s", $employer_email);
        // $update_stmt->execute();

        // Log the successful login action  
        $action_query = "INSERT INTO action (company, action, date, ip, time) VALUES (?, 'Employer Login Successful', CURDATE(), ?, CURTIME())";
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $action_stmt = $conn->prepare($action_query);
        $action_stmt->bind_param("ss", $employer_email, $ip_address);
        $action_stmt->execute();

        header("Location: ../employer-dashboard.php");
        exit;
    } else {
        // Log the failed login attempt
        $action_query = "INSERT INTO action (company, action, date, ip, time) VALUES (?, 'Employer Login Failed', CURDATE(), ?, CURTIME())";
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $action_stmt = $conn->prepare($action_query);
        $action_stmt->bind_param("ss", $employer_email, $ip_address);
        $action_stmt->execute();

        header("Location: ../employer-login-signup.php?type=error&message=Login failed! Please check your credentials.");
        exit;
    }
} catch (Exception $e) {
    header("Location: ../employer-login-signup.php?type=error&message=An error occurred: " . $e->getMessage());
    exit;
}