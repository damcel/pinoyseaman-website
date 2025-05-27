<?php
session_start();

// Set session timeout duration (e.g., 30 minutes)
$timeoutDuration = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    session_unset();
    session_destroy();
    header("Location: user-login-signup.php?type=error&message=Session timed out. Please log in again.");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['seeker_id'])) {
    header("Location: user-login-signup.php?type=error&message=You must log in to access this page.");
    exit;
}

include 'db.php';

$companyLogoPath = "company-logo/";
$companyLogoDefault = "Logo-placeholder.png";

// Get company_code from URL
$companyCode = isset($_GET['company_code']) ? $_GET['company_code'] : '';

if (!$companyCode) {
    echo "<h2>Invalid company.</h2>";
    exit;
}

// Fetch company details
$stmt = $conn->prepare("SELECT * FROM employer WHERE company_code = ? AND verify = 'y' LIMIT 1");
$stmt->bind_param("s", $companyCode);
$stmt->execute();
$companyResult = $stmt->get_result();
$company = $companyResult->fetch_assoc();
$stmt->close();

if (!$company) {
    echo "<h2>Company not found or not verified.</h2>";
    exit;
}

// Company logo
$logoFile = !empty($company['logo']) && file_exists($companyLogoPath . $company['logo'])
    ? $companyLogoPath . $company['logo']
    : $companyLogoPath . $companyLogoDefault;

// Get job count for this company
$jobQuery = "SELECT COUNT(*) as job_total FROM jobs WHERE company_code = ? AND expiry > CURDATE()";
$stmt = $conn->prepare($jobQuery);
$stmt->bind_param("s", $companyCode);
$stmt->execute();
$jobResult = $stmt->get_result();
$jobRow = $jobResult->fetch_assoc();
$jobTotal = $jobRow['job_total'];
$stmt->close();

// Fetch jobs for this company (limit to 10 for demo, add pagination as needed)
$jobs = [];
$jobListQuery = "SELECT * FROM jobs WHERE company_code = ? AND expiry > CURDATE() ORDER BY expiry DESC LIMIT 10";
$stmt = $conn->prepare($jobListQuery);
$stmt->bind_param("s", $companyCode);
$stmt->execute();
$jobListResult = $stmt->get_result();
while ($job = $jobListResult->fetch_assoc()) {
    $jobs[] = $job;
}
$stmt->close();
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
    <link rel="icon" href="Pinoyseaman.ico" type="image/x-icon"> 
    <title><?= htmlspecialchars($company['company']) ?> - Company Profile</title>
</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="sidebar-header">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="<?= htmlspecialchars($logoFile) ?>" alt="pinoyseaman-logo" id="sidebar-logo">
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
                <a href="history.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-business-time"></i><span>History</span>
                </a>
              </li>
              <li class="separator">
                <a href="companies.php">
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
            
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="userprofile.php" class="prfl">Profile Settings</a>
                    <a href="includes/logout.php">Logout</a>
                </div>
            </div>
        </section>

        <section class="job-list-container">
            <div class="job-search-container">
                <section class="company-profile-container">
                    <div class="company-cover">
                        <div class="company-profile-box">
                            <img src="<?= htmlspecialchars($logoFile) ?>" alt="image">
                        </div>
                    </div>
                
                    <article class="company-info-box">
                        <!-- Moved Company Name, Rating, and Review Button inside company-info-box -->
                        <header class="company-header">
                            <div class="company-name">
                                <h2><?= htmlspecialchars($company['company']) ?></h2>
                            </div>
                        </header>
                
                        <h3>Company Overview</h3>
                
                        <dl class="company-details">
                            <div class="info-item">
                                <dt>Website</dt>
                                <dd>
                                    <?php if (!empty($company['website'])): ?>
                                        <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($company['website']) ?></a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </dd>
                            </div>
                
                            <div class="info-item">
                                <dt>Phone</dt>
                                <dd><?= htmlspecialchars($company['phone'] ?? 'N/A') ?></dd>
                            </div>
                
                            <div class="info-item">
                                <dt>Contact Person</dt>
                                <dd><?= htmlspecialchars($company['contact'] ?? 'N/A') ?></dd>
                            </div>
                
                            <div class="info-item">
                                <dt>Primary location</dt>
                                <dd><?= htmlspecialchars($company['address'] ?? 'N/A') ?></dd>
                            </div>
                        </dl>
                
                        <section class="company-description">
                            <p>
                                <?= !empty($company['company_profile']) ? nl2br(htmlspecialchars($company['company_profile'])) : 'No company description available.' ?>
                            </p>
                        </section>
                    </article>
                </section>
                
                <h3>Job Offer</h3>
                <section class="dashboard-job-container">
                    <?php if (count($jobs) > 0): ?>
                        <?php foreach ($jobs as $job): ?>
                            <article class="job-details-container">
                                <section class="related-job-card">
                                    <div class="job-info">
                                        <label class="job-title"><?= htmlspecialchars($job['job_title']) ?></label>
                                        <div class="job-details">
                                            <p class="job-description"><i class="fas fa-ship"></i> <?= htmlspecialchars($job['vessel'] ?? 'N/A') ?></p>
                                            <p class="job-description"><i class="fa-solid fa-calendar"></i> <?= htmlspecialchars($job['contract'] ?? 'N/A') ?></p>
                                        </div>
                                        <a href="company-profile.php?company_code=<?= urlencode($companyCode) ?>" class="company-link">
                                            <i class="fas fa-briefcase"></i> <?= htmlspecialchars($company['company']) ?>
                                        </a>
                                    </div>
                                    <div class="apply-container">
                                        <!-- <a href="job-details.php?job_id=<?= urlencode($job['id']) ?>" class="apply-button">Details & Apply</a> -->
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
                                        
                                        <div class="header-logo mb-3 text-center">
                                            <img src="<?= htmlspecialchars($logoFile) ?>" alt="Wallem Logo" style="width: 80%; height: 150px;">
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
                                                <p>Contract Length: <?php echo htmlspecialchars($job['contract']); ?></p>
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
                                            <p class="mb-1 text-muted"><strong>Company Profile: </strong><?php echo nl2br(htmlspecialchars($company['company_profile'])); ?></p>
                                            <br>
                                            <p class="mb-1 text-muted"><strong>Company Phone: </strong><?php echo htmlspecialchars($company['phone']); ?></p>
                                            <p class="mb-1 text-muted"><strong>Company Email: </strong><?php echo htmlspecialchars($company['email']); ?></p>
                                            <p class="mb-2 text-muted"><strong>Company Address: </strong><?php echo htmlspecialchars($company['address']); ?></p>
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

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No job offers available for this company.</p>
                    <?php endif; ?>
                </section>

                <!-- <section>
                    <div class="section-pagination"> 
                        <ul class="pagination">
                            <li class="page-prev">&lt;</li>
                            <li class="page-number active">1</li>
                            <li class="page-number">2</li>
                            <li class="page-number">3</li>
                            <li class="page-next">&gt;</li>
                        </ul>
                    </div>
                </section> -->
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