<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "dbh.inc.php";

// require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // $recaptcha_token = $_POST['recaptcha_token'] ?? '';
    // $recaptcha_secret = '6LcsKjIrAAAAAKLKhlob34wEVJxNK2nf9fZ8Fqam'; // Replace with your Secret Key

    // $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    // $response = file_get_contents($verify_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_token);
    // $response_data = json_decode($response, true);
    
    // // Optional: You may log score or action
    // if (!$response_data['success'] || $response_data['score'] < 0.5 || $response_data['action'] !== 'submit') {
    //     $_SESSION['errors'] = ['reCAPTCHA validation failed. Please try again.'];
    //     header("Location: ../index.php");
    //     exit;
    // }

    
    $prefer_job = trim($_POST["prefer_job"]);
    $first_name = trim($_POST["first_name"]);
    $middle_name = trim($_POST["middle_name"] ?? '');
    $last_name = trim($_POST["last_name"]);
    $birthday = trim($_POST["birthday"]);
    $gender = trim($_POST["gender"]);
    $email = strtolower(trim($_POST["email"]));
    $phone = trim($_POST["phone"]);
    $currentDate = date("Y-m-d");
    $currentTime = date("H:i:s");
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // Check if already registered from this IP today
    // $checkIPStmt = $pdo->prepare("SELECT COUNT(*) FROM action WHERE ip = ? AND date = ?");
    // $checkIPStmt->execute([$ipAddress, $currentDate]);
    // if ($checkIPStmt->fetchColumn() > 0) {
    //     $_SESSION['errors'] = ['Only one registration is allowed per IP per day.'];
    //     header("Location: ../index.php");
    //     exit;
    // }

    // Normalize names
    $first_name = ucwords(strtolower($first_name));
    $middle_name = ucwords(strtolower($middle_name));
    $last_name = ucwords(strtolower($last_name));

    // Validation
    $errors = [];
    if (empty($prefer_job) || empty($first_name) || empty($last_name) || empty($birthday) ||
        empty($gender) || empty($email) || empty($phone)) {
        $errors[] = "Please fill in all required fields.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!DateTime::createFromFormat('Y-m-d', $birthday)) {
        $errors[] = "Invalid date format. Use YYYY-MM-DD.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../quick-application.php");
        exit;
    }

    function generateID($length) {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    $newid = generateID(8);
    $newpassword = md5($newid); // Use bcrypt in production
    

    try {
        // Check if email exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM job_seeker WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetchColumn() > 0) {
            $_SESSION['errors'] = ['This email is already registered.'];
            header("Location: ../quick-application.php");
            exit;
        }

        // Insert job seeker
        $stmt = $pdo->prepare("INSERT INTO job_seeker (prefer_job, first_name, last_name, birthday, gender, email, cellphone, id, password, date, view)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$prefer_job, $first_name, $last_name, $birthday, $gender, $email, $phone, $newid, $newpassword, $currentDate, 'y']);

        // Log action
        $logStmt = $pdo->prepare("INSERT INTO action (date, action, seaman, ip, time) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([$currentDate, "Seaman Quick Apply Registration", $email, $ipAddress, $currentTime]);

        // === BREVO API SENDING ===
        $apiKey = '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'; // Replace with your Brevo v3 API Key
        $senderEmail = 'noreply@pinoyseaman.com';
        $senderName = 'PinoySeaman';

        // Send confirmation to user
        $userBody = "
            <p>Hello $first_name,</p>
            <p>Welcome to PinoySeaman! Your account has been created successfully.</p>
            <p>Email: <strong>$email</strong></p>
            <p>Temporary Password: <strong>$newid</strong></p>
            <p>Please login and change your password immediately.</p>
            <br>
            <p><em>Note: We do not charge any fees for registration or job applications.</em></p>
        ";

        sendBrevoEmail($senderEmail, $senderName, $email, "$first_name $last_name", 'Welcome to PinoySeaman!', $userBody, $apiKey);

        // Notify admin
        $adminBody = "
            <p>New job seeker has registered:</p>
            <p><strong>Name:</strong> $first_name $middle_name $last_name</p>
            <p><strong>Preferred Job:</strong> $prefer_job</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Gender:</strong> $gender</p>
            <p><strong>Birthday:</strong> $birthday</p>
            <p><strong>Date Registered:</strong> $currentDate</p>
        ";

        sendBrevoEmail($senderEmail, $senderName, 'admin@pinoyseaman.com', 'Admin', 'New Job Seeker Registration', $adminBody, $apiKey);

        $_SESSION['success'] = 'Registration successful! You may now login.';
        header("Location: ../quick-application.php");
        exit;

    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
    exit;
}

// === Function to send email using Brevo ===
function sendBrevoEmail($fromEmail, $fromName, $toEmail, $toName, $subject, $htmlBody, $apiKey) {
    $data = [
        'sender' => [ 'name' => $fromName, 'email' => $fromEmail ],
        'to' => [[ 'email' => $toEmail, 'name' => $toName ]],
        'subject' => $subject,
        'htmlContent' => $htmlBody,
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . $apiKey,
        'content-type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("Brevo cURL Error: " . curl_error($ch));
    }

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($statusCode >= 400) {
        error_log("Brevo API Error ($statusCode): $response");
    }
    curl_close($ch);
}