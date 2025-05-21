<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_name("employerSession");
session_start();
include '../db.php';

$company_code = $_SESSION['company_code'] ?? '';

$id = $_GET['id'] ?? '';
$response = [];

if ($id) {
    $stmt = $conn->prepare("SELECT js.id,
                                   js.first_name, 
                                   js.middle_name, 
                                   js.last_name, 
                                   js.user_photo,
                                   js.address,
                                   js.gender,
                                   js.rank,
                                   js.birthday,
                                   js.marital_status,
                                   js.nationality,
                                   js.religion,
                                   js.english_level,
                                   js.email,
                                   js.cellphone,
                                   js.seagoing_work,
                                   js.non_seagoing_work,
                                   js.passport_no,
                                   js.passport_country,
                                   js.passport_issued,
                                   js.passport_valid,
                                   js.sbook_no,
                                   js.sbook_country,
                                   js.sbook_issued,
                                   js.sbook_valid,
                                   sd1.doc_url AS seagoing_doc_url,
                                   sd2.doc_url AS landbased_doc_url,
                                   sd3.doc_url AS seaman_passport_url,
                                   sd4.doc_url AS sbook_url,
                                   svd.visa_no,
                                   svd.visa_type_name,
                                   svd.visa_issued,
                                   svd.visa_valid,
                                   svd.visa_url,
                                   sc.cert_type_id,
                                   sc.cert_number,
                                   sc.country,
                                   sc.start_date,
                                   sc.end_date,
                                   sc.file_path,
                                   ct.type AS cert_type_name

                            FROM job_seeker js
                            LEFT JOIN seaman_documents sd1 ON js.id = sd1.seaman_id AND sd1.type_of_doc = 'Seagoing Experience File'
                            LEFT JOIN seaman_documents sd2 ON js.id = sd2.seaman_id AND sd2.type_of_doc = 'Land-Based Experience File'
                            LEFT JOIN seaman_documents sd3 ON js.id = sd3.seaman_id AND sd3.type_of_doc = 'Seaman Passport'
                            LEFT JOIN seaman_documents sd4 ON js.id = sd4.seaman_id AND sd4.type_of_doc = 'Seaman Book'
                            LEFT JOIN seaman_visa_docs svd ON js.id = svd.seaman_id
                            LEFT JOIN seaman_certificates sc ON js.id = sc.seaman_id
                            LEFT JOIN certificate_types ct ON sc.cert_type_id = ct.id
                            WHERE js.id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $response = $row;

        // Fetch employer's member_type using company_code from session
        $member_type = null;
        if (!empty($company_code)) {
            $empStmt = $conn->prepare("SELECT member_type FROM employer WHERE company_code = ?");
            $empStmt->bind_param("s", $company_code);
            $empStmt->execute();
            $empResult = $empStmt->get_result();
            if ($empRow = $empResult->fetch_assoc()) {
                $member_type = $empRow['member_type'];
            }
        }
        $response['member_type'] = $member_type;

        // Fetch all education rows for this seaman
        $educStmt = $conn->prepare("SELECT school_name, educ_level, field_of_study, from_date, to_date, attachment_url FROM seaman_educ WHERE seaman_id = ?");
        $educStmt->bind_param("s", $id);
        $educStmt->execute();
        $educResult = $educStmt->get_result();
        $educations = [];
        while ($educRow = $educResult->fetch_assoc()) {
            $educations[] = $educRow;
        }
        $response['educations'] = $educations;
        
    }
}

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);