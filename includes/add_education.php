<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to add education.");
    exit;
}

// Get the logged-in user's ID and email
$seekerEmail = $_SESSION['seeker_id'];

// Fetch the seaman's ID from the job_seeker table
$seamanIdQuery = "SELECT id FROM job_seeker WHERE email = ?";
$stmt = $conn->prepare($seamanIdQuery);
$stmt->bind_param("s", $seekerEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $seamanId = $row['id'];
} else {
    header("Location: ../userprofile.php?type=error&message=User not found.");
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $schoolName = filter_var(trim($_POST['school']), FILTER_SANITIZE_STRING);
    $educationLevel = filter_var(trim($_POST['educationLevel']), FILTER_SANITIZE_STRING);
    $fieldOfStudy = filter_var(trim($_POST['fieldOfStudy']), FILTER_SANITIZE_STRING);
    $fromDate = $_POST['fromDate']; // Date input (no need to sanitize as it's a date)
    $toDate = $_POST['toDate']; // Date input (no need to sanitize as it's a date)

    // Handle file upload
    $attachmentUrl = null;
    if (isset($_FILES['documentUpload']) && $_FILES['documentUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/Education/"; // Directory to store uploaded files
        $fileExtension = pathinfo($_FILES['documentUpload']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension; // Generate a unique file name
        $filePath = $uploadDir . $uniqueFileName;

        // Check file type
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (in_array($_FILES['documentUpload']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['documentUpload']['tmp_name'], $filePath)) {
                $attachmentUrl = $uniqueFileName; // Save only the file name as the URL
            } else {
                header("Location: ../userprofile.php?type=error&message=Failed to upload document.");
                exit;
            }
        } else {
            header("Location: ../userprofile.php?type=error&message=Invalid file type. Only PDF and Word documents are allowed.");
            exit;
        }
    }

    // Validate required fields
    if (empty($schoolName) || empty($educationLevel) || empty($fieldOfStudy) || empty($fromDate) || empty($toDate)) {
        header("Location: ../userprofile.php?type=error&message=All fields are required.");
        exit;
    }

    // Insert the education details into the database
    $insertQuery = "INSERT INTO seaman_educ (school_name, educ_level, field_of_study, from_date, to_date, attachment_url, email, seaman_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssssss", $schoolName, $educationLevel, $fieldOfStudy, $fromDate, $toDate, $attachmentUrl, $seekerEmail, $seamanId);

    if ($stmt->execute()) {
        // Redirect back to the profile page with a success message
        header("Location: ../userprofile.php?type=success&message=Education added successfully.");
        exit;
    } else {
        // Redirect back to the profile page with an error message
        header("Location: ../userprofile.php?type=error&message=Failed to add education. Please try again.");
        exit;
    }
} else {
    // Redirect if the request method is not POST
    header("Location: ../userprofile.php");
    exit;
}
?>