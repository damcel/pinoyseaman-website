<?php
include '../db.php';

$jobCode = $_GET['job_code'] ?? '';
$rank = $_GET['rank'] ?? '';

$response = [];

if ($jobCode) {
    $sql = "SELECT js. id, ja.name, ja.email, js.rank, js.passport_valid, js.sbook_valid, js.user_photo
            FROM job_applicants ja
            INNER JOIN jobs j ON ja.job_code = j.code
            LEFT JOIN job_seeker js ON ja.email = js.email
            WHERE j.code = ?";
    if ($rank) {
        $sql .= " AND js.rank LIKE ?";
    }
    $stmt = $conn->prepare($sql);
    if ($rank) {
        $likeRank = "%$rank%";
        $stmt->bind_param("ss", $jobCode, $likeRank);
    } else {
        $stmt->bind_param("s", $jobCode);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Format passport_valid and sbook_valid to "F j, Y" (e.g., January 1, 2002)
        $row['passport_valid'] = (!empty($row['passport_valid']) && $row['passport_valid'] !== '0000-00-00')
            ? date("F j, Y", strtotime($row['passport_valid']))
            : '';
        $row['sbook_valid'] = (!empty($row['sbook_valid']) && $row['sbook_valid'] !== '0000-00-00')
            ? date("F j, Y", strtotime($row['sbook_valid']))
            : '';
        // Add photo path
        $photoFile = $row['user_photo'] ?? '';
        $row['photo_path'] = (!empty($photoFile) && file_exists("../Uploads/Seaman/User-Photo/" . $photoFile))
            ? "Uploads/Seaman/User-Photo/" . htmlspecialchars($photoFile)
            : "Uploads/Seaman/User-Photo/Portrait_Placeholder.png";
        $response[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($response);