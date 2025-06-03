<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../db.php'; // Your database configuration

header('Content-Type: application/json');

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

try {
    $sql = "SELECT id, first_name, last_name, seagoing_work, /* other fields */ 
            CONCAT(first_name, ' ', last_name) AS fullname
            FROM job_seeker 
            WHERE first_name LIKE ? 
               OR last_name LIKE ? 
               OR seagoing_work LIKE ?
            ORDER BY last_name, first_name";
    
    $stmt = $conn->prepare($sql);
    $searchTerm = '%'.$term.'%';
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $results = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($results);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}