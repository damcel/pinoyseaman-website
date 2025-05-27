<?php
require_once "../db.php";
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
$otp = trim($_POST['otp'] ?? '');

date_default_timezone_set('Asia/Manila');

$valid = false;
if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^\d{6}$/', $otp)) {
    $stmt = $conn->prepare("SELECT id FROM password_resets WHERE email = ? AND otp = ? AND is_verified = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $valid = true;
    $stmt->close();
}

echo json_encode(['valid' => $valid]);