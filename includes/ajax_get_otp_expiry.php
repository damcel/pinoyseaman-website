<?php
require_once "../db.php";
header('Content-Type: application/json');


$email = trim($_POST['email'] ?? '');

date_default_timezone_set('Asia/Manila');

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $stmt = $conn->prepare("SELECT expires_at FROM password_resets WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($expires_at);
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'expires_at' => $expires_at]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false]);
}