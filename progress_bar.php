<?php
error_reporting(E_ALL);
header('Content-Type: application/json');

session_start();
require_once 'db.php'; // adjust to your DB connection

// Replace with the actual session variable for job_seeker ID
$job_seeker_email = $_SESSION['seeker_id'];  

// Fetch all columns for the job seeker
$sql = "SELECT * FROM job_seeker WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $job_seeker_email);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$totalFields = count($data);
$emptyFields = [];
$filledCount = 0;

// Define optional or system fields that should NOT count toward progress (e.g., IDs, timestamps)
$excludedFields = ['id', 'password', 'online', 'mark', 'code', 'date', 'children', 'language', 'fax', 'city', 'educ_training', 'prefer_job', 'view', 'phone', 'status', 'certificates', 'verification'];  // Adjust as needed

foreach ($data as $field => $value) {
    if (in_array($field, $excludedFields)) {
        $totalFields--; // Don't count excluded fields
        continue;
    }

    if (is_null($value) || trim($value) === '') {
        $emptyFields[] = $field;
    } else {
        $filledCount++;
    }
}

// Check if there is at least one education record in seaman_educ
$educSql = "SELECT COUNT(*) as educ_count FROM seaman_educ WHERE email = ?";
$educStmt = $conn->prepare($educSql);
$educStmt->bind_param("s", $job_seeker_email);
$educStmt->execute();
$educResult = $educStmt->get_result();
$educRow = $educResult->fetch_assoc();
$hasEducation = ($educRow['educ_count'] > 0);

// Treat all education as one field: "Education Information"
$totalFields++;
if ($hasEducation) {
    $filledCount++;
} else {
    $emptyFields[] = "Education Information";
}

// Seafaring Experience: check seagoing_work in job_seeker and all seaman_documents (type_of_doc = 'Seagoing Experience File')
$totalFields++;
$hasSeagoingWork = !empty($data['seagoing_work']) && trim($data['seagoing_work']) !== '';

$seadocSql = "SELECT COUNT(*) as doc_count FROM seaman_documents WHERE type_of_doc = 'Seagoing Experience File' AND seaman_email = ?";
$seadocStmt = $conn->prepare($seadocSql);
$seadocStmt->bind_param("s", $job_seeker_email);
$seadocStmt->execute();
$seadocResult = $seadocStmt->get_result();
$seadocRow = $seadocResult->fetch_assoc();
$hasSeagoingDoc = ($seadocRow['doc_count'] > 0);

if ($hasSeagoingWork || $hasSeagoingDoc) {
    $filledCount++;
} else {
    $emptyFields[] = "Seafaring Experience";
}

// Non-Seafaring Experience: check seagoing_work in job_seeker and all seaman_documents (type_of_doc = 'Land-Based Experience File')
$totalFields++;
$hasNonSeagoingWork = !empty($data['non_seagoing_work']) && trim($data['non_seagoing_work']) !== '';

$nonseadocSql = "SELECT COUNT(*) as doc_count FROM seaman_documents WHERE type_of_doc = 'Land-Based Experience File' AND seaman_email = ?";
$nonseadocStmt = $conn->prepare($nonseadocSql);
$nonseadocStmt->bind_param("s", $job_seeker_email);
$nonseadocStmt->execute();
$nonseadocResult = $nonseadocStmt->get_result();
$nonseadocRow = $nonseadocResult->fetch_assoc();
$nonhasSeagoingDoc = ($nonseadocRow['doc_count'] > 0);

if ($hasNonSeagoingWork || $nonhasSeagoingDoc) {
    $filledCount++;
} else {
    $emptyFields[] = "Land-Based Experience";
}

// Passport: check passport in job_seeker and all seaman_documents (type_of_doc = 'Seaman Passport')
$totalFields++;
$hasPassport = !empty($data['passport_country']) && trim($data['passport_country']) !== ''
                    && !empty($data['passport_no']) && trim($data['passport_no']) !== ''
                    && !empty($data['passport_issued']) && trim($data['passport_issued']) !== ''
                    && !empty($data['passport_valid']) && trim($data['passport_valid']) !== '';

$passportSql = "SELECT COUNT(*) as doc_count FROM seaman_documents WHERE type_of_doc = 'Seaman Passport' AND seaman_email = ?";
$passportStmt = $conn->prepare($passportSql);
$passportStmt->bind_param("s", $job_seeker_email);
$passportStmt->execute();
$passportResult = $passportStmt->get_result();
$passportRow = $passportResult->fetch_assoc();
$passportHas = ($passportRow['doc_count'] > 0);

if ($hasPassport || $passportHas) {
    $filledCount++;
} else {
    $emptyFields[] = "Seaman Passport";
}

// Seaman's book: check Seaman's book in job_seeker and all seaman_documents (type_of_doc = 'Seaman Book')
$totalFields++;
$hasSbook = !empty($data['sbook_country']) && trim($data['sbook_country']) !== ''
                    && !empty($data['sbook_no']) && trim($data['sbook_no']) !== ''
                    && !empty($data['sbook_issued']) && trim($data['sbook_issued']) !== ''
                    && !empty($data['sbook_valid']) && trim($data['sbook_valid']) !== '';

$sbookSql = "SELECT COUNT(*) as doc_count FROM seaman_documents WHERE type_of_doc = 'Seaman Book' AND seaman_email = ?";
$sbookStmt = $conn->prepare($sbookSql);
$sbookStmt->bind_param("s", $job_seeker_email);
$sbookStmt->execute();
$sbookResult = $sbookStmt->get_result();
$sbookRow = $sbookResult->fetch_assoc();
$sbookHas = ($sbookRow['doc_count'] > 0);

if ($hasSbook || $sbookHas) {
    $filledCount++;
} else {
    $emptyFields[] = "Seaman's Book";
}

// Check if there is at least one visa record in seaman_educ
$visaSql = "SELECT COUNT(*) as visa_count FROM seaman_visa_docs WHERE seaman_email = ?";
$visaStmt = $conn->prepare($visaSql);
$visaStmt->bind_param("s", $job_seeker_email);
$visaStmt->execute();
$visaResult = $visaStmt->get_result();
$visaRow = $visaResult->fetch_assoc();
$hasVisa = ($visaRow['visa_count'] > 0);

// Treat all visa as one field: "Visa Information"
$totalFields++;
if ($hasVisa) {
    $filledCount++;
} else {
    $emptyFields[] = "Visa Information";
}

// Check if there is at least one certificate record in seaman_educ
$certSql = "SELECT COUNT(*) as cert_count FROM seaman_certificates WHERE seaman_email = ?";
$certStmt = $conn->prepare($certSql);
$certStmt->bind_param("s", $job_seeker_email);
$certStmt->execute();
$certResult = $certStmt->get_result();
$certRow = $certResult->fetch_assoc();
$hasCert = ($certRow['cert_count'] > 0);

// Treat all education as one field: "Education Information"
$totalFields++;
if ($hasCert) {
    $filledCount++;
} else {
    $emptyFields[] = "Certificate Information";
}

// Competence: check Competence in job_seeker and all seaman_documents (type_of_doc = 'Seaman Book')
$totalFields++;
$hasCompetence = !empty($data['competence']) && trim($data['competence']) !== '';

$competenceSql = "SELECT COUNT(*) as doc_count FROM seaman_documents WHERE type_of_doc = 'Competence Document' AND seaman_email = ?";
$competenceStmt = $conn->prepare($competenceSql);
$competenceStmt->bind_param("s", $job_seeker_email);
$competenceStmt->execute();
$competenceResult = $competenceStmt->get_result();
$competenceRow = $competenceResult->fetch_assoc();
$competenceHas = ($competenceRow['doc_count'] > 0);

if ($hasCompetence || $competenceHas) {
    $filledCount++;
} else {
    $emptyFields[] = "Competence Information";
}

// Merits: check Merits in job_seeker and all seaman_documents (type_of_doc = 'Merits Document')
$totalFields++;
$hasMerits = !empty($data['merits']) && trim($data['merits']) !== '';

$meritsSql = "SELECT COUNT(*) as doc_count FROM seaman_documents WHERE type_of_doc = 'Merits Document' AND seaman_email = ?";
$meritsStmt = $conn->prepare($meritsSql);
$meritsStmt->bind_param("s", $job_seeker_email);
$meritsStmt->execute();
$meritsResult = $meritsStmt->get_result();
$meritsRow = $meritsResult->fetch_assoc();
$meritsHas = ($meritsRow['doc_count'] > 0);

if ($hasMerits || $meritsHas) {
    $filledCount++;
} else {
    $emptyFields[] = "Merits Information";
}

$progress = ($totalFields > 0) ? ($filledCount / $totalFields) * 100 : 0;

echo json_encode([
    "progress" => $progress,
    "missing_fields" => $emptyFields
]);
?>