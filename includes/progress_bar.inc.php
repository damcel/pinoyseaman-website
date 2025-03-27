<?php
header('Content-Type: application/json');
session_start();

// Database connection (modify as needed)
$conn = new mysqli("localhost", "root", "", "your_database");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Define required fields
$fields = [
    'middlename', 'phone_number', 'gender', 'education', 'passport',
    'seamans_book', 'visa', 'certificate', 'training', 'seaman_experience',
    'non_seaman_experience', 'competency', 'merits_rewards'
];

$total_fields = count($fields);
$completed_fields = 0;
$missing_fields = [];

// Fetch user data securely
$sql = "SELECT " . implode(", ", $fields) . " FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if ($user_data) {
    foreach ($fields as $field) {
        if (empty($user_data[$field])) {
            $missing_fields[] = $field;
        } else {
            $completed_fields++;
        }
    }
}

$progress = ($total_fields > 0) ? ($completed_fields / $total_fields) * 100 : 0;

// Return JSON response
echo json_encode([
    "progress" => round($progress, 2),
    "missing_fields" => $missing_fields
]);

$stmt->close();
$conn->close();

