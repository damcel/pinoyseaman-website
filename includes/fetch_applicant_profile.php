<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';

$id = $_GET['id'] ?? '';
$response = [];

if ($id) {
    $stmt = $conn->prepare("SELECT first_name, 
                                   middle_name, 
                                   last_name, 
                                   user_photo,
                                   address,
                                   gender,
                                   rank,
                                   birthday,
                                   marital_status,
                                   nationality,
                                   religion,
                                   english_level,
                                   email,
                                   cellphone,
                                   seagoing_work,
                                   non_seagoing_work
                                   FROM job_seeker 
                                   WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $response = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($response);