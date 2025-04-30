<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to upload a photo.");
    exit;
}

// Get the logged-in user's email
$seekerEmail = $_SESSION['seeker_id'];

// Check if a file was uploaded
if (isset($_FILES['userPhoto']) && $_FILES['userPhoto']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "../Uploads/Seaman/User-Photo/"; // Directory to store uploaded files
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }

    $fileExtension = pathinfo($_FILES['userPhoto']['name'], PATHINFO_EXTENSION);
    $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension; // Generate a unique file name
    $filePath = $uploadDir . $uniqueFileName;

    // Check file type (optional: restrict to images only)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['userPhoto']['type'], $allowedTypes)) {
        if (move_uploaded_file($_FILES['userPhoto']['tmp_name'], $filePath)) {
            // Delete the old photo if it exists
            $oldPhotoQuery = "SELECT user_photo FROM job_seeker WHERE email = ?";
            $stmt = $conn->prepare($oldPhotoQuery);
            $stmt->bind_param("s", $seekerEmail);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $oldPhoto = $row['user_photo'];
                if (!empty($oldPhoto) && file_exists($uploadDir . $oldPhoto)) {
                    unlink($uploadDir . $oldPhoto); // Delete the old photo
                }
            }

            // Save the new file name to the database
            $updateQuery = "UPDATE job_seeker SET user_photo = ? WHERE email = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ss", $uniqueFileName, $seekerEmail);

            if ($stmt->execute()) {
                header("Location: ../userprofile.php?type=success&message=Photo uploaded successfully.");
                exit;
            } else {
                header("Location: ../userprofile.php?type=error&message=Failed to save photo information to the database.");
                exit;
            }
        } else {
            header("Location: ../userprofile.php?type=error&message=Failed to upload photo.");
            exit;
        }
    } else {
        header("Location: ../userprofile.php?type=error&message=Invalid file type. Only JPG, PNG, and GIF are allowed.");
        exit;
    }
} else {
    header("Location: ../userprofile.php?type=error&message=No file uploaded.");
    exit;
}
?>