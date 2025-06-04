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

        // Remove URL params after showing modal
        const url = new URL(window.location.href);
        url.searchParams.delete('type');
        url.searchParams.delete('message');
        window.history.replaceState({}, document.title, url.pathname);
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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="Pinoyseaman.ico" type="image/x-icon"> 
    <title>Job Post</title>
</head>
<body>
    
    <!-- // Include your sidebar here -->
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

        <section class="job-list-container">
            <div class="job-search-container">
                <section class="job-posting-container">
                     <!-- Header Tabs -->
                    <div class="post-header">
                        <h3>Post Job</h3>
                        <p class="subtext">What seafaring rank or position are you hiring for? Post it here!</p>
                        <ul class="tab-list">
                        <li class="active-tab"><a href="employer-posting.php">Published</a></li>
                        <li><a href="applicant-list.php">Applicant</a></li>
                        </ul>
                    </div>
                </section>

                <section class="dashboard-job-container">
                    <!-- Call to Action Box -->
                    <div class="cta-card">
                        <div class="cta-text">
                        <h4>Post quickly, hire smarter.</h4>
                        <p>Aplikanteng Seaman, Isang Post Lang</p>
                        <button class="cta-button" data-bs-toggle="modal" data-bs-target="#jobPostModal" <?= $isDisabled; ?>>Post Job</button>
                        <?php if ($isDisabled): ?>
                            <div class="text-warning mt-2" style="font-size:0.95em;">
                                You can only post up to 3 jobs on a Free plan. Upgrade to Premium for unlimited postings.
                            </div>
                        <?php endif; ?>
                        </div>
                        <div class="cta-image">
                        <img src="https://img.icons8.com/color/96/megaphone.png" alt="Megaphone" />
                        </div>
                    </div>
                </section>

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

                
                <section class="dashboard-job-container">
                    <?php if (count($jobs) > 0): ?>
                        <?php foreach ($jobs as $job): ?>
                            <?php
                                $expiryDate = $job['expiry'] ?? null;
                                $jobCode = $job['code'] ?? '';

                                // Determine if expired
                                $isExpired = false;
                                $expiryLabel = '';
                                if ($expiryDate && strtotime($expiryDate) <= time()) {
                                    $isExpired = true;
                                    $expiryLabel = '<span class="badge bg-danger ms-2">Expired</span>';
                                }
                            ?>
                            <article class="job-details-container">
                                <section class="employer-related-job">
                                    <div class="job-card">
                                        <div class="card-left">
                                            <label class="job-title"><?= htmlspecialchars($job['job_title']) ?> <?= $expiryLabel ?></label>
                                            <p class="posting-job-detail"><i class="fas fa-ship"></i> <?= htmlspecialchars($job['vessel']) ?></p>
                                            <p class="posting-job-detail"><i class="fa-solid fa-calendar"></i> <?= htmlspecialchars($job['contract']) ?></p>
                                            <p class="posting-job-detail"><i class="fa-solid fa-file-word"></i> <?= htmlspecialchars($job['requirements']) ?></p>
                                            <label>Description</label>
                                            <p class="job-posting-description"><?= nl2br(htmlspecialchars($job['job_description'])) ?></p>
                                        </div>
                                        <div class="card-right">
                                            <?php if ($isExpired): ?>
                                                <form method="POST" action="includes/delete_job2.php" onsubmit="return confirm('Are you sure you want to delete this job posting?');" style="display:inline;">
                                                    <input type="hidden" name="job_code" value="<?= $jobCode ?>">
                                                    <input type="hidden" name="delete_job2" value="1">
                                                    <button type="submit" class="btn btn-danger job-delete-icon" id="deleteJobBtn2" title="Delete Job">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="job-edit-icon edit-job-btn" aria-label="Edit <?= $jobTitle ?>" data-bs-toggle="modal" data-bs-target="#edit-recent-job" data-job-code="<?= $jobCode ?>">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            <?php endif; ?>
                                            <img src="<?= $logoPath ?>" alt="Company Logo">
                                        </div>
                                    </div>
                                </section>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No active job postings found.</p>
                    <?php endif; ?>
                </section>                                          
            </div>

            <?php include 'components/employer_jobPosting_aside.php'; ?>
            

        </section>

    </main>

    <!-- JOB POST Modal -->
    <section class="modal fade" id="jobPostModal" tabindex="-1" aria-labelledby="jobPostModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="jobPostModalLabel">Create Job</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/post_job2.php" method="POST">

                <div class="modal-body">
                    <!-- HERE -->
                    
                        <div class="row mb-3">
                            <?php
                                // Fetch all jobs with their categories
                                $jobQuery = "SELECT category, job FROM seaman_jobs";
                                $jobStmt = $conn->prepare($jobQuery);
                                $jobStmt->execute();
                                $jobResult = $jobStmt->get_result();

                                $jobsByCategory = [];
                                $jobToCategory = [];
                                while ($jobRow = $jobResult->fetch_assoc()) {
                                    $category = $jobRow['category'];
                                    $job = $jobRow['job'];
                                    $jobsByCategory[$category][] = $job;
                                    $jobToCategory[$job] = $category; // Map job back to category
                                }
                                ?>
                                
                                <div class="col">
                                    <label for="rank" class="form-label">Rank Department</label>
                                    <select class="form-select" id="rank" name="rank">
                                        <option disabled selected>Select Department</option>
                                        <?php foreach (array_keys($jobsByCategory) as $category): ?>
                                            <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col">
                                    <label for="jobPostName" class="form-label">Rank/Position</label>
                                    <select class="form-select" id="jobPostName" name="jobPostName">
                                        <option disabled selected>Select Rank/Position</option>
                                        <?php foreach ($jobToCategory as $job => $category): ?>
                                            <option value="<?= htmlspecialchars($job) ?>"><?= htmlspecialchars($job) ?></option>
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

                <form action="includes/update_job2.php" method="POST">
                    <input type="hidden" id="editJobCode" name="job_code">
                    <input type="hidden" name="delete_job" id="deleteJobInput" value="0">

                <div class="modal-body">
                    <!-- HERE -->
                        <?php
                        // Fetch all jobs with their categories
                        $jobQuery = "SELECT category, job FROM seaman_jobs";
                        $jobStmt = $conn->prepare($jobQuery);
                        $jobStmt->execute();
                        $jobResult = $jobStmt->get_result();

                        $jobsByCategory = [];
                        $jobToCategory = [];
                        while ($jobRow = $jobResult->fetch_assoc()) {
                            $category = $jobRow['category'];
                            $job = $jobRow['job'];
                            $jobsByCategory[$category][] = $job;
                            $jobToCategory[$job] = $category;
                        }
                        ?>
                    
                        <div class="row mb-3">
                            
                            <div class="col">
                                <label for="editRank" class="form-label">Rank Department</label>
                                <select class="form-select" id="editRank" name="editRank">
                                    <option disabled selected>Select department</option>
                                    <?php foreach (array_keys($jobsByCategory) as $category): ?>
                                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col">
                                <label for="editJobTitle" class="form-label">Job Title</label>
                                <select class="form-select" id="editJobTitle" name="editJobTitle">
                                    <option disabled selected>Select job post</option>
                                    <?php foreach ($jobToCategory as $job => $category): ?>
                                        <option value="<?= htmlspecialchars($job) ?>"><?= htmlspecialchars($job) ?></option>
                                    <?php endforeach; ?>
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
    
    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <script src="script/employer_job_posting.js"></script>
    <!-- <script src="script/employer_dashboard.js"></script> -->

    <script>
    const jobsByCategory = <?php echo json_encode($jobsByCategory); ?>;
    const jobToCategory = <?php echo json_encode($jobToCategory); ?>;

    const rankSelect = document.getElementById('rank');
    const jobSelect = document.getElementById('jobPostName');

    // Populate Rank/Position when Rank Department is selected
    rankSelect.addEventListener('change', function () {
        const selectedCategory = this.value;

        // Reset job select
        jobSelect.innerHTML = '<option disabled selected>Select job post</option>';

        if (jobsByCategory[selectedCategory]) {
            jobsByCategory[selectedCategory].forEach(job => {
                const option = document.createElement('option');
                option.value = job;
                option.textContent = job;
                jobSelect.appendChild(option);
            });
        }
    });

    // Select Rank Department when Rank/Position is selected
    jobSelect.addEventListener('change', function () {
        const selectedJob = this.value;
        const category = jobToCategory[selectedJob];

        if (category) {
            rankSelect.value = category;

            // Trigger change event to repopulate job list
            const event = new Event('change');
            rankSelect.dispatchEvent(event);

            // Reselect the job
            jobSelect.value = selectedJob;
        }
    });
</script>

<script>
    const jobsByCategory = <?= json_encode($jobsByCategory) ?>;
    const jobToCategory = <?= json_encode($jobToCategory) ?>;

    const editRankSelect = document.getElementById('editRank');
    const editJobTitleSelect = document.getElementById('editJobTitle');

    editRankSelect.addEventListener('change', function () {
        const selectedCategory = this.value;

        editJobTitleSelect.innerHTML = '<option disabled selected>Select job post</option>';
        if (jobsByCategory[selectedCategory]) {
            jobsByCategory[selectedCategory].forEach(job => {
                const option = document.createElement('option');
                option.value = job;
                option.textContent = job;
                editJobTitleSelect.appendChild(option);
            });
        }
    });

    editJobTitleSelect.addEventListener('change', function () {
        const selectedJob = this.value;
        if (jobToCategory[selectedJob]) {
            editRankSelect.value = jobToCategory[selectedJob];
        }
    });

    // Optional: Populate with current values when modal is shown
    document.getElementById('edit-recent-job').addEventListener('show.bs.modal', function () {
        const selectedJob = editJobTitleSelect.value;
        if (selectedJob && jobToCategory[selectedJob]) {
            editRankSelect.value = jobToCategory[selectedJob];

            // Trigger change to repopulate job list
            editRankSelect.dispatchEvent(new Event('change'));

            // Re-select job if needed
            editJobTitleSelect.value = selectedJob;
        }
    });
</script>
    
</body>
</html>