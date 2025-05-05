<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to edit the Seaman's Book.");
    exit;
}

// Get the logged-in user's email
$seekerEmail = $_SESSION['seeker_id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the delete action was triggered
    if (isset($_POST['delete']) && $_POST['delete'] == 1) {
        // Handle the delete action
        $uploadDir = "../Uploads/Seaman/Passport/";

        // Fetch the current file to delete
        $oldFileQuery = "SELECT doc_url FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = 'Seaman Passport'";
        $stmt = $conn->prepare($oldFileQuery);
        $stmt->bind_param("s", $seekerEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $oldFile = $row['doc_url'];

            // Delete the file from the server
            if (!empty($oldFile) && file_exists($uploadDir . $oldFile)) {
                unlink($uploadDir . $oldFile);
            }
        }

        // Delete the record from the seaman_documents table
        $deleteDocQuery = "DELETE FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = 'Seaman Passport'";
        $stmt = $conn->prepare($deleteDocQuery);
        $stmt->bind_param("s", $seekerEmail);
        $stmt->execute();

        // Clear the Seaman's Passport details from the job_seeker table
        $clearJobSeekerQuery = "UPDATE job_seeker SET passport_no = NULL, passport_country = NULL, passport_issued = NULL, passport_valid = NULL WHERE email = ?";
        $stmt = $conn->prepare($clearJobSeekerQuery);
        $stmt->bind_param("s", $seekerEmail);

        if ($stmt->execute()) {
            header("Location: ../seafarer-documents.php?type=success&message=Seaman's Passport deleted successfully.");
            exit;
        } else {
            header("Location: ../seafarer-documents.php?type=error&message=Failed to delete Seaman's Passport.");
            exit;
        }
    }

    // Get the input values
    $country = $_POST['editPassCountry'] ?? '';
    $seamanNumber = $_POST['editPassportID'] ?? '';
    $fromDate = $_POST['editPassFromDate'] ?? '';
    $toDate = $_POST['editPassToDate'] ?? '';

    // Validate required fields
    if (empty($country) || empty($seamanNumber) || empty($fromDate) || empty($toDate)) {
        header("Location: ../seafarer-documents.php?type=error&message=All fields are required.");
        exit;
    }

    // Handle the file upload
    if (isset($_FILES['editPassDocumentUpload']) && $_FILES['editPassDocumentUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/Passport/"; // Directory to store uploaded files
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        $fileExtension = pathinfo($_FILES['editPassDocumentUpload']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension; // Generate a unique file name
        $filePath = $uploadDir . $uniqueFileName;

        // Check file type (optional: restrict to specific types)
        $allowedTypes = ['pdf', 'doc', 'docx'];
        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            if (move_uploaded_file($_FILES['editPassDocumentUpload']['tmp_name'], $filePath)) {
                // Delete the old file if it exists
                $oldFileQuery = "SELECT doc_url FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = 'Seaman Passport'";
                $stmt = $conn->prepare($oldFileQuery);
                $stmt->bind_param("s", $seekerEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $oldFile = $row['doc_url'];
                    if (!empty($oldFile) && file_exists($uploadDir . $oldFile)) {
                        unlink($uploadDir . $oldFile); // Delete the old file
                    }
                }

                // Update the file name in the seaman_documents table
                $updateDocQuery = "UPDATE seaman_documents SET doc_url = ? WHERE seaman_email = ? AND type_of_doc = 'Seaman Passport'";
                $stmt = $conn->prepare($updateDocQuery);
                $stmt->bind_param("ss", $uniqueFileName, $seekerEmail);

                if (!$stmt->execute()) {
                    header("Location: ../seafarer-documents.php?type=error&message=Failed to update document information.");
                    exit;
                }
            } else {
                header("Location: ../seafarer-documents.php?type=error&message=Failed to upload the document.");
                exit;
            }
        } else {
            header("Location: ../seafarer-documents.php?type=error&message=Invalid file type. Only PDF, DOC, and DOCX are allowed.");
            exit;
        }
    }

    // Update the Seaman's Book details in the job_seeker table
    $updateQuery = "UPDATE job_seeker SET passport_no = ?, passport_country = ?, passport_issued = ?, passport_valid = ? WHERE email = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssss", $seamanNumber, $country, $fromDate, $toDate, $seekerEmail);

    if ($stmt->execute()) {
        header("Location: ../seafarer-documents.php?type=success&message=Seaman's Passport updated successfully.");
        exit;
    } else {
        header("Location: ../seafarer-documents.php?type=error&message=Failed to update Seaman's Passport information.");
        exit;
    }
} else {
    header("Location: ../seafarer-documents.php?type=error&message=Invalid request.");
    exit;
}
?>