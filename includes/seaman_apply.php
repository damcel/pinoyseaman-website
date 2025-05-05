<?php
session_start();
require_once "../db.php"; // Include the database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    header("Location: ../user-login-signup.php?type=error&message=You must log in to apply for a job.");
    exit;
}

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data from the AJAX submission
    $jobCode = $_POST['job_code'] ?? '';
    $companyCode = $_POST['company_code'] ?? '';
    $jobTitle = $_POST['job_title'] ?? '';
    $companyName = $_POST['company_name'] ?? '';
    $companyEmail = $_POST['company_email'] ?? '';
    
    // Get user data from session
    $seekerEmail = $_SESSION['seeker_id'];

    // Fetch user details from database to get name components
    $userQuery = "SELECT first_name, middle_name, last_name, password FROM job_seeker WHERE email = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("s", $seekerEmail);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }
    
    $userData = $userResult->fetch_assoc();
    $firstName = $userData['first_name'] ?? '';
    $middleName = $userData['middle_name'] ?? '';
    $lastName = $userData['last_name'] ?? '';
    $seekerPassword = $userData['password'] ?? '';
    
    // Combine name components into full name
    $fullName = trim("$firstName " . ($middleName ? "$middleName " : "") . $lastName);
    
    // Validate required fields
    if (empty($companyCode) || empty($jobTitle) || empty($seekerEmail) || empty($fullName) || empty($seekerPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Handle the CV upload
    $uploadedFileName = null;
    $filePath = null;
    if (isset($_FILES['cvUpload']) && $_FILES['cvUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../Uploads/Seaman/CV-Resume/";
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['cvUpload']['name'], PATHINFO_EXTENSION));
        $uniqueFileName = uniqid('cv_') . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $uniqueFileName;

        // Validate file type and size
        $allowedTypes = ['pdf', 'doc', 'docx', 'odt', 'txt'];
        $maxFileSize = 3 * 1024 * 1024; // 3MB

        if (!in_array($fileExtension, $allowedTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only PDF, DOC, DOCX, ODT, and TXT are allowed.']);
            exit;
        }

        if ($_FILES['cvUpload']['size'] > $maxFileSize) {
            echo json_encode(['status' => 'error', 'message' => 'File size exceeds 3MB limit.']);
            exit;
        }

        // Move the uploaded file
        if (move_uploaded_file($_FILES['cvUpload']['tmp_name'], $filePath)) {
            $uploadedFileName = $uniqueFileName;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload the CV.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'CV upload is required.']);
        exit;
    }

    // Insert application into database
    try {
        $currentTime = date("H:i:s");
        $currentDate = date("Y-m-d");
        
        $insertQuery = "INSERT INTO job_applicants 
                        (company_code, job_hiring, email, time, date, password, name, company, seaman_cv, job_code) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param(
            "ssssssssss",
            $companyCode,
            $jobTitle,
            $seekerEmail,
            $currentTime,
            $currentDate,
            $seekerPassword,
            $fullName,
            $companyName,
            $uploadedFileName,
            $jobCode
        );

        if ($stmt->execute()) {
            // Send email notification without SMTP authentication
            $mail = new PHPMailer(true);
            
            try {
                // Basic mail settings (no SMTP)
                $mail->isMail(); // Use PHP's mail() function
                
                // Recipients
                $mail->setFrom('noreply@pinoyseaman.com', 'PinoySeaman Job Portal');
                $mail->addAddress($companyEmail, $companyName);
                
                // Attach CV if available
                if ($filePath) {
                    $mail->addAttachment($filePath, $fullName.'_CV.'.$fileExtension);
                }

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'New Application for '.$jobTitle.' - PinoySeaman';
                $mail->Body = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 5px;">
                    <div style="background-color: #0056b3; padding: 20px; text-align: center; color: white;">
                        <h2>New Job Application Received</h2>
                        <p>PinoySeaman Job Portal Notification</p>
                    </div>
                    
                    <div style="padding: 20px;">
                        <p>Dear '.$companyName.',</p>
                        
                        <p>We are pleased to inform you that a new candidate has applied for your job posting:</p>
                        
                        <div style="background-color: #f5f5f5; padding: 15px; border-left: 4px solid #0056b3; margin: 15px 0;">
                            <h3 style="margin-top: 0; color: #0056b3;">'.$jobTitle.'</h3>
                            <p><strong>Job Code:</strong> '.$jobCode.'</p>
                        </div>
                        
                        <h3 style="color: #0056b3;">Applicant Details</h3>
                        <p><strong>Name:</strong> '.$fullName.'</p>
                        <p><strong>Email:</strong> '.$seekerEmail.'</p>
                        <p><strong>Application Date:</strong> '.$currentDate.' at '.$currentTime.'</p>
                        
                        <p style="margin-top: 20px;">The applicant\'s CV is attached to this email for your review.</p>
                        
                        <div style="margin: 25px 0; text-align: center;">
                            <a href="https://pinoyseaman.com/company/dashboard" 
                            style="background-color: #0056b3; color: white; padding: 10px 20px; 
                                    text-decoration: none; border-radius: 5px; display: inline-block;">
                                View Application in Dashboard
                            </a>
                        </div>
                        
                        <p style="border-top: 1px solid #eee; padding-top: 15px; margin-top: 20px;">
                            <strong>Note:</strong> This is an automated notification. Please do not reply to this email.
                        </p>
                    </div>
                    
                    <div style="background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                        <p>© '.date('Y').' PinoySeaman. All rights reserved.</p>
                        <p>If you have any questions, please contact <a href="mailto:support@pinoyseaman.com">support@pinoyseaman.com</a></p>
                    </div>
                </div>';

                $mail->AltBody = "New Application Notification\n\n".
                                "Job Title: ".$jobTitle."\n".
                                "Job Code: ".$jobCode."\n\n".
                                "Applicant Details:\n".
                                "Name: ".$fullName."\n".
                                "Email: ".$seekerEmail."\n".
                                "Application Date: ".$currentDate." at ".$currentTime."\n\n".
                                "The applicant's CV is attached to this email.\n\n".
                                "View this application in your dashboard:\n".
                                "https://pinoyseaman.com/company/dashboard\n\n".
                                "Note: This is an automated notification. Please do not reply to this email.\n\n".
                                "© ".date('Y')." PinoySeaman. All rights reserved.";

                $mail->send();
                
                header("Location: ../dashboardjobs.php?type=success&message=Application submitted successfully! The company has been notified.");
                exit;
            } catch (Exception $e) {
                // Email failed but application was saved
                error_log("Mailer Error: ".$mail->ErrorInfo);
                header("Location: ../dashboardjobs.php?type=success&message=Application submitted successfully! (Email notification failed)");
                exit;
            }
        } else {
            header("Location: ../dashboardjobs.php?type=error&message=Database error: " . urlencode($stmt->error));
            exit;
        }
    } catch (Exception $e) {
        header("Location: ../dashboardjobs.php?type=error&message=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: ../dashboardjobs.php?type=error&message=Invalid request method.");
    exit;
}
?>