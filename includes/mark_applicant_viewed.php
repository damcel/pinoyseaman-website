<?php
include '../db.php';

$id = $_POST['id'] ?? '';
$job_code = $_POST['job_code'] ?? '';

if ($id && $job_code) {
    // Get email from job_seeker
    $stmt = $conn->prepare("SELECT email FROM job_seeker WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($email);
    if ($stmt->fetch() && $email) {
        $stmt->close();
        // Update mark in job_applicants
        $update = $conn->prepare("UPDATE job_applicants SET mark = 'Viewed' WHERE email = ? AND job_code = ?");
        $update->bind_param("ss", $email, $job_code);
        $update->execute();
        $update->close();
    } else {
        $stmt->close();
    }
}
http_response_code(204); // No content