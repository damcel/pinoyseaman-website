<?php
require_once "../db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $otp = trim($_POST['otp'] ?? '');

    // Validate input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^\d{6}$/', $otp)) {
        header("Location: ../forgot-pws.php?type=error&message=Invalid input.");
        exit;
    }

    date_default_timezone_set('Asia/Manila');

    // Check OTP in password_resets table
    $stmt = $conn->prepare("SELECT id, expires_at, is_verified FROM password_resets WHERE email = ? AND otp = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        header("Location: ../forgot-pws.php?type=error&message=Invalid OTP code.");
        exit;
    }

    $stmt->bind_result($reset_id, $expires_at, $is_verified);
    $stmt->fetch();
    $stmt->close();

    // Check if OTP is already used
    if ($is_verified) {
        header("Location: ../forgot-pws.php?type=error&message=OTP already used.");
        exit;
    }

    // Check if OTP is expired
    if (strtotime($expires_at) < time()) {
        header("Location: ../forgot-pws.php?type=error&message=OTP expired. Please request a new code.");
        exit;
    }

    // Mark OTP as verified
    $updateStmt = $conn->prepare("UPDATE password_resets SET is_verified = 1 WHERE id = ?");
    $updateStmt->bind_param("i", $reset_id);
    $updateStmt->execute();
    $updateStmt->close();

    // Redirect to reset password page, passing email and reset_id as GET parameters
    header("Location: ../reset-password.php?email=" . urlencode($email) . "&reset_id=" . urlencode($reset_id));
    exit;
} else {
    header("Location: ../forgot-pws.php?type=error&message=Invalid request.");
    exit;
}