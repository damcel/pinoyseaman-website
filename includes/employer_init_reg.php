<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieving form data
    $companyName = trim($_POST["company_name"]);
    $companyEmail = trim($_POST["company_email"]);
    $companyPhone = trim($_POST["company_phone"]);
    $companyWebsite = trim($_POST["company_website"]);
    $companyPassword = trim($_POST["company_password"]);
    $memberType = "FREE";

    // Generate unique ID
    function generateID($length) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    $newid = generateID(8);
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $newpassword = md5($password); 

    function generateRNDID($plength)
    {
        // Characters to choose from: uppercase, lowercase, and digits
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        mt_srand(microtime(true) * 1000000); // Randomize seed
        $pwd = '';

        // Generate random ID
        for ($i = 0; $i < $plength; $i++) {
            $key = mt_rand(0, strlen($chars) - 1);
            $pwd .= $chars[$key];
        }

        return $pwd;
    }

    $rnd_id = generateRNDID(8);

    try {
        require_once "../db.php";

        // Check if email already exists
        $checkQuery = "SELECT COUNT(*) FROM employer WHERE email = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $companyEmail);
        $checkStmt->execute();
        $checkStmt->bind_result($recordExists);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($recordExists > 0) {
            header("Location: ../alert.php?type=error&message=This email is already registered.");
            exit;
        }

        // Insert data into the database
        $query = "INSERT INTO employer (id, company, email, phone, website, password, secret, member_type, company_code, date, date_registered) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $newid, $companyName, $companyEmail, $companyPhone, $companyWebsite, $newpassword, $companyPassword, $memberType, $rnd_id);
        $stmt->execute();
        $stmt->close();

        // Insert action into the action table
        $actionQuery = "INSERT INTO action (date, action, company, time) VALUES (CURDATE(), 'Employer Initial Registration', ?, CURTIME())";
        $actionStmt = $conn->prepare($actionQuery);
        $actionStmt->bind_param("s", $companyEmail);
        $actionStmt->execute();
        $actionStmt->close();

        $pdo = null;
        $stmt = null;

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            
            $mail->isMail();

            // Sender and recipient settings
            $mail->setFrom('noreply@pinoyseaman.com', 'PinoySeaman');
            $mail->addAddress('admin@pinoyseaman.com');
            $mail->Subject = 'New Employer Registration';
            $mail->Body = "
                <p>A new Employer has registered on PinoySeaman:</p>
                <p>Company Name : $companyName</p>
                <p>Email : $companyEmail</p>
                <p>Phone : $companyPhone</p>
                <p>Company ID : $newid</p>
                <p>Website : $companyWebsite</p>";

            $mail->send();

            header("Location: ../index.php?type=success&message=Registration successful!");
            exit;
        } catch (Exception $e) {
            header("Location: ../index.php?type=error&message=Registration successful, but email sending failed: {$mail->ErrorInfo}");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: ../alert.php?type=error&message=Error: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: ../alert.php?type=error&message=Invalid request method.");
    exit;
}
