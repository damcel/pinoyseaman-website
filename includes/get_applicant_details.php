<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db.php';

if (isset($_GET['job_seeker_id'])) {
    $jobSeekerId = $_GET['job_seeker_id'];

    // Fetch applicant details
    $query = "
        SELECT 
            js.id AS job_seeker_id,
            js.user_photo, 
            CONCAT(js.first_name, ' ', js.last_name) AS name, 
            j.job_title, 
            ja.date AS date_applied, 
            js.rank,
            js.cellphone AS applicant_cellphone,
            js.email AS applicant_email,
            js.seagoing_work,
            js.non_seagoing_work
        FROM 
            job_applicants ja
        INNER JOIN 
            job_seeker js ON ja.email = js.email
        INNER JOIN 
            jobs j ON ja.job_code = j.code
        WHERE 
            js.id = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $jobSeekerId);
    if (!$stmt->execute()) {
        echo json_encode(["error" => "SQL execute failed: " . $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $applicant = $result->fetch_assoc();

        // Fetch education details
        $educationQuery = "
            SELECT 
                school_name, 
                educ_level, 
                field_of_study, 
                from_date, 
                to_date, 
                attachment_url
            FROM 
                seaman_educ
            WHERE 
                seaman_id = ?";
        
        $educationStmt = $conn->prepare($educationQuery);
        if (!$educationStmt) {
            echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
            exit;
        }

        $educationStmt->bind_param("s", $jobSeekerId);
        if (!$educationStmt->execute()) {
            echo json_encode(["error" => "SQL execute failed: " . $educationStmt->error]);
            exit;
        }

        $educationResult = $educationStmt->get_result();
        $educationData = [];
        while ($row = $educationResult->fetch_assoc()) {
            $educationData[] = $row;
        }

        // Fetch document details
        $documents = [
            "Seaman Passport" => "passport",
            "Seaman Book" => "seaman_book",
            "Competence Document" => "competence",
            "Merits Document" => "merits"
        ];

        $documentData = [];
        foreach ($documents as $type => $key) {
            $docQuery = "
                SELECT 
                    doc_url
                FROM 
                    seaman_documents
                WHERE 
                    seaman_id = ? AND type_of_doc = ?";
            
            $docStmt = $conn->prepare($docQuery);
            if (!$docStmt) {
                echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
                exit;
            }

            $docStmt->bind_param("ss", $jobSeekerId, $type);
            if (!$docStmt->execute()) {
                echo json_encode(["error" => "SQL execute failed: " . $docStmt->error]);
                exit;
            }

            $docResult = $docStmt->get_result();
            $docRow = $docResult->fetch_assoc();
            $documentData[$key] = $docRow['doc_url'] ?? null;
        }

        // Fetch visa details
        $visaQuery = "
            SELECT 
                visa_url
            FROM 
                seaman_visa_docs
            WHERE 
                seaman_id = ?";
        
        $visaStmt = $conn->prepare($visaQuery);
        if (!$visaStmt) {
            echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
            exit;
        }

        $visaStmt->bind_param("s", $jobSeekerId);
        if (!$visaStmt->execute()) {
            echo json_encode(["error" => "SQL execute failed: " . $visaStmt->error]);
            exit;
        }

        $visaResult = $visaStmt->get_result();
        $visaData = [];
        while ($row = $visaResult->fetch_assoc()) {
            $visaData[] = $row;
        }

        // Fetch certificate details
        $certificateQuery = "
            SELECT 
                file_path
            FROM 
                seaman_certificates
            WHERE 
                seaman_id = ?";
        
        $certificateStmt = $conn->prepare($certificateQuery);
        if (!$certificateStmt) {
            echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
            exit;
        }

        $certificateStmt->bind_param("s", $jobSeekerId);
        if (!$certificateStmt->execute()) {
            echo json_encode(["error" => "SQL execute failed: " . $certificateStmt->error]);
            exit;
        }

        $certificateResult = $certificateStmt->get_result();
        $certificateData = [];
        while ($row = $certificateResult->fetch_assoc()) {
            $certificateData[] = $row['file_path'];
        }

        // Add education, document, visa, and certificate data to the response
        $applicant['education'] = $educationData;
        $applicant['documents'] = $documentData;
        $applicant['visas'] = $visaData;
        $applicant['certificates'] = $certificateData;

        echo json_encode($applicant);
    } else {
        echo json_encode(["error" => "Applicant not found."]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}
?>