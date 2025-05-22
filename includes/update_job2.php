<?php
session_start(); // Start the session

include '../db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $jobCode = $_POST['job_code'] ?? '';
    $jobTitle = $_POST['editJobTitle'] ?? '';
    $rank = $_POST['editRank'] ?? '';
    $contractLength = $_POST['editContractLength'] ?? '';
    $vesselType = $_POST['editVesselType'] ?? '';
    $jobRequirements = $_POST['editJobRequirements'] ?? '';
    $jobDescription = $_POST['editJobDescription'] ?? '';

    // Check if this is a delete request
    if (isset($_POST['delete_job']) && $_POST['delete_job'] === '1' && !empty($_POST['job_code'])) {
        $jobCode = $_POST['job_code'];
        $deleteQuery = "DELETE FROM jobs WHERE code = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("s", $jobCode);

        if ($deleteStmt->execute()) {
            header("Location: ../employer-posting.php?type=success&message=Job deleted successfully.");
        } else {
            header("Location: ../employer-posting.php?type=error&message=Failed to delete job.");
        }
        $deleteStmt->close();
        $conn->close();
        exit;
    }

    // Validate required fields
    if (empty($jobCode) || empty($jobTitle) || empty($rank) || empty($contractLength) || empty($vesselType) || empty($jobRequirements) || empty($jobDescription)) {
        header("Location: ../employer-posting.php?type=error&message=All fields are required.");
        exit;
    }

    // Update the job details in the database
    $query = "UPDATE jobs 
              SET job_title = ?, rank = ?, contract = ?, vessel = ?, requirements = ?, job_description = ? 
              WHERE code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $jobTitle, $rank, $contractLength, $vesselType, $jobRequirements, $jobDescription, $jobCode);

    if ($stmt->execute()) {
        // Redirect back to the dashboard with a success message
        header("Location: ../employer-posting.php?type=success&message=Job updated successfully.");
    } else {
        // Redirect back to the dashboard with an error message
        header("Location: ../employer-posting.php?type=error&message=Failed to update job.");
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect if the request method is not POST
    header("Location: ../employer-posting.php?type=error&message=Invalid request.");
    exit;
}
?>