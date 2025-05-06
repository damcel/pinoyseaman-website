<?php
session_start();
require_once "../db.php";

// Ensure user is authenticated
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to add education.");
    exit;
}

$seaman_email = $_SESSION['seeker_id'];

// Check for the delete action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Delete the certificate
    $deleteQuery = "DELETE FROM seaman_certificates WHERE seaman_email = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("s", $seaman_email);
    if ($stmt->execute()) {
        // Optionally delete the file if it exists
        $fileQuery = "SELECT file_path FROM seaman_certificates WHERE seaman_email = ?";
        $stmtFile = $conn->prepare($fileQuery);
        $stmtFile->bind_param("s", $seaman_email);
        $stmtFile->execute();
        $result = $stmtFile->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $oldFile = $row['file_path'];
            if (!empty($oldFile) && file_exists("../Uploads/Seaman/Certificate/" . $oldFile)) {
                unlink("../Uploads/Seaman/Certificate/" . $oldFile);  // Delete old file from server
            }
        }
        $_SESSION['success'] = "Certificate deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete certificate.";
    }

    $stmt->close();
    $conn->close();

    // Redirect to the certificate page after deletion
    header("Location: ../competency-certificate.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cert_type_id = $_POST['edit_cert_type'];
    $cert_number = $_POST['editcertNumber'];
    $country = $_POST['editcertCountry'];
    $start_date = $_POST['editcertfromDate'];
    $end_date = isset($_POST['editunlimitedCheckboxAdd']) ? null : $_POST['editcerttoDateAdd'];

    // Handle file upload
    $file_path = null;
    $uploadDir = "../Uploads/Seaman/Certificate/";

    if (isset($_FILES['editcertUpload']) && $_FILES['editcertUpload']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['editcertUpload']['name'], PATHINFO_EXTENSION);
        $allowedTypes = ['pdf', 'doc', 'docx'];

        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension;
            $file_path = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($_FILES['editcertUpload']['tmp_name'], $file_path)) {
                // Delete old file
                $oldFileQuery = "SELECT file_path FROM seaman_certificates WHERE seaman_email = ?";
                $stmt = $conn->prepare($oldFileQuery);
                $stmt->bind_param("s", $seaman_email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $oldFile = $row['file_path'];
                    if (!empty($oldFile) && file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $stmt->close();
            } else {
                header("Location: ../seafarer-documents.php?type=error&message=Failed to upload the document.");
                exit;
            }
        } else {
            header("Location: ../seafarer-documents.php?type=error&message=Invalid file type. Only PDF, DOC, and DOCX are allowed.");
            exit;
        }
    }

    // Prepare update query
    if ($file_path) {
        $query = "UPDATE seaman_certificates 
                  SET cert_type_id = ?, cert_number = ?, country = ?, start_date = ?, end_date = ?, file_path = ?
                  WHERE seaman_email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $cert_type_id, $cert_number, $country, $start_date, $end_date, $file_path, $seaman_email);
    } else {
        $query = "UPDATE seaman_certificates 
                  SET cert_type_id = ?, cert_number = ?, country = ?, start_date = ?, end_date = ?
                  WHERE seaman_email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $cert_type_id, $cert_number, $country, $start_date, $end_date, $seaman_email);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Certificate updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update certificate.";
    }

    $stmt->close();
    $conn->close();
}

header("Location: ../competency-certificate.php");
exit();
