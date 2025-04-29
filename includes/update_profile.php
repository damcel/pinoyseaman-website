<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to update your profile.");
    exit;
}

// Get the logged-in user's ID
$seekerId = $_SESSION['seeker_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $firstName = filter_var(trim($_POST['firstName']), FILTER_SANITIZE_STRING);
    $middleName = filter_var(trim($_POST['middleName']), FILTER_SANITIZE_STRING);
    $lastName = filter_var(trim($_POST['lastName']), FILTER_SANITIZE_STRING);
    $address = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
    $placeOfBirth = filter_var(trim($_POST['placeOfBirth']), FILTER_SANITIZE_STRING);
    $dob = $_POST['dob']; // Date of birth (no need to sanitize as it's a date)
    $gender = filter_var(trim($_POST['gender']), FILTER_SANITIZE_STRING);
    $maritalStatus = filter_var(trim($_POST['maritalStatus']), FILTER_SANITIZE_STRING);
    $nationality = filter_var(trim($_POST['nationality']), FILTER_SANITIZE_STRING);
    $religion = filter_var(trim($_POST['religion']), FILTER_SANITIZE_STRING);
    $rank = filter_var(trim($_POST['rank']), FILTER_SANITIZE_STRING);
    $englishLevel = filter_var(trim($_POST['englishLevel']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']); // Password input

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($dob)) {
        header("Location: ../userprofile.php?type=error&message=First name, last name, and date of birth are required.");
        exit;
    }

    // Build the update query dynamically
    $updateQuery = "UPDATE job_seeker 
                    SET first_name = ?, middle_name = ?, last_name = ?, address = ?, place_of_birth = ?, 
                        birthday = ?, gender = ?, marital_status = ?, nationality = ?, religion = ?, rank = ?, 
                        english_level = ?";
    $params = [
        $firstName,
        $middleName,
        $lastName,
        $address,
        $placeOfBirth,
        $dob,
        $gender,
        $maritalStatus,
        $nationality,
        $religion,
        $rank,
        $englishLevel
    ];

    // Check if the password is provided
    if (!empty($password)) {
        $hashedPassword = md5($password); // Hash the password
        $updateQuery .= ", password = ?";
        $params[] = $hashedPassword;
    }

    $updateQuery .= " WHERE email = ?";
    $params[] = $seekerId;

    // Prepare and execute the query
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);

    if ($stmt->execute()) {
        // Log the action into the actions table
        $actionQuery = "INSERT INTO action (date, action, seaman, ip, time) 
                        VALUES (NOW(), ?, ?, ?, NOW())";
        $actionStmt = $conn->prepare($actionQuery);
        $actionType = "Seaman Profile Update";
        $actionDescription = "User updated their profile information.";
        $userIp = $_SERVER['REMOTE_ADDR']; // Get the user's IP address
        $actionStmt->bind_param("sss", $actionType, $seekerId, $userIp);
        $actionStmt->execute();

        // Redirect back to the profile page with a success message
        header("Location: ../userprofile.php?type=success&message=Profile updated successfully.");
        exit;
    } else {
        // Redirect back to the profile page with an error message
        header("Location: ../userprofile.php?type=error&message=Failed to update profile. Please try again.");
        exit;
    }
} else {
    // Redirect if the request method is not POST
    header("Location: ../userprofile.php");
    exit;
}
?>