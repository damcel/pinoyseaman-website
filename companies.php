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

$companyQuery = "SELECT * FROM employer 
                 WHERE verify = 'y' 
                 ORDER BY FIELD(member_type, 'Plan4', 'Plan3', 'Plan2', 'Plan1', 'FREE')";
$companyResult = $conn->query($companyQuery);
$companyCount = $companyResult->num_rows;
$companyData = $companyResult->fetch_all(MYSQLI_ASSOC);

$companyLogoPath = "company-logo/";
$companyLogoDefault = "Logo-placeholder.png"; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="Pinoyseaman.ico" type="image/x-icon"> 
    <title>Profile Settings</title>
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
            <!-- <div class="saved-ctn">
                <a href="saved.html" class="saved-btn">
                    <i class="fa-solid fa-book-bookmark"></i>
                </a>
            </div> -->
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="userprofile.php" class="prfl">Profile Settings</a>
                    <a href="includes/logout.php">Logout</a>
                </div>
            </div>
        </section>

        <section class="company-search-container">
            <form class="search-box" role="search">
                <input type="text" placeholder="Search by company name" aria-label="Search by company name">
                <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </section>

        <section class="company-search-main-container">
            <h2>Explore Companies</h2>
            <section class="company-list">
            <?php foreach ($companyData as $company): ?>
                <?php
                    $logoFile = !empty($company['logo']) && file_exists($companyLogoPath . $company['logo'])
                                ? $companyLogoPath . $company['logo']
                                : $companyLogoPath . $companyLogoDefault;

                    // Get job count for this specific company
                    $companyCode = $company['company_code'];
                    $jobQuery = "SELECT COUNT(*) as job_total FROM jobs WHERE company_code = ? AND expiry > CURDATE()";
                    $stmt = $conn->prepare($jobQuery);
                    $stmt->bind_param("s", $companyCode);
                    $stmt->execute();
                    $jobResult = $stmt->get_result();
                    $jobRow = $jobResult->fetch_assoc();
                    $jobTotal = $jobRow['job_total'];
                    $stmt->close();
                ?>
                <a href="company-profile.php?company_code=<?= urlencode($companyCode) ?>" class="company-card">
                    <img src="<?= htmlspecialchars($logoFile) ?>" alt="Company Logo" class="company-logo">
                    <div class="company-name">
                        <h3><?= htmlspecialchars($company['company']) ?></h3>
                    </div>
                    <div class="company-job-count"><?= $jobTotal ?> Job<?= $jobTotal != 1 ? 's' : '' ?></div>
                </a>
            <?php endforeach; ?>
            </section>

            <div class="see-more">
                <button>See more</button>
            </div>
        </section>
    
    </main>
    <script src="script/see-more.js"></script>
    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>

</body>
</html>