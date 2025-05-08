<?php
session_start();
// delete_application.php
require_once "../db.php"; // adjust as needed

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['code'])) {
    $code = $_POST['code'];

    // Example query (adjust table/column names to match your DB)
    $stmt = $conn->prepare("DELETE FROM job_applicants WHERE code = ?");
    $stmt->bind_param("s", $code);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "invalid";
}
