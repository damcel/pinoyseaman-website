<?php
session_start();

include '../db.php';

if (isset($_GET['job_code'])) {
    $jobCode = $_GET['job_code'];

    // Fetch job details from the database
    $query = "SELECT code, job_title, rank, contract, vessel, requirements, job_description FROM jobs WHERE code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $jobCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $jobDetails = $result->fetch_assoc();
        echo json_encode($jobDetails);
    } else {
        echo json_encode(["error" => "Job not found"]);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>