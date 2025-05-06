<?php
session_start();
require_once "../db.php";

// Check if user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in first.");
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

if ($result->num_rows === 0) {
    header("Location: ../userprofile.php?type=error&message=User not found.");
    exit;
}

$row = $result->fetch_assoc();
$seamanId = $row['id'];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $certTypeId = $_POST['cert_type'] ?? '';
    $certNumber = $_POST['certNumber'] ?? '';
    $certCountry = $_POST['certCountry'] ?? '';
    $startDate = $_POST['certfromDate'] ?? '';
    $endDate = isset($_POST['unlimitedCheckboxAdd']) ? NULL : ($_POST['certtoDateAdd'] ?? NULL);
    
    // Validate required fields
    if (empty($certTypeId) || empty($certNumber) || empty($certCountry) || empty($startDate)) {
        header("Location: ../competency-certificate.php?type=error&message=Please fill all required fields.");
        exit;
    }

    // Handle file upload
    $uniqueFileName = null;
    $filePath = null;
    if (isset($_FILES['certUpload']) && $_FILES['certUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/Certificate/";
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("Failed to create directory: " . $uploadDir);
                header("Location: ../competency-certificate.php?type=error&message=Failed to create upload directory.");
                exit;
            }
        }

        // Validate file type
        $allowedTypes = ['pdf', 'doc', 'docx'];
        $originalName = $_FILES['certUpload']['name'];
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedTypes)) {
            header("Location: ../competency-certificate.php?type=error&message=Invalid file type. Only PDF, DOC, and DOCX are allowed.");
            exit;
        }

        // Generate unique filename with original extension
        $uniqueFileName = 'cert_' . $seamanId . '_' . uniqid() . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $uniqueFileName;

        // Move uploaded file
        if (!move_uploaded_file($_FILES['certUpload']['tmp_name'], $filePath)) {
            error_log("Failed to move uploaded file to: " . $filePath);
            header("Location: ../competency-certificate.php?type=error&message=Failed to upload document.");
            exit;
        }
    }

    try {
        // Insert certificate into database
        $insertQuery = "INSERT INTO seaman_certificates 
                        (seaman_email, seaman_id, cert_type_id, cert_number, country, start_date, end_date, file_path, date_added) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $bindResult = $stmt->bind_param(
            "ssisssss",
            $seekerEmail,
            $seamanId,
            $certTypeId,
            $certNumber,
            $certCountry,
            $startDate,
            $endDate,
            $uniqueFileName
        );
        
        if (!$bindResult) {
            throw new Exception("Bind failed: " . $stmt->error);
        }

        $executeResult = $stmt->execute();
        if (!$executeResult) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        header("Location: ../competency-certificate.php?type=success&message=Certificate added successfully!");
        exit;
    } catch (Exception $e) {
        // Delete uploaded file if an exception occurred
        if ($uniqueFileName && $filePath && file_exists($filePath)) {
            unlink($filePath);
        }
        
        error_log("Database Error: " . $e->getMessage());
        
        // More detailed error message for debugging
        $errorMessage = "An error occurred while adding the certificate. ";
        $errorMessage .= "Error: " . $e->getMessage();
        
        header("Location: ../competency-certificate.php?type=error&message=" . urlencode($errorMessage));
        exit;
    }
} else {
    header("Location: ../competency-certificate.php?type=error&message=Invalid request method.");
    exit;
}
?>