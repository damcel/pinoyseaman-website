<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to add a visa.");
    exit;
}

// Get the logged-in user's email
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

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input values
    $visaType = $_POST['visaType'] ?? '';
    $visaNumber = $_POST['visaNumber'] ?? '';
    $visaFromDate = $_POST['visaFromDate'] ?? '';
    $visaToDate = $_POST['visaToDate'] ?? '';

    // Validate required fields
    if (empty($visaType) || empty($visaNumber) || empty($visaFromDate) || empty($visaToDate)) {
        header("Location: ../seafarer-documents.php?type=error&message=All fields are required.");
        exit;
    }

    // Fetch the visa_type_name from the seaman_visa_list table
    $visaTypeNameQuery = "SELECT visa_type FROM seaman_visa_list WHERE id = ?";
    $stmt = $conn->prepare($visaTypeNameQuery);
    $stmt->bind_param("s", $visaType);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $visaTypeName = $row['visa_type'];
    } else {
        header("Location: ../seafarer-documents.php?type=error&message=Invalid Visa Type.");
        exit;
    }

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

    // Insert the visa details into the seaman_visa_docs table
    $insertVisaQuery = "INSERT INTO seaman_visa_docs (seaman_email, seaman_id, visa_type_id, visa_type_name, visa_no, visa_issued, visa_valid, visa_url) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertVisaQuery);
    $stmt->bind_param("ssssssss", $seekerEmail, $seamanId, $visaType, $visaTypeName, $visaNumber, $visaFromDate, $visaToDate, $uploadedFileName);

    if ($stmt->execute()) {
        header("Location: ../seafarer-documents.php?type=success&message=Visa added successfully.");
        exit;
    } else {
        header("Location: ../seafarer-documents.php?type=error&message=Failed to save visa information.");
        exit;
    }
} else {
    header("Location: ../seafarer-documents.php?type=error&message=Invalid request.");
    exit;
}
?>