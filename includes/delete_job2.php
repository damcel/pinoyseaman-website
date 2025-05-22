<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job2']) && $_POST['delete_job2'] == '1' && !empty($_POST['job_code'])) {
    $jobCode = $_POST['job_code'];

    $deleteQuery = "DELETE FROM jobs WHERE code = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("s", $jobCode);

    if ($stmt->execute()) {
        header("Location: ../employer-posting.php?type=success&message=Job deleted successfully.");
        exit;
    } else {
        header("Location: ../employer-posting.php?type=error&message=Failed to delete job.");
        exit;
    }
} else {
    header("Location: ../employer-posting.php?type=error&message=Invalid request.");
    exit;
}
?>