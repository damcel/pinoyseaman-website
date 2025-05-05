<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to edit a visa.");
    exit;
}

// Get the logged-in user's email
$seekerEmail = $_SESSION['seeker_id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the delete button was clicked
    if (isset($_POST['delete']) && $_POST['delete'] == 1) {
        $visaId = $_POST['visaId'] ?? '';

        // Validate visa ID
        if (empty($visaId)) {
            header("Location: ../seafarer-documents.php?type=error&message=Visa ID is required for deletion.");
            exit;
        }

        // Fetch the file URL from the database
        $fetchFileQuery = "SELECT visa_url FROM seaman_visa_docs WHERE id = ? AND seaman_email = ?";
        $stmt = $conn->prepare($fetchFileQuery);
        $stmt->bind_param("is", $visaId, $seekerEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $fileData = $result->fetch_assoc();
            $filePath = "../Uploads/Seaman/Visa/" . $fileData['visa_url'];

            // Delete the file if it exists
            if (!empty($fileData['visa_url']) && file_exists($filePath)) {
                unlink($filePath); // Delete the file
            }
        }

        // Delete the visa record from the database
        $deleteVisaQuery = "DELETE FROM seaman_visa_docs WHERE id = ? AND seaman_email = ?";
        $stmt = $conn->prepare($deleteVisaQuery);
        $stmt->bind_param("is", $visaId, $seekerEmail);

        if ($stmt->execute()) {
            header("Location: ../seafarer-documents.php?type=success&message=Visa deleted successfully.");
            exit;
        } else {
            header("Location: ../seafarer-documents.php?type=error&message=Failed to delete visa.");
            exit;
        }
    }

    // Get the input values
    $visaId = $_POST['visaId'] ?? '';
    $visaType = $_POST['visaType'] ?? '';
    $visaNumber = $_POST['visaNumber'] ?? '';
    $visaFromDate = $_POST['visaFromDate'] ?? '';
    $visaToDate = $_POST['visaToDate'] ?? '';

    // Validate required fields
    if (empty($visaId) || empty($visaType) || empty($visaNumber) || empty($visaFromDate) || empty($visaToDate)) {
        header("Location: ../seafarer-documents.php?type=error&message=All fields are required.");
        exit;
    }

    // Fetch the visa_type_id from the seaman_visa_list table
    $visaTypeIdQuery = "SELECT id FROM seaman_visa_list WHERE visa_type = ?";
    $stmt = $conn->prepare($visaTypeIdQuery);
    $stmt->bind_param("s", $visaType);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../seafarer-documents.php?type=error&message=Invalid Visa Type.");
        exit;
    }

    $visaTypeId = $result->fetch_assoc()['id'];

    // Handle the file upload
    $uploadedFileName = null;
    if (isset($_FILES['visaDocumentUpload']) && $_FILES['visaDocumentUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/Visa/"; // Directory to store uploaded files
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        $fileExtension = pathinfo($_FILES['visaDocumentUpload']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension; // Generate a unique file name
        $filePath = $uploadDir . $uniqueFileName;

        // Check file type (optional: restrict to specific types)
        $allowedTypes = ['pdf', 'doc', 'docx'];
        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            if (move_uploaded_file($_FILES['visaDocumentUpload']['tmp_name'], $filePath)) {
                $uploadedFileName = $uniqueFileName; // Save the file name for database insertion
            } else {
                header("Location: ../seafarer-documents.php?type=error&message=Failed to upload the document.");
                exit;
            }
        } else {
            header("Location: ../seafarer-documents.php?type=error&message=Invalid file type. Only PDF, DOC, and DOCX are allowed.");
            exit;
        }
    }

    // Update the visa details in the seaman_visa_docs table
    $updateVisaQuery = "UPDATE seaman_visa_docs 
                        SET visa_type_id = ?, visa_type_name = ?, visa_no = ?, visa_issued = ?, visa_valid = ?, visa_url = IFNULL(?, visa_url) 
                        WHERE id = ? AND seaman_email = ?";
    $stmt = $conn->prepare($updateVisaQuery);
    $stmt->bind_param("ssssssis", $visaTypeId, $visaType, $visaNumber, $visaFromDate, $visaToDate, $uploadedFileName, $visaId, $seekerEmail);

    if ($stmt->execute()) {
        header("Location: ../seafarer-documents.php?type=success&message=Visa updated successfully.");
        exit;
    } else {
        header("Location: ../seafarer-documents.php?type=error&message=Failed to update visa information.");
        exit;
    }
} else {
    header("Location: ../seafarer-documents.php?type=error&message=Invalid request.");
    exit;
}
?>