<?php
session_start();
require_once "../db.php"; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to add experience.");
    exit;
}

// Get the logged-in user's email and ID from the session
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
    // Retrieve and sanitize the seagoing experience notes
    $landBasedExp = isset($_POST['landBasedExp']) ? filter_var(trim($_POST['landBasedExp']), FILTER_SANITIZE_STRING) : null;

    // Handle file upload
    $fileUploaded = false;
    if (isset($_FILES['landdocumentUpload']) && $_FILES['landdocumentUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/Land-Based-Exp/"; // Directory to store uploaded files
        $fileExtension = pathinfo($_FILES['landdocumentUpload']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . "_" . time() . "." . $fileExtension; // Generate a unique file name
        $filePath = $uploadDir . $uniqueFileName;

        // Check file type
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (in_array($_FILES['landdocumentUpload']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['landdocumentUpload']['tmp_name'], $filePath)) {
                // Save the file information to the database
                $typeOfDoc = "Land-Based Experience File";
                $insertQuery = "INSERT INTO seaman_documents (seaman_email, seaman_id, type_of_doc, doc_url) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ssss", $seekerEmail, $seamanId, $typeOfDoc, $uniqueFileName);

                if ($stmt->execute()) {
                    $fileUploaded = true;
                } else {
                    header("Location: ../userprofile.php?type=error&message=Failed to save file information to the database.");
                    exit;
                }
            } else {
                header("Location: ../userprofile.php?type=error&message=Failed to upload file.");
                exit;
            }
        } else {
            header("Location: ../userprofile.php?type=error&message=Invalid file type. Only PDF and Word documents are allowed.");
            exit;
        }
    }

    // Update the seagoing experience notes in the job_seeker table
    if ($landBasedExp) {
        $updateQuery = "UPDATE job_seeker SET non_seagoing_work = ? WHERE email = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ss", $landBasedExp, $seekerEmail);

        if ($stmt->execute()) {
            if ($fileUploaded) {
                header("Location: ../userprofile.php?type=success&message=Seagoing experience and file uploaded successfully.");
            } else {
                header("Location: ../userprofile.php?type=success&message=Seagoing experience updated successfully.");
            }
            exit;
        } else {
            header("Location: ../userprofile.php?type=error&message=Failed to update seagoing experience.");
            exit;
        }
    } elseif ($fileUploaded) {
        header("Location: ../userprofile.php?type=success&message=Seagoing experience file uploaded successfully.");
        exit;
    } else {
        header("Location: ../userprofile.php?type=error&message=No data to save.");
        exit;
    }
} else {
    // Redirect if the request method is not POST
    header("Location: ../userprofile.php");
    exit;
}
?>