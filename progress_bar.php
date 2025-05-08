<?php
session_start();
require_once 'db.php'; // adjust to your DB connection

// Replace with the actual session variable for job_seeker ID
$job_seeker_email = $_SESSION['seeker_id'];  

// Fetch all columns for the job seeker
$sql = "SELECT * FROM job_seeker WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $job_seeker_email);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$totalFields = count($data);
$emptyFields = [];
$filledCount = 0;

// Define optional or system fields that should NOT count toward progress (e.g., IDs, timestamps)
$excludedFields = ['id', 'password', 'online', 'mark', 'code', 'date', 'children', 'language', 'fax', 'city', 'educ_training', 'prefer_job', 'view', 'phone', 'status', 'certificates'];  // Adjust as needed

foreach ($data as $field => $value) {
    if (in_array($field, $excludedFields)) {
        $totalFields--; // Don't count excluded fields
        continue;
    }

    if (is_null($value) || trim($value) === '') {
        $emptyFields[] = $field;
    } else {
        $filledCount++;
    }
}

$progress = ($totalFields > 0) ? ($filledCount / $totalFields) * 100 : 0;

echo json_encode([
    "progress" => $progress,
    "missing_fields" => $emptyFields
]);
?>