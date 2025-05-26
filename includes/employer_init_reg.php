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
    $companyPoea = trim($_POST["poea_num"]);
    $companyPassword = trim($_POST["company_password"]);
    $memberType = "FREE";

    // Generate unique ID
    function generateID($length) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    $newid = generateID(8);
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $newpassword = md5($companyPassword); 

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
            header("Location: ../employer-login-signup.php?type=error&message=This email is already registered.");
            exit;
        }

        // Insert data into the database
        $query = "INSERT INTO employer (id, company, email, phone, website, password, secret, member_type, company_code, fax, date, date_registered) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssss", $newid, $companyName, $companyEmail, $companyPhone, $companyWebsite, $newpassword, $companyPassword, $memberType, $rnd_id, $companyPoea);
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

        // Send email using Brevo API
        $apiKey = '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'; // Replace with your actual API key

        $emailData = [
            "sender" => [
                "name" => "PinoySeaman",
                "email" => "noreply@pinoyseaman.com"
            ],
            "to" => [
                [
                    "email" => "admin@pinoyseaman.com",
                    "name" => "Admin"
                ]
            ],
            "subject" => "New Employer Registration",
            "htmlContent" => "
                <html>
                    <body>
                        <p>A new Employer has registered on <strong>PinoySeaman</strong>:</p>
                        <ul>
                            <li><strong>Company Name:</strong> $companyName</li>
                            <li><strong>Email:</strong> $companyEmail</li>
                            <li><strong>Phone:</strong> $companyPhone</li>
                            <li><strong>Company ID:</strong> $newid</li>
                            <li><strong>Website:</strong> $companyWebsite</li>
                        </ul>
                    </body>
                </html>"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "accept: application/json",
            "api-key: $apiKey",
            "content-type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 201) {
            header("Location: ../employer-login-signup.php?type=success&message=Registration successful! We are now reviewing your registration. Please wait for admin approval.");
            exit;
        } else {
            header("Location: ../employer-login-signup.php?type=error&message=Registration successful, but email sending failed.");
            exit;
        }

    } catch (PDOException $e) {
        header("Location: ../employer-login-signup.php?type=error&message=Error: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: ../employer-login-signup.php?type=error&message=Invalid request method.");
    exit;
}
?>