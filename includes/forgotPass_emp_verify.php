<?php
require_once "../db.php";

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../forgot-pws.php?type=error&message=Invalid email address.");
        exit;
    }

    // Check if email exists in employer table
    $stmt = $conn->prepare("SELECT company_code FROM employer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        header("Location: ../forgot-pws.php?type=error&message=Email not found.");
        exit;
    }
    $stmt->close();

    date_default_timezone_set('Asia/Manila');

    // Check if email exists in password_resets table excluding expired OTPs
    $stmt = $conn->prepare("SELECT id FROM password_resets WHERE email = ? AND is_verified = 0 AND expires_at > NOW()");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: ../forgot-pws.php?type=error&message=An OTP has already been sent to this email. Please check your inbox.");
        exit;
    }

    // Generate 6-digit OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Set expiry (2 minutes from now)
    
    $expires_at = date("Y-m-d H:i:s", strtotime("+2 minutes"));
    $created_at = date("Y-m-d H:i:s");
    $is_verified = 0;

    // Remove previous OTPs for this email (optional, for security)
    $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $deleteStmt->bind_param("s", $email);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Insert new OTP
    $insertStmt = $conn->prepare("INSERT INTO password_resets (email, otp, expires_at, is_verified, created_at) VALUES (?, ?, ?, ?, ?)");
    $insertStmt->bind_param("sssis", $email, $otp, $expires_at, $is_verified, $created_at);
    $insertStmt->execute();
    $insertStmt->close();

    // Send OTP via Brevo API
    $apiKey = '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'; // Replace with your Brevo API key

    $brevoData = [
        "sender" => [
            "name" => "PinoySeaman",
            "email" => "noreply@pinoyseaman.com"
        ],
        "to" => [
            [
                "email" => $email
            ]
        ],
        "subject" => "Your PinoySeaman Password Reset OTP",
        "htmlContent" => "
            <p>Hello,</p>
            <p>Your OTP for password reset is: <strong>$otp</strong></p>
            <p>This code will expire in 2 minutes.</p>
            <br>
            <p>If you did not request this, please ignore this email.</p>
            <p>PinoySeaman Team</p>
        ",
        "textContent" => "Your OTP for password reset is: $otp\nThis code will expire in 2 minutes.\nIf you did not request this, please ignore this email."
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($brevoData));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        header("Location: ../forgot-pws.php?type=success&message=OTP sent to your email. Please check your inbox.");
        exit;
    } else {
        header("Location: ../forgot-pws.php?type=error&message=Failed to send OTP. Please try again later.");
        exit;
    }
} else {
    header("Location: ../forgot-pws.php?type=error&message=Invalid request.");
    exit;
}