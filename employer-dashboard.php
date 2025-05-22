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

// Fetch the verification status from the database
$employerEmail = $_SESSION['employer_email'];
$query = "SELECT * FROM employer WHERE email = ?";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
    
     <link rel="stylesheet" href="css/dashboard.css">
    <title>Dashboard</title>
 
</head>
<body>
    
    <!-- // Include your sidebar here -->
    <?php include 'components/employer_aside.php'; ?>
    
    <main class="dashboard-container">
        <?php if (!$isVerified): ?>
            <div class="text-center mt-5">
                <h1>Your account is not yet verified.</h1>
                <p>Please wait for the admin to verify your account. You will be notified via email once your account is verified.</p>
            </div>
        <?php else: ?>
        
        <!-- Include your header here -->
        <?php include 'components/employer_header.php'; ?>

        <section class="top-info-container">
            <div class="job-search-container"> 
                <section class="job-posting-container">

                    <?php
                    // Fetch the company code, email, and member type from the session or database
                    $companyCode = $row['company_code'] ?? ''; // Ensure $row contains the company_code
                    $employerEmail = $_SESSION['employer_email'] ?? '';
                    $memberType = $row['member_type'] ?? '';

                    // Fetch the count of jobs posted by the employer
                    $jobCountQuery = "SELECT COUNT(id) AS job_count FROM jobs WHERE email = ? AND company_code = ?";
                    $jobCountStmt = $conn->prepare($jobCountQuery);
                    $jobCountStmt->bind_param("ss", $employerEmail, $companyCode);
                    $jobCountStmt->execute();
                    $jobCountResult = $jobCountStmt->get_result();
                    $jobCountRow = $jobCountResult->fetch_assoc();
                    $jobCount = $jobCountRow['job_count'] ?? 0;

                    // Determine if the button should be disabled based on member type
                    if (stripos($memberType, 'free') !== false) {
                        // If member type contains "free", limit to 5 job postings
                        $isDisabled = $jobCount >= 3 ? 'disabled' : '';
                    } else {
                        // Otherwise, allow unlimited job postings
                        $isDisabled = '';
                    }
                    ?>

                    <!-- Create New Jobs Card -->
                    <button class="display-card create-job open-modal-btn" type="button" data-bs-toggle="modal" data-bs-target="#jobPostModal" <?php echo $isDisabled; ?>>
                        <div class="icon white"><i class="fa-solid fa-plus"></i></div>
                        <div class="text">
                            <?php if ($isDisabled): ?>
                                <!-- <div class="title white">POST NEW</div>
                                <div class="subtitle white">job</div> -->
                                <p class="text-warning mt-2">You can only post up to 3 jobs. Upgrade to Premium plan for unlimited job postings!</p>
                            <?php else: ?>
                                <div class="title white">POST NEW</div>
                                <div class="subtitle white">job</div>
                            <?php endif; ?>
                        </div>
                    </button>
                
                    <!-- Job Seekdisplay-card -->
                    <a href="applicant-list.php" class="display-card job-seeker">
                        <div class="icon"><i class="fa-solid fa-users"></i></div>
                        <div class="text">
                            <div class="title-seeker">JOB SEEKER</div>
                            <div class="subtitle green">
                                <?php
                                // Fetch the company code from the employer's session
                                $companyCode = $row['company_code'];
                                $company_name = $row['company'];

                                // Query to count job applicants for the company
                                $applicantCountQuery = "SELECT COUNT(code) AS applicant_count FROM job_applicants WHERE company_code = ? AND company = ?";
                                $applicantCountStmt = $conn->prepare($applicantCountQuery);
                                $applicantCountStmt->bind_param("ss", $companyCode, $company_name);
                                $applicantCountStmt->execute();
                                $applicantCountResult = $applicantCountStmt->get_result();
                                $applicantCountRow = $applicantCountResult->fetch_assoc();

                                // Display the count dynamically
                                $applicantCount = $applicantCountRow['applicant_count'] ?? 0;
                                echo $applicantCount . " job applicant" . ($applicantCount != 1 ? "s" : "");
                                ?>
                            </div>
                        </div>
                    </a>
                
                    <!-- Total Jobs Card -->
                    <a href="employer-posting.php" class="display-card total-jobs">
                        <div class="icon"><i class="fa-solid fa-briefcase"></i></div>
                        <div class="text">
                            <div class="title-jobs">TOTAL JOBS</div>
                            <div class="subtitle purple">
                                <?php
                                // Fetch the company code and email from the session
                                $companyCode = $row['company_code'];
                                $employerEmail = $row['email'];

                                // Query to count total jobs for the company
                                $jobCountQuery = "SELECT COUNT(code) AS job_count FROM jobs WHERE email = ? AND company_code = ?";
                                $jobCountStmt = $conn->prepare($jobCountQuery);
                                $jobCountStmt->bind_param("ss", $employerEmail, $companyCode);
                                $jobCountStmt->execute();
                                $jobCountResult = $jobCountStmt->get_result();
                                $jobCountRow = $jobCountResult->fetch_assoc();

                                // Display the count dynamically
                                $jobCount = $jobCountRow['job_count'] ?? 0;
                                echo $jobCount . " job" . ($jobCount != 1 ? "s" : "") . " posted";
                                ?>
                            </div>
                        </div>
                    </a>

                    <!-- Notification Card -->
                    <a href="account-plan.html" class="display-card notification">
                        <div class="icon"><i class="fa-solid fa-address-card"></i></div>
                        <div class="text">
                            <div class="title-notif">Account Plan</div>
                            <div class="subtitle yellow"><?php echo htmlspecialchars($row['member_type']) ?></div>
                        </div>
                    </a>
        
                </section>
            </div>
        </section>

        <section class="job-list-container">
            <div class="job-search-container">                  
                <section class="dashboard-job-container">
                    <div class="display-job-posted">
                        <div class="project-summary">
                          <div class="summary-header">
                            <h3>Job Post Monitoring</h3>
                            <!-- <div class="jobpost-dropdown">
                              <button class="filter-btn" id="dropdownSelect">Recent Post <i class="fa-solid fa-angle-down"></i></button>
                              <ul class="dropdown-menu" id="dropdownList">
                                <li data-type="recent">Recent Job Post</li>
                                <li data-type="all">Job Post List</li>
                              </ul>
                            </div> -->
                          </div>
                    
                          <div class="table-responsive">
                            <table class="summary-table" id="projectTable">
                              <thead class="job-posted-header">
                                <tr>
                                  <th>Job Title</th>
                                  <th>Vessel Type</th>
                                  <th>Date Posted</th>
                                  <th>Applicants</th>
                                </tr>
                              </thead>
                                <tbody id="tableBody">
                                    <?php
                                    // Fetch the company code and email from the session
                                    $companyCode = $row['company_code'];
                                    $employerEmail = $row['email'];

                                    // Query to fetch job posts for the company
                                    $jobPostsQuery = "SELECT jobs.code, job_title, vessel, DATE_FORMAT(date_posted, '%m/%d/%Y') AS formatted_date, 
                                                    (SELECT COUNT(code) FROM job_applicants WHERE job_applicants.job_code = jobs.code) AS applicant_count 
                                                    FROM jobs 
                                                    WHERE email = ? AND company_code = ? 
                                                    ORDER BY date_posted DESC";
                                    $jobPostsStmt = $conn->prepare($jobPostsQuery);
                                    $jobPostsStmt->bind_param("ss", $employerEmail, $companyCode);
                                    $jobPostsStmt->execute();
                                    $jobPostsResult = $jobPostsStmt->get_result();

                                    // Loop through the results and display them in the table
                                    while ($jobPost = $jobPostsResult->fetch_assoc()) {
                                        $jobTitle = htmlspecialchars($jobPost['job_title']);
                                        $vesselType = htmlspecialchars($jobPost['vessel']);
                                        $datePosted = htmlspecialchars($jobPost['formatted_date']);
                                        $applicantCount = htmlspecialchars($jobPost['applicant_count']);
                                        $jobCode = htmlspecialchars($jobPost['code']);

                                        echo "<tr class='job-posted'>
                                                <td data-label='Job Title'>$jobTitle</td>
                                                <td data-label='Vessel Type'>$vesselType</td>
                                                <td data-label='Date Posted'>$datePosted</td>
                                                <td data-label='Applicants'>$applicantCount</td>
                                                <td>
                                                    <button class='profile-side-btn edit-job-btn' data-bs-toggle='modal' data-bs-target='#edit-recent-job' data-job-code='$jobCode'>
                                                        <i class='fa-solid fa-pen-to-square'></i>
                                                    </button>
                                                </td>
                                            </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                          </div>
                        </div>
                      </div>                  
                </section>

                <section class="performance-tracker">
                    <!-- Performance Tracker -->
                    <div class="dashboard-card">
                        <h3>Performance Tracker</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
                        <div class="stats-container">
                        <div class="stat-box">
                            <strong>100</strong>
                            <p>Search appearance<br><span class="positive">+100% last 7 days</span></p>
                        </div>
                        <div class="stat-box">
                            <strong>0</strong>
                            <p>Applicants<br><span class="neutral">0% last 7 days</span></p>
                        </div>
                        <div class="stat-box">
                            <strong>0</strong>
                            <p>Post Impression<br><span class="negative">-100% last 7 days</span></p>
                        </div>
                        <div class="stat-box">
                            <strong>100</strong>
                            <p>Job post visitors<br><span class="positive">+100% last 7 days</span></p>
                        </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="currency-date-aside">
                <aside class="applicant-container">
                    <div class="aside-header">
                        <h2>New Applicant</h2>
                        <span class="position-title">Position</span>
                    </div>

                    <section class="applicant-list">
                        <?php
                        error_reporting(E_ALL);
                        ini_set('display_errors', 1);
                        // Fetch applicants dynamically
                        $applicantQuery = "
                            SELECT 
                                js.user_photo, 
                                js.id AS job_seeker_id, 
                                ja.job_code, 
                                ja.date, 
                                j.job_title,
                                ja.name
                            FROM 
                                job_applicants ja
                            INNER JOIN 
                                job_seeker js ON ja.email = js.email
                            INNER JOIN 
                                jobs j ON ja.job_code = j.code
                            WHERE 
                                ja.company_code = ?
                            ORDER BY 
                                ja.date DESC
                            LIMIT 3"; // Limit to 3 applicants for display

                        $applicantStmt = $conn->prepare($applicantQuery);
                        $applicantStmt->bind_param("s", $companyCode); // Use the company code from the session
                        $applicantStmt->execute();
                        $applicantResult = $applicantStmt->get_result();

                        if ($applicantResult->num_rows > 0) {
                            while ($applicant = $applicantResult->fetch_assoc()) {
                                $userPhoto = !empty($applicant['user_photo']) && file_exists("Uploads/Seaman/User-Photo/" . $applicant['user_photo']) 
                                    ? "Uploads/Seaman/User-Photo/" . htmlspecialchars($applicant['user_photo']) 
                                    : "Uploads/Seaman/User-Photo/Portrait_Placeholder.png"; // Placeholder if no photo
                                $jobSeekerId = htmlspecialchars($applicant['job_seeker_id']);
                                $applicantName = htmlspecialchars($applicant['name']);
                                $jobTitle = htmlspecialchars($applicant['job_title']);
                                $dateApplied = date("F j, Y, g:i a", strtotime($applicant['date'])); // Format the date

                                echo "
                                <div class='applicant-card' data-bs-toggle='modal' data-bs-target='#applicantModal' data-job-seeker-id='$jobSeekerId'>
                                    <div class='info'>
                                        <img src='$userPhoto' alt='Avatar'>
                                        <div class='details'>
                                            <div class='name-time'>
                                                <p class='name'>$applicantName</p>
                                                <p class='time'>$dateApplied</p>
                                            </div>
                                            <span class='position-label'>$jobTitle</span>
                                        </div>
                                    </div>
                                    
                                </div>";
                            }
                        } else {
                            echo "<p class='text-muted'>No new applicants found.</p>";
                        }
                        ?>
                        <div class="view-all">
                            <button type="button">View all<i class="fa-solid fa-angle-down"></i></button>
                        </div>
                    </section>
                </aside>
                                             
                <aside class="calendar-container">
                    <!-- Footer Section -->
                    <footer class="page-footer">
                        <ul class="footer-links">
                        <li>About us</li>
                        <li>Our Story</li>
                        <li>Privacy & Terms</li>
                        <li>Advertise</li>
                        <li>Ad Choices</li>
                        <li>Get in Touch</li>
                        </ul>
                        <div class="footer-branding">
                            <img src="pinoyseaman-logo/alternativeHeaderLogo.png" alt="alternative-logo">
                            <p>
                                pinoyseaman.com © 2025
                            </p>
                        </div>
                    </footer>
                </aside>

            </div>
        </section>
        
        <?php endif; ?>

    </main>

    <?php include_once 'show_user_applicant_modal.php'; ?>

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
                    <input type="hidden" name="delete_job" id="deleteJobInput" value="0">

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


        <!-- JOB POST Modal -->
        <section class="modal fade" id="jobPostModal" tabindex="-1" aria-labelledby="jobPostModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="jobPostModalLabel">Create Job</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="includes/post_job.php" method="POST">

                    <div class="modal-body">
                        <!-- HERE -->
                        
                            <div class="row mb-3">
                                <?php
                                // Fetch job titles dynamically
                                $jobTitles = [];
                                $jobQuery = "SELECT category, job FROM seaman_jobs"; // Replace 'job_table' with your actual table name
                                $jobStmt = $conn->prepare($jobQuery);
                                $jobStmt->execute();
                                $jobResult = $jobStmt->get_result();

                                while ($jobRow = $jobResult->fetch_assoc()) {
                                    $jobTitles[] = htmlspecialchars($jobRow['category'] . " - " . $jobRow['job']);
                                }
                                ?>
                                <div class="col">
                                    <label for="jobPostName" class="form-label">Job Title</label>
                                    <select class="form-select searchable-select" id="jobPostName" name="jobPostName" data-live-search="true">
                                        <option disabled selected>Select job post</option>
                                        <?php foreach ($jobTitles as $jobTitle): ?>
                                            <option data-tokens="<?php echo $jobTitle; ?>" value="<?php echo $jobTitle; ?>"><?php echo $jobTitle; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php
                                // Fetch job titles dynamically
                                $rankTitles = [];
                                $rankQuery = "SELECT rank_name, rank_name_shortcut FROM seaman_ranks"; // Replace 'job_table' with your actual table name
                                $rankStmt = $conn->prepare($rankQuery);
                                $rankStmt->execute();
                                $rankResult = $rankStmt->get_result();

                                while ($rankRow = $rankResult->fetch_assoc()) {
                                    $rankTitles[] = htmlspecialchars($rankRow['rank_name'] . " - " . $rankRow['rank_name_shortcut']);
                                }
                                ?>
                                <div class="col">
                                    <label for="rank" class="form-label">Rank*</label>
                                    <select class="form-select searchable-select" id="rank" name="rank" data-live-search="true">
                                        <option disabled selected>Select job post</option>
                                        <?php foreach ($rankTitles as $rankTitle): ?>
                                            <option data-tokens="<?php echo $rankTitle; ?>" value="<?php echo $rankTitle; ?>"><?php echo $rankTitle; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="contractLength" class="form-label">Contract Length*</label>
                                    <input type="text" class="form-control" id="contractLength" name="contractLength">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="vesselType" class="form-label">Vessel Type*</label>
                                    <select class="form-select" id="vesselType" name="vesselType">
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
                                    <label for="jobRequirements" class="form-label">Job requirements*</label>
                                    <input type="text" class="form-control job-requirements-input" id="jobRequirements" name="jobRequirements">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="jobDescription" class="form-label">Job Description*</label>
                                <textarea class="form-control" id="jobDescription" name="jobDescription" rows="4"></textarea>
                            </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Post Job</button>
                    </div>
                    </form>
                </div>
            </div>
        </section>

        
        <!-- Modal Alert -->
        <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alertModalLabel">Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="alertModalMessage">
                        <!-- Message will be dynamically inserted here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
          
          
    <script src="script/dashboard-drop-jobslist.js"></script>
    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>
    <script src="script/popover.js"></script>
    <script src="script/employer_dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show spinner and fetch job details when edit-job-btn is clicked
            document.querySelectorAll('.edit-job-btn').forEach(function(btn) {
                btn.addEventListener('click', function () {
                    const jobCode = btn.getAttribute('data-job-code');
                    const spinner = document.getElementById('editJobLoadingSpinner');
                    if (spinner) spinner.style.display = 'flex';

                    // Fetch job details via AJAX
                    fetch(`includes/get_job_details.php?job_code=${encodeURIComponent(jobCode)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (spinner) spinner.style.display = "none";

                            if (data.error) {
                                alert(data.error);
                                return;
                            }

                            // Populate the modal with job details
                            const jobTitleSelect = document.getElementById("editJobTitle");
                            const rankSelect = document.getElementById("editRank");
                            const vesselTypeSelect = document.getElementById("editVesselType");

                            // Set values for text/textarea/hidden fields
                            document.getElementById("editContractLength").value = data.contract || "";
                            document.getElementById("editJobRequirements").value = data.requirements || "";
                            document.getElementById("editJobDescription").value = data.job_description || "";
                            document.getElementById("editJobCode").value = data.code || "";

                            // Pre-select dropdown values (reset first)
                            if (jobTitleSelect) {
                                Array.from(jobTitleSelect.options).forEach(option => {
                                    option.selected = (option.value === data.job_title);
                                });
                            }
                            if (rankSelect) {
                                Array.from(rankSelect.options).forEach(option => {
                                    option.selected = (option.value === data.rank);
                                });
                            }
                            if (vesselTypeSelect) {
                                Array.from(vesselTypeSelect.options).forEach(option => {
                                    option.selected = (option.value === data.vessel);
                                });
                            }
                        })
                        .catch(error => {
                            if (spinner) spinner.style.display = "none";
                            alert("Error fetching job details.");
                            console.error("Error fetching job details:", error);
                        });
                });
            });

            // Hide spinner when modal is closed
            var editModal = document.getElementById('edit-recent-job');
            if (editModal) {
                editModal.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('editJobLoadingSpinner').style.display = 'none';
                });
            }
        });
    </script>
    
</body>
</html>