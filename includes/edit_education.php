<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to edit education.");
    exit;
}

// Get the logged-in user's email from the session
$seekerEmail = $_SESSION['seeker_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the request is for deleting a record
    if (isset($_POST['delete']) && isset($_POST['educationId'])) {
        $educationId = intval($_POST['educationId']); // Get the education ID to delete

        // Fetch the file path of the attachment
        $fileQuery = "SELECT attachment_url FROM seaman_educ WHERE id = ? AND email = ?";
        $stmt = $conn->prepare($fileQuery);
        $stmt->bind_param("is", $educationId, $seekerEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $filePath = "../Uploads/Seaman/Education/" . $row['attachment_url'];

            // Delete the record from the database
            $deleteQuery = "DELETE FROM seaman_educ WHERE id = ? AND email = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("is", $educationId, $seekerEmail);

            if ($stmt->execute()) {
                // Delete the file from the server if it exists
                if (!empty($row['attachment_url']) && file_exists($filePath)) {
                    unlink($filePath); // Delete the file
                }
                header("Location: ../userprofile.php?type=success&message=Education record deleted successfully.");
                exit;
            } else {
                header("Location: ../userprofile.php?type=error&message=Failed to delete education record.");
                exit;
            }
        } else {
            header("Location: ../userprofile.php?type=error&message=Education record not found.");
            exit;
        }
    }

    // Otherwise, handle the update functionality
    if (isset($_POST['educationId'])) {
        $educationId = intval($_POST['educationId']); // Get the education ID to update

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

        // Build the update query dynamically
        $updateQuery = "UPDATE seaman_educ 
                        SET school_name = ?, educ_level = ?, field_of_study = ?, from_date = ?, to_date = ?";
        $params = [$schoolName, $educationLevel, $fieldOfStudy, $fromDate, $toDate];
        $types = "sssss";

        // Include the attachment URL if a new file was uploaded
        if ($attachmentUrl) {
            $updateQuery .= ", attachment_url = ?";
            $params[] = $attachmentUrl;
            $types .= "s";
        }

        $updateQuery .= " WHERE id = ? AND email = ?";
        $params[] = $educationId;
        $params[] = $seekerEmail;
        $types .= "is";

        // Prepare and execute the query
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            header("Location: ../userprofile.php?type=success&message=Education record updated successfully.");
            exit;
        } else {
            header("Location: ../userprofile.php?type=error&message=Failed to update education record.");
            exit;
        }
    }
} else {
    // Redirect if the request method is not POST
    header("Location: ../userprofile.php");
    exit;
}
?>