<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to edit experience.");
    exit;
}

// Get the logged-in user's email from the session
$seekerEmail = $_SESSION['seeker_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle deletion
    if (isset($_POST['delete']) && $_POST['delete'] == 1) {
        $typeOfDoc = "Merits Document";

        // Fetch the file URL from the database
        $fetchQuery = "SELECT doc_url FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = ?";
        $stmt = $conn->prepare($fetchQuery);
        $stmt->bind_param("ss", $seekerEmail, $typeOfDoc);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Delete the file from the server
            $filePath = "../Uploads/Seaman/Merits/" . $row['doc_url'];
            if (!empty($row['doc_url']) && file_exists($filePath)) {
                unlink($filePath); // Delete the file
            }

            // Delete the record from the database
            $deleteQuery = "DELETE FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("ss", $seekerEmail, $typeOfDoc);

            if ($stmt->execute()) {
                // Clear the seagoing experience notes in the job_seeker table
                $clearNotesQuery = "UPDATE job_seeker SET merits = NULL WHERE email = ?";
                $stmt = $conn->prepare($clearNotesQuery);
                $stmt->bind_param("s", $seekerEmail);
                $stmt->execute();

                header("Location: ../competency-certificate.php?type=success&message=Competence deleted successfully.");
                exit;
            } else {
                header("Location: ../competency-certificate.php?type=error&message=Failed to delete Competence.");
                exit;
            }
        } else {
            header("Location: ../competency-certificate.php?type=error&message=No Competence found to delete.");
            exit;
        }
    }

    // Retrieve and sanitize the seagoing experience notes
    $editMerits = isset($_POST['editMerits']) ? filter_var(trim($_POST['editMerits']), FILTER_SANITIZE_STRING) : null;

    // Handle file upload
    $attachmentUrl = null;
    if (isset($_FILES['merits_edit_file_upload']) && $_FILES['merits_edit_file_upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/Merits/"; // Directory to store uploaded files
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        $fileExtension = pathinfo($_FILES['merits_edit_file_upload']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension; // Generate a unique file name
        $filePath = $uploadDir . $uniqueFileName;

        // Check file type
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (in_array($_FILES['merits_edit_file_upload']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['merits_edit_file_upload']['tmp_name'], $filePath)) {
                $attachmentUrl = $uniqueFileName; // Save only the file name as the URL
            } else {
                header("Location: ../competency-certificate.php?type=error&message=Failed to upload file.");
                exit;
            }
        } else {
            header("Location: ../competency-certificate.php?type=error&message=Invalid file type. Only PDF and Word documents are allowed.");
            exit;
        }
    }

    // Update the seagoing experience notes in the job_seeker table
    $updateQuery = "UPDATE job_seeker SET merits = ? WHERE email = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ss", $editMerits, $seekerEmail);

    if (!$stmt->execute()) {
        header("Location: ../competency-certificate.php?type=error&message=Failed to update merits.");
        exit;
    }

    // If a new file was uploaded, update the seaman_documents table
    if ($attachmentUrl) {
        $typeOfDoc = "Merits Document";

        // Check if a previous file exists for this user
        $checkQuery = "SELECT doc_url FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $seekerEmail, $typeOfDoc);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Delete the old file
            $row = $result->fetch_assoc();
            $oldFilePath = "../Uploads/Seaman/Merits/" . $row['doc_url'];
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            // Update the existing record
            $updateFileQuery = "UPDATE seaman_documents SET doc_url = ? WHERE seaman_email = ? AND type_of_doc = ?";
            $stmt = $conn->prepare($updateFileQuery);
            $stmt->bind_param("sss", $attachmentUrl, $seekerEmail, $typeOfDoc);
        } else {
            // Insert a new record
            $insertFileQuery = "INSERT INTO seaman_documents (seaman_email, type_of_doc, doc_url) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertFileQuery);
            $stmt->bind_param("sss", $seekerEmail, $typeOfDoc, $attachmentUrl);
        }

        if (!$stmt->execute()) {
            header("Location: ../competency-certificate.php?type=error&message=Failed to update file information.");
            exit;
        }
    }

    header("Location: ../competency-certificate.php?type=success&message=Competence updated successfully.");
    exit;
} else {
    // Redirect if the request method is not POST
    header("Location: ../competency-certificate.php");
    exit;
}
?>