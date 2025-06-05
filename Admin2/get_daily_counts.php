<?php
include 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Manila');

$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get number of days in month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Initialize array with all dates of the month, default count to 0
$data = [];
for ($d = 1; $d <= $daysInMonth; $d++) {
    $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
    $data[$date] = 0;
}

// Fetch actual counts from database
$sql = "SELECT DATE(date) AS reg_date, COUNT(*) AS count
        FROM job_seeker
        WHERE MONTH(date) = ? AND YEAR(date) = ?
        GROUP BY reg_date";

$stmt = $link->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $reg_date = $row['reg_date'];
    $data[$reg_date] = (int)$row['count'];
}

// Convert associative array to indexed array of objects for JSON
$response = [];
foreach ($data as $date => $count) {
    $response[] = [
        'reg_date' => $date,
        'count' => $count
    ];
}

echo json_encode($response);
