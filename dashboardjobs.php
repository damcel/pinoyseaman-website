<?php
session_start(); // Start the session

// Set session timeout duration (e.g., 15 minutes = 900 seconds)
$timeoutDuration = 1800; // 30 minutes

// Check if the session timeout is set
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    // If the session has timed out, destroy the session and redirect to login
    session_unset();
    session_destroy();
    header("Location: user-login-signup.php?type=error&message=Session timed out. Please log in again.");
    exit;
}

// Update the last activity time
$_SESSION['LAST_ACTIVITY'] = time();

// Prevent caching of the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if the user is logged in
if (!isset($_SESSION['seeker_id'])) {
    // Redirect to the login page with an error message
    header("Location: user-login-signup.php?type=error&message=You must log in to access this page.");
    exit;
}

// Include the database connection file
include 'db.php';

// Fetch user details from the database
$seekerEmail = $_SESSION['seeker_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Dashboard</title>
</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="sidebar-header">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="pinoyseaman-logo/pinoyseaman-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
                    </a>
                </div>
                <button onclick="toggleSidebar()" id="toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0B1C33">
                        <path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/>
                    </svg>
                </button>
            </div>
            <ul class="ul-links">
              <li class="menu-title">MENU</li>
              <li>
                <a href="dashboardjobs.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-briefcase"></i><span>Jobs</span>
                </a>
              </li>
              <li class="separator">
                <a href="userprofile.php">
                  <!-- SVG Icon -->
                  <i class="fa-regular fa-user"></i><span>Profile</span>
                </a>
              </li>
              
              <li class="menu-title">ANALYTICS</li>
              <li>
                <a href="history.html">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-business-time"></i><span>History</span>
                </a>
              </li>
              <li class="separator">
                <a href="companies.html">
                  <!-- SVG Icon -->
                  <i class="fa-regular fa-building"></i><span>Companies</span>
                </a>
              </li>
                <div id="progress-main-container" class="progress-main-container">
                    <div class="complete-percentage">
                        <p>Complete your profile</p>
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar" id="progress-bar"></div>
                        <p id="progress-text">0% Completed</p>
                        <div class="incomplete-container">
                            <h3>Incomplete Fields:</h3>
                            <ul id="missing-fields"></ul>
                        </div>
                    </div>
                </div>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-container">
        <section class="header-container">
            <div class="saved-ctn">
                <a href="saved.html" class="saved-btn">
                    <i class="fa-solid fa-book-bookmark"></i>
                </a>
            </div>
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn">DP</button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="#" class="prfl">Profile Settings</a>
                    <a href="index.php">Logout</a>
                </div>
            </div>
        </section>

        <section class="job-list-container">
            
            <div class="job-search-container">
            <form action="dashboardjobs.php" method="POST">

                <section class="dashboard-search-container">

                    <select class="search-select" name="job_type">
                        <option value="" <?php echo empty($_POST['job_type']) ? 'selected' : ''; ?>>Select job</option>
                        <?php
                        // Include the database connection file
                        include 'db.php';

                        // Query to fetch jobs from the seaman_jobs table in ascending order
                        $jobQuery = "SELECT DISTINCT job FROM seaman_jobs ORDER BY job ASC";
                        $jobResult = $conn->query($jobQuery);

                        if ($jobResult && $jobResult->num_rows > 0) {
                            while ($row = $jobResult->fetch_assoc()) {
                                $job = htmlspecialchars($row['job']); // Escape special characters
                                $selected = (isset($_POST['job_type']) && $_POST['job_type'] === $job) ? 'selected' : '';
                                echo "<option value=\"$job\" $selected>$job</option>";
                            }
                        } else {
                            echo "<option value=\"\">No jobs available</option>";
                        }
                        ?>
                    </select>

                    <select class="search-select" name="vessel_type">
                        <option selected value="" <?php echo empty($_POST['vessel_type']) ? 'selected' : ''; ?>>Select vessel type</option>
                        <?php
                        // Query to fetch vessel types from the vessel_types table in ascending order
                        $typeQuery = "SELECT DISTINCT type FROM vessel_types ORDER BY type ASC";
                        $typeResult = $conn->query($typeQuery);

                        if ($typeResult && $typeResult->num_rows > 0) {
                            while ($row = $typeResult->fetch_assoc()) {
                                $type = htmlspecialchars($row['type']); // Escape special characters
                                $selected = (isset($_POST['vessel_type']) && $_POST['vessel_type'] === $type) ? 'selected' : '';
                                echo "<option value=\"$type\" $selected>$type</option>";
                            }
                        } else {
                            echo "<option value=\"\">No types available</option>";
                        }
                        ?>
                    </select>

                    <button type="submit" class="dashboard-src-btn"><i class="fa-solid fa-magnifying-glass"></i></button>

                
                </section>
                </form>

                <?php
                // Include the database connection file
                include 'db.php';

                // Pagination variables
                $jobsPerPage = 10; // Number of jobs per page
                $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($currentPage - 1) * $jobsPerPage;

                // Get the search filters from the form
                $jobType = isset($_POST['job_type']) ? $_POST['job_type'] : '';
                $vesselType = isset($_POST['vessel_type']) ? $_POST['vessel_type'] : '';

                // Build the WHERE clause dynamically
                $whereClauses = ["j.expiry >= CURDATE()"]; // Ensure jobs are not expired
                if (!empty($jobType)) {
                    $whereClauses[] = "j.job_title LIKE '%" . $conn->real_escape_string($jobType) . "%'";
                }
                if (!empty($vesselType)) {
                    $whereClauses[] = "j.vessel LIKE '%" . $conn->real_escape_string($vesselType) . "%'";
                }
                $whereSQL = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

                // Query to count total jobs with filters
                $totalJobsQuery = "SELECT COUNT(*) AS total_jobs FROM jobs j $whereSQL";
                $totalJobsResult = $conn->query($totalJobsQuery);
                $totalJobs = $totalJobsResult->fetch_assoc()['total_jobs'];
                $totalPages = ceil($totalJobs / $jobsPerPage);

                // Query to fetch jobs with filters for the current page, prioritizing employers by member type
                $jobQuery = "SELECT j.* , e.company, e.logo, e.company_profile, e.phone, e.email, e.address, e.website
                            FROM jobs j
                            INNER JOIN employer e ON j.company_code = e.company_code
                            $whereSQL
                            ORDER BY 
                                FIELD(e.member_type, 'Plan4', 'Plan3', 'Plan2', 'Plan1', 'free'), 
                                j.date_posted DESC
                            LIMIT $offset, $jobsPerPage";
                $jobResult = $conn->query($jobQuery);
                ?>
                
                <section class="dashboard-job-container">
                    <?php if ($jobResult && $jobResult->num_rows > 0): ?>
                        <?php while ($job = $jobResult->fetch_assoc()): ?>
                            <article class="job-details-container">
                                <section class="related-job-card">
                                    <div class="job-info">
                                        <label class="job-title"><?php echo htmlspecialchars($job['job_title']); ?></label>
                                        
                                        <div class="job-details">
                                            <p class="job-description"><i class="fas fa-ship"></i> <?php echo htmlspecialchars($job['vessel']); ?></p>
                                            <p class="job-description"><i class="fa-solid fa-calendar"></i> Posted on: <?php echo htmlspecialchars($job['date_posted']); ?></p>
                                        </div>  
                                        
                                        <a href="#" class="company-link"><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($job['company']); ?></a>
                                    </div>
                                    
                                    <div class="apply-container">
                                        <button class="bookmark-btn">
                                            <i class="fa-regular fa-bookmark"></i>
                                        </button>
                                        <button class="apply-button" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanva-job-<?php echo $job['code']; ?>" aria-controls="offcanvasRight">Details & Apply</button>
                                    </div>
                                </section>
                            </article>

                            <!-- Offcanvas for Job Details -->
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanva-job-<?php echo $job['code']; ?>" aria-labelledby="offcanvasRightLabel">
                                <div class="offcanvas-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <div class="job-posting">
                                        <?php
                                        $logoPath = "company-logo/" . htmlspecialchars($job['logo']);
                                        if (!file_exists($logoPath) || empty($job['logo'])) {
                                            $logoPath = "company-logo/Logo-placeholder.png"; // Fallback logo
                                        }
                                        ?>
                                        <div class="header-logo mb-3 text-center">
                                            <img src="<?php echo $logoPath; ?>" alt="Wallem Logo" style="width: 80%; height: 150px;">
                                        </div>
                                        <h5 class="fw-bold"><?php echo htmlspecialchars($job['job_title']); ?></h5>
                                        
                                        <div class="icon-text-group mb-4">
                                            <div class="icon-text-row">
                                                <i class="bi bi-truck-front-fill"></i>
                                                <p>Vessel type: <?php echo htmlspecialchars($job['vessel']); ?></p>
                                            </div>
                                            <div class="icon-text-row">
                                                <i class="bi bi-calendar2-week"></i>
                                                <p>Posted on: <?php echo htmlspecialchars($job['date_posted']); ?></p>
                                            </div>
                                            <div class="icon-text-row">
                                                <i class="bi bi-calendar2-week"></i>
                                                <p>Contract Length</p>
                                            </div>
                                            <div class="icon-text-row">
                                                <i class="bi bi-list-check"></i>
                                                <p>Job Requirements: <?php echo htmlspecialchars($job['requirements']); ?></p>
                                            </div>
                                            <div class="icon-text-row">
                                                <i class="bi bi-file-earmark-text"></i>
                                            <p>
                                                <?php echo htmlspecialchars($job['job_description']); ?>
                                            </p>
                                            </div>
                                        </div>
                                        
                                        <section class="user-submit-form">
                                            <form action="includes/seaman_apply.php" class="row align-items-end g-2"
                                                data-job-code="<?php echo htmlspecialchars($job['code']); ?>"
                                                data-company-code="<?php echo htmlspecialchars($job['company_code']); ?>"
                                                data-job-title="<?php echo htmlspecialchars($job['job_title']); ?>"
                                                data-company-name="<?php echo htmlspecialchars($job['company']); ?>"
                                                data-company-email="<?php echo htmlspecialchars($job['email']); ?>">

                                                <!-- CV/Resume Upload UI -->
                                                <div class="col-12 mb-3">
                                                    <label class="form-label fw-semibold">CV/Resume<span class="text-danger">*</span></label>
                                                    <div class="cv-upload-box">
                                                        <label for="cvUpload-<?php echo $job['code']; ?>" class="cv-upload-label">
                                                            <span class="text-success fw-semibold">Choose file to upload</span> or drop here<br>
                                                            <small class="text-muted">*.pdf, *.doc, *.docx, *.odt or *.txt 3MB max</small>
                                                            <input type="file" id="cvUpload-<?php echo $job['code']; ?>" name="cvUpload" accept=".pdf,.doc,.docx,.odt,.txt" hidden>
                                                        </label>
                                                        <p id="fileName-<?php echo $job['code']; ?>" class="text-muted mt-2"></p> <!-- File name display -->
                                                    </div>
                                                </div>

                                                <!-- Your existing form fields -->
                                                <div class="col-12 d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-danger w-100 fw-bold">Apply Now!</button>
                                                </div>
                                            </form>
                                        </section>

                                        <div class="company-profile p-3 border rounded">
                                            <h6 class="fw-bold"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                                            <p class="mb-1 text-muted"><strong>Company Profile: </strong><?php echo htmlspecialchars($job['company_profile']); ?></p>
                                            <br>
                                            <p class="mb-1 text-muted"><strong>Company Phone: </strong><?php echo htmlspecialchars($job['phone']); ?></p>
                                            <p class="mb-1 text-muted"><strong>Company Email: </strong><?php echo htmlspecialchars($job['email']); ?></p>
                                            <p class="mb-2 text-muted"><strong>Company Address: </strong><?php echo htmlspecialchars($job['address']); ?></p>
                                            <?php
                                            $website = htmlspecialchars($job['website']);
                                            if (!empty($website)) {
                                                // Prepend "http://" if the URL doesn't already have a protocol
                                                if (!preg_match('/^https?:\/\//', $website)) {
                                                    $website = 'http://' . $website;
                                                }
                                            }
                                            ?>
                                            <?php if (!empty($job['website'])): ?>
                                                <a href="<?php echo $website; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger w-100">Direct Apply →</a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary w-100" disabled>No Website Available</button>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No jobs available at the moment.</p>
                    <?php endif; ?>
                </section>

                <section class="section-pagination">
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-prev"><a href="?page=<?php echo $currentPage - 1; ?>">&lt;</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-number <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-next"><a href="?page=<?php echo $currentPage + 1; ?>">&gt;</a></li>
                        <?php endif; ?>
                    </ul>
                </section>
            </div>
            <div class="currency-date-aside">
                <aside class="currency-container">
                    <h3>Currency Exchange Rates</h3>
                    <div id="currency-list">
                        
                    </div>
                    <div class="currency-pagination">
                        <button class="page-btn" id="prev-btn">Prev</button>
                        <button class="page-btn" id="next-btn">Next</button>
                    </div>
                </aside>
                <aside class="calendar-container">
                    <div class="calendar-header">   
                        <span id="month-year"></span>
                        <span id="year"></span>
                    </div>
                    <div class="calendar-days" id="calendar"></div>
                </aside>
            </div>
        </section>

    </main>
    
    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script src="script/currency-calendar.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select all file inputs
            const fileInputs = document.querySelectorAll('input[type="file"]');

            fileInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const fileNameDisplay = document.getElementById('fileName-' + this.id.split('-')[1]);
                    if (this.files && this.files.length > 0) {
                        fileNameDisplay.textContent = `Selected file: ${this.files[0].name}`;
                    } else {
                        fileNameDisplay.textContent = '';
                    }
                });
            });
        });

        document.querySelector('.user-submit-form form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Add additional fields from data attributes
            formData.append('job_code', this.dataset.jobCode);
            formData.append('company_code', this.dataset.companyCode);
            formData.append('job_title', this.dataset.jobTitle);
            formData.append('company_name', this.dataset.companyName);
            formData.append('company_email', this.dataset.companyEmail);

            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Show success message
                    alert(data.message);
                    // Optionally redirect or refresh
                    window.location.href = '../dashboardjobs.php';
                } else {
                    // Show error message
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during submission.');
            });
        });

    </script>
</body>
</html>