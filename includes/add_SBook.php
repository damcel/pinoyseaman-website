<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to add a Seaman's Book.");
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
    $country = $_POST['country'] ?? '';
    $seamanNumber = $_POST['seamanNumber'] ?? '';
    $fromDate = $_POST['fromDate'] ?? '';
    $toDate = $_POST['toDate'] ?? '';

    // Validate required fields
    if (empty($country) || empty($seamanNumber) || empty($fromDate) || empty($toDate)) {
        header("Location: ../seafarer-documents.php?type=error&message=All fields are required.");
        exit;
    }

    // Handle the file upload
    if (isset($_FILES['documentUpload']) && $_FILES['documentUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/SBook/"; // Directory to store uploaded files
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
        }

        $fileExtension = pathinfo($_FILES['documentUpload']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension; // Generate a unique file name
        $filePath = $uploadDir . $uniqueFileName;

        // Check file type (optional: restrict to specific types)
        $allowedTypes = ['pdf', 'doc', 'docx'];
        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            if (move_uploaded_file($_FILES['documentUpload']['tmp_name'], $filePath)) {
                // Save the file name to the seaman_documents table
                $docType = "Seaman Book";
                $insertDocQuery = "INSERT INTO seaman_documents (seaman_email, seaman_id, type_of_doc, doc_url) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insertDocQuery);
                $stmt->bind_param("ssss", $seekerEmail, $seamanId, $docType, $uniqueFileName);

                if (!$stmt->execute()) {
                    header("Location: ../seafarer-documents.php?type=error&message=Failed to save document information.");
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

    // Save the input data to the job_seeker table
    $updateQuery = "UPDATE job_seeker SET sbook_no = ?, sbook_country = ?, sbook_issued = ?, sbook_valid = ? WHERE email = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssss", $seamanNumber, $country, $fromDate, $toDate, $seekerEmail);

    if ($stmt->execute()) {
        header("Location: ../seafarer-documents.php?type=success&message=Seaman's Book added successfully.");
        exit;
    } else {
        header("Location: ../seafarer-documents.php?type=error&message=Failed to save Seaman's Book information.");
        exit;
    }
} else {
    header("Location: ../seafarer-documents.php?type=error&message=Invalid request.");
    exit;
}
?>