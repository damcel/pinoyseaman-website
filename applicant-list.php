<?php
session_name("employerSession");
session_start(); // Start the session

// Set session timeout duration (e.g., 15 minutes = 900 seconds)
$timeoutDuration = 1800; // 30 minutes

// Check if the session timeout is set
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    // If the session has timed out, destroy the session and redirect to login
    session_unset();
    session_destroy();
    header("Location: employer-login-signup.php?type=error&message=Session timed out. Please log in again.");
    exit;
}

// Update the last activity time
$_SESSION['LAST_ACTIVITY'] = time();

// Prevent caching of the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if the user is logged in
if (!isset($_SESSION['employer_email'])) {
    // Redirect to the login page with an error message
    header("Location: employer-login-signup.php?type=error&message=You must log in to access this page.");
    exit;
}

// Include the database connection file
include 'db.php';

// Check if there is a success or error message
if (isset($_GET['type']) && isset($_GET['message'])) {
    $alertType = ($_GET['type'] === 'success') ? 'success' : 'error';
    $message = htmlspecialchars($_GET['message']); // Sanitize the message
    echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
            const alertModalMessage = document.getElementById('alertModalMessage');
            const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
            alertModalMessage.textContent = '$message';
            alertModal.show();
        });
    </script>";
}

// Fetch the verification status and job details from the database
$employerEmail = $_SESSION['employer_email'];
$query = "
    SELECT 
        e.email, 
        e.logo, 
        e.verify, 
        e.company_code,
        e.member_type,
        j.vessel, 
        j.code, 
        j.job_title, 
        j.job_description, 
        j.requirements, 
        j.contract 
    FROM 
        employer e
    LEFT JOIN 
        jobs j ON e.email = j.email 
    WHERE 
        e.email = ? 
        AND (j.expiry IS NULL OR j.expiry > NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employerEmail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$verifyStatus = $row['verify'] ?? 'n'; // Default to 'n' if not found
$isVerified = ($verifyStatus === 'y');

$logoFilename = $row['logo'] ?? '';
$logoPath = !empty($logoFilename) && file_exists("company-logo/" . $logoFilename) 
    ? "company-logo/" . htmlspecialchars($logoFilename) 
    : "company-logo/Logo-placeholder.png";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Applicant List</title>
    <style>
        .modal-overlay {
            background-color: transparent !important; /* <- This removes the gray */
        }
    </style>
</head>
<body>

    <?php include 'components/employer_aside.php'; ?>
    
    <main class="dashboard-container">
        <section class="header-container">
            
            <div class="dropdown-container">
              <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
              <div class="dropdown" id="dropdownMenu">
                <a href="employer-settings.php" class="prfl">Settings</a>
                <a href="includes/logout_employer.php">Logout</a>
              </div>
            </div>
        </section>

        <section class="job-list-container">
            <div class="job-search-container">
                <section class="job-posting-container">
                     <!-- Header Tabs -->
                    <div class="post-header">
                        <h3>Applicant List</h3>
                        <p class="subtext">Looking for the right seafarer? Browse and choose from the list below!</p>
                        <ul class="tab-list">
                        <li><a href="employer-posting.php">Published</a></li>
                        <li class="active-tab"><a href="applicant-list.php">Applicant</a></li>
                        </ul>
                        <div class="search-applicant">
                            <select class="search-select" id="jobSelect">
                              <option value="">Select job</option>
                                <?php
                                    // Fetch job titles and codes for this employer, ordered by latest date_posted
                                    $jobTitleQuery = "SELECT code, job_title, date_posted FROM jobs WHERE email = ? AND (expiry IS NULL OR expiry > NOW()) ORDER BY date_posted DESC";
                                    $jobTitleStmt = $conn->prepare($jobTitleQuery);
                                    $jobTitleStmt->bind_param("s", $employerEmail);
                                    $jobTitleStmt->execute();
                                    $jobTitleResult = $jobTitleStmt->get_result();
                                    $jobTitlesSeen = [];
                                    $firstJobCode = '';
                                    $first = true;
                                    while ($jobRow = $jobTitleResult->fetch_assoc()) {
                                        // Avoid duplicate job titles
                                        if (!in_array($jobRow['job_title'], $jobTitlesSeen)) {
                                            $jobTitlesSeen[] = $jobRow['job_title'];
                                            $selected = '';
                                            if ($first) {
                                                $firstJobCode = $jobRow['code'];
                                                $selected = 'selected';
                                                $first = false;
                                            }
                                            echo '<option value="' . htmlspecialchars($jobRow['code']) . "\" $selected>" . htmlspecialchars($jobRow['job_title']) . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                          
                            <select class="search-select" id="rankSelect">
                              <option value="">Select Seaman's Rank</option>
                                <?php
                                    // Fetch seaman ranks dynamically
                                    $rankQuery = "SELECT rank_name_shortcut, rank_name FROM seaman_ranks ORDER BY rank_name ASC";
                                    $rankStmt = $conn->prepare($rankQuery);
                                    $rankStmt->execute();
                                    $rankResult = $rankStmt->get_result();
                                    while ($rankRow = $rankResult->fetch_assoc()) {
                                        $rankShortcut = htmlspecialchars($rankRow['rank_name_shortcut']);
                                        $rankName = htmlspecialchars($rankRow['rank_name']);
                                        echo "<option value=\"$rankShortcut\">$rankName ($rankShortcut)</option>";
                                    }
                                ?>
                            </select>
                          
                            <button class="dashboard-src-btn" id="searchApplicantsBtn"><i class="fa-solid fa-magnifying-glass"></i></button>
                          </div>
                    </div>
                </section>  

                <?php
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                // After your job select, get the default selected job code
                $defaultJobCode = $firstJobCode ?? '';

                // Fetch applicants for the default selected job
                $applicants = [];
                if ($defaultJobCode) {
                    $applicantQuery = "SELECT js.id, 
                                    js.first_name, 
                                    js.middle_name, 
                                    js.last_name, 
                                    js.address, 
                                    js.gender, 
                                    js.birthday, 
                                    ja.name, 
                                    ja.email, 
                                    js.rank, 
                                    js.passport_valid, 
                                    js.sbook_valid, 
                                    js.user_photo,
                                    js.marital_status,
                                    js.nationality,
                                    js.religion,
                                    js.english_level,
                                    js.email,
                                    js.cellphone,
                                    js.seagoing_work,
                                    js.non_seagoing_work
                                    FROM job_applicants ja
                                    INNER JOIN jobs j ON ja.job_code = j.code
                                    LEFT JOIN job_seeker js ON ja.email = js.email
                                    WHERE j.code = ?";
                    $applicantStmt = $conn->prepare($applicantQuery);
                    $applicantStmt->bind_param("s", $defaultJobCode);
                    $applicantStmt->execute();
                    $applicantResult = $applicantStmt->get_result();
                    while ($applicantRow = $applicantResult->fetch_assoc()) {
                        // Format passport_valid and sbook_valid to "F j, Y" (e.g., January 1, 2002)
                        $row['passport_valid'] = (!empty($row['passport_valid']) && $row['passport_valid'] !== '0000-00-00')
                            ? date("F j, Y", strtotime($row['passport_valid']))
                            : '';
                        $row['sbook_valid'] = (!empty($row['sbook_valid']) && $row['sbook_valid'] !== '0000-00-00')
                            ? date("F j, Y", strtotime($row['sbook_valid']))
                            : '';
                        // Build full name: First M. Last
                        $middleInitial = $applicantRow['middle_name'] ? strtoupper(substr($applicantRow['middle_name'], 0, 1)) . '.' : '';
                        $applicantRow['full_name'] = trim($applicantRow['first_name'] . ' ' . $middleInitial . ' ' . $applicantRow['last_name']);
                        $applicants[] = $applicantRow;
                    }
                }
                ?>
                
                <section class="applicant-profile-container" >
                    <section class="applicant-card-list" id="applicantCardList">
                        
                    </section>
                </section>             
            </div>

            <?php
            $employerEmail = $_SESSION['employer_email'];
            $query = "
                SELECT 
                    j.vessel, 
                    j.code, 
                    j.job_title, 
                    j.job_description, 
                    j.requirements, 
                    j.contract,
                    j.date_posted,
                    j.expiry
                FROM 
                    jobs j
                WHERE 
                    j.email = ? 
                    AND (j.expiry IS NULL OR j.expiry > NOW())
                ORDER BY j.date_posted DESC
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $employerEmail);
            $stmt->execute();
            $result = $stmt->get_result();

            $jobs = [];
            while ($row = $result->fetch_assoc()) {
                $jobs[] = $row;
            }
            ?>
            
            <div class="currency-date-aside">
                <?php include 'components/employer_jobPosting_aside.php'; ?>

            </div>
        </section>

    </main>

    <?php include 'components/show_applicant_profile.php'; ?>

    <!-- Edit recent job Modal -->
    <section class="modal fade" id="edit-recent-job" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">  
        <!-- update / Delete recent job Modal -->
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="position:relative;">
                <!-- Loading Spinner for Edit Modal -->
                <div id="editJobLoadingSpinner" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:10;justify-content:center;align-items:center;">
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="modal-header justify-content-between align-items-center">
                    <h1 class="modal-title fs-5" id="jobPostModalLabel">Edit Job</h1>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn text-danger p-0" title="Delete Job" id="deleteJobBtn">
                            <i class="fa-solid fa-trash-can fs-5"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <form action="includes/update_job.php" method="POST">
                    <input type="hidden" id="editJobCode" name="job_code">

                <div class="modal-body">
                    <!-- HERE -->
                    
                        <div class="row mb-3">
                            <div class="col">
                                <label for="editJobTitle" class="form-label">Job Title</label>
                                <select class="form-select searchable-select" id="editJobTitle" name="editJobTitle">
                                    <option disabled selected>Select job post</option>
                                    <?php
                                    // Fetch job titles dynamically
                                    $jobQuery = "SELECT category, job FROM seaman_jobs";
                                    $jobStmt = $conn->prepare($jobQuery);
                                    $jobStmt->execute();
                                    $jobResult = $jobStmt->get_result();

                                    while ($jobRow = $jobResult->fetch_assoc()) {
                                        $jobTitle = htmlspecialchars($jobRow['category'] . " - " . $jobRow['job']);
                                        echo "<option value=\"$jobTitle\">$jobTitle</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="editRank" class="form-label">Rank*</label>
                                <select class="form-select searchable-select" id="editRank" name="editRank">
                                    <option disabled selected>Select rank</option>
                                    <?php
                                    // Fetch rank titles dynamically
                                    $rankQuery = "SELECT rank_name, rank_name_shortcut FROM seaman_ranks";
                                    $rankStmt = $conn->prepare($rankQuery);
                                    $rankStmt->execute();
                                    $rankResult = $rankStmt->get_result();

                                    while ($rankRow = $rankResult->fetch_assoc()) {
                                        $rankTitle = htmlspecialchars($rankRow['rank_name'] . " - " . $rankRow['rank_name_shortcut']);
                                        echo "<option value=\"$rankTitle\">$rankTitle</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="editContractLength" class="form-label">Contract Length*</label>
                                <input type="text" class="form-control" id="editContractLength" name="editContractLength">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="editVesselType" class="form-label">Vessel type*</label>
                                <select class="form-select" id="editVesselType" name="editVesselType">
                                    <option disabled selected>Select vessel type</option>
                                    <?php
                                    // Fetch vessel types dynamically
                                    $vesselTypesQuery = "SELECT type FROM vessel_types";
                                    $vesselTypesStmt = $conn->prepare($vesselTypesQuery);
                                    $vesselTypesStmt->execute();
                                    $vesselTypesResult = $vesselTypesStmt->get_result();

                                    while ($vesselTypeRow = $vesselTypesResult->fetch_assoc()) {
                                        $vesselType = htmlspecialchars($vesselTypeRow['type']);
                                        echo "<option value=\"$vesselType\">$vesselType</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="editJobRequirements" class="form-label">Job requirements*</label>
                                <input type="text" class="form-control job-requirements-input" id="editJobRequirements" name="editJobRequirements">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editJobDescription" class="form-label">Job Description*</label>
                            <textarea class="form-control" id="editJobDescription" name="editJobDescription" rows="4"></textarea>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

                </form>

            </div>
        </div>
    </section>
    
    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
     <!-- jQuery (very important, load it FIRST) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="script/popover.js"></script>
    <script src="script/employer_job_posting.js"></script>
    <script src="script/applicant-list.js"></script>

    <!-- Your own script to activate Select2 -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const selects = document.querySelectorAll('.search-select');
        selects.forEach(select => {
            $(select).select2({
            placeholder: 'Select an option',
            allowClear: true,
            width: 'resolve'
            });
        });
        });
    </script>
    
</body>
</html>