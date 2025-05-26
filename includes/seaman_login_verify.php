<?php
session_start();
require_once "../db.php"; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Retrieve and sanitize input
$job_seeker_id = filter_var(trim($_POST["job_seeker_id"]), FILTER_SANITIZE_EMAIL);
$job_seeker_password = trim($_POST["job_seeker_password"]);

if (empty($job_seeker_id) || empty($job_seeker_password)) {
    header("Location: ../user-login-signup.php?type=error&message=Email and Password are required!");
    exit;
}

if (!filter_var($job_seeker_id, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../user-login-signup.php?type=error&message=Invalid email format!");
    exit;
}

// Hash the password (assuming passwords are stored as MD5 hashes in the database)
$job_seeker_password_hashed = md5($job_seeker_password);

try {
    // Check if the user exists in the database
    $query = "SELECT * FROM job_seeker WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $job_seeker_id, $job_seeker_password_hashed);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, start session
        $row = $result->fetch_assoc();
        $_SESSION["seeker_id"] = $job_seeker_id;
        $_SESSION["seeker_pass"] = $job_seeker_password_hashed;
        $_SESSION["name"] = $row["first_name"] . " " . $row["last_name"];

        // Update user status to online
        // $update_query = "UPDATE job_seeker SET online = 'y' WHERE email = ?";
        // $update_stmt = $conn->prepare($update_query);
        // $update_stmt->bind_param("s", $job_seeker_id);
        // $update_stmt->execute();

        // Log the successful login action
        $action_query = "INSERT INTO action (seaman, action, date, ip, time) VALUES (?, 'Seaman Login Successful', CURDATE(), ?, CURTIME())";
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $action_stmt = $conn->prepare($action_query);
        $action_stmt->bind_param("ss", $job_seeker_id, $ip_address);
        $action_stmt->execute();

        header("Location: ../userprofile.php");
        exit;
    } else {
        // Log the failed login attempt
        $action_query = "INSERT INTO action (seaman, action, date, ip, time) VALUES (?, 'Seaman Login Failed', CURDATE(), ?, CURTIME())";
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $action_stmt = $conn->prepare($action_query);
        $action_stmt->bind_param("ss", $job_seeker_id, $ip_address);
        $action_stmt->execute();

        header("Location: ../user-login-signup.php?type=error&message=Login failed! Please check your credentials.");
        exit;
    }
} catch (Exception $e) {
    header("Location: ../user-login-signup.php?type=error&message=An error occurred: " . $e->getMessage());
    exit;
}