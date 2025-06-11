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

// Fetch the verification status from the database
$employerEmail = $_SESSION['employer_email'];
$query = "SELECT * FROM employer WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employerEmail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$verifyStatus = $row['verify'] ?? 'n'; 
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/account-plan.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Account Plan</title>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'components/employer_aside.php'; ?>

    <main class="dashboard-container">
        <section class="header-container">
            <!-- Your existing saved and profile dropdown (unchanged) -->
            <div class="saved-ctn">
              <a href="#" class="saved-btn">
                <i class="fa-solid fa-book-bookmark"></i>
              </a>
            </div>
            <div class="dropdown-container">
              <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
              <div class="dropdown" id="dropdownMenu">
                <a href="employer-settings.php" class="prfl">Settings</a>
                <a href="includes/logout.php">Logout</a>
              </div>
            </div>
        </section>
        <section class="account-plan-setup-container">
            <section class="job-list">
                <article class="intro-section">
                  <div class="intro-header">
                    <h2>Pick a plan that’s <span class="highlight">right for you</span></h2>
                    <p class="intro-subtext">
                      Pricing plans for businesses at every stage of growth.<br>
                      Try our risk-free for 14 days. No credit card required.
                    </p>
                  </div>
                  <div class="free-plan-promo">
                    <strong>100% free Plan</strong>
                    <p>Get started with our free plan and make 10 lookups per month absolutely free!</p>
                    <div>Limited Access to applicant</div>
                    <p class="note">Payment Methods will be instructed after availing a subscription</p>
                  </div>
                </article>
          
                <article class="pricing-cards">
                  <div class="pricing-box">
                    <div>
                      <div class="plan-header">Monthly</div>
                      <div class="price">₱20,000<span>Subscription per Month</span></div>
                      <ul class="features">
                          <li><span class="icon">✔</span> Access to All Features</li>
                          <li><span class="icon">✔</span> Unlimited search to top rank seafarers directly</li>
                          <li><span class="icon">✔</span> Features Company Badge</li>
                          <li><span class="icon">✔</span> Unlimited Job Posting</li>
                          <li><span class="icon">✔</span> Notification via email</li>
                          <li><span class="icon">✔</span> Priority job listings</li>
                      </ul>
                    </div>
                    <div>
                      <!-- Monthly Plan -->
                      <button class="action-button" onclick="window.location.href='premium-account-manual.php?plan=monthly';">
                          Subscribe now
                      </button>
                      <p class="note">No credit card required</p>
                    </div>
                  </div>
          
                  <div class="pricing-box premium">
                    <div>
                      <div class="plan-header-annually">1 year package</div>
                      <div class="price">₱120,000<span>Subscription Annully</span></div>
                      <ul class="features">
                        <li class="discount"><span class="icon">✔</span>50% Discount</li>
                        <li><span class="icon">✔</span> Access to All Features</li>
                        <li><span class="icon">✔</span> Unlimited search to top rank seafarers directly</li>
                        <li><span class="icon">✔</span> Features Company Badge</li>
                        <li><span class="icon">✔</span> Unlimited Job Posting</li>
                        <li><span class="icon">✔</span> Notification via email</li>
                        <li><span class="icon">✔</span> Priority job listings</li>
                      </ul>
                    </div>
                    <div>
                        <!-- Yearly Plan -->
                        <button class="action-button" onclick="window.location.href='premium-account-manual.php?plan=yearly';">
                            Subscribe now
                        </button>
                      <p class="note">No credit card required</p>
                    </div>
                  </div>
                </article>
            </section>
    </main>
    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script src="script/currency-calendar.js"></script>
</body>
</html>