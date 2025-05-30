<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Get the raw POST data
$dataJson = file_get_contents('php://input');
$data = json_decode($dataJson, true);

if (!$data || !isset($data['data'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format']);
    exit;
}

$row = $data['data'];
$conn = new mysqli("localhost", "root", "", "pinoysea_pinoyseaman");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

// Prepare data
$first_name = $conn->real_escape_string($row['Name'] ?? '');
$birthday_raw = strtotime($row['Date of Birth'] ?? '');
$birthday = $birthday_raw ? date("F j, Y", $birthday_raw) : '';
$age = (int)($row['Age'] ?? 0);
$phone = $conn->real_escape_string($row['Phone'] ?? '');
$email = $conn->real_escape_string($row['E-Mail'] ?? '');
$lastEmp_position = $conn->real_escape_string($row['Rank / Position'] ?? '');
$lastEmp_company = $conn->real_escape_string($row['Last Company'] ?? '');
$lastEmp_vessel = $conn->real_escape_string($row['Vessel Experience'] ?? '');

// Check if email exists
$checkEmail = $conn->query("SELECT id FROM job_seeker WHERE email = '$email'");
if ($checkEmail && $checkEmail->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Duplicate email']);
    $conn->close();
    exit;
}

// Generate unique ID function
function generateUniqueID($conn, $length = 8) {
    do {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $id = substr(str_shuffle($chars), 0, $length);
        $checkID = $conn->query("SELECT id FROM job_seeker WHERE id = '$id'");
    } while ($checkID && $checkID->num_rows > 0);
    return $id;
}

$newid = generateUniqueID($conn);

$sql = "INSERT INTO job_seeker 
        (first_name, birthday, age, phone, cellphone, email, lastEmp_position, lastEmp_company, lastEmp_vessel, id, date) 
        VALUES 
        ('$first_name', '$birthday', $age, '$phone', '$phone', '$email', '$lastEmp_position', '$lastEmp_company', '$lastEmp_vessel', '$newid', NOW())";

if ($conn->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Record inserted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert error: ' . $conn->error]);
}

$conn->close();
?>