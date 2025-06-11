<?php
session_name("employerSession");
session_start(); // Start the session

$selectedPlan = $_GET['plan'] ?? 'none';
$_SESSION['selected_plan'] = $selectedPlan;

$planDetails = [
    'monthly' => ['name' => 'Monthly Subscription', 'price' => 20000],
    'yearly' => ['name' => '1 Year Package (50% Discount)', 'price' => 120000],
];

$productName = $planDetails[$selectedPlan]['name'] ?? 'No Plan Selected';
$productPrice = $planDetails[$selectedPlan]['price'] ?? 0;


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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/premium-account-manual.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Account Plan</title>

</head>
<body>

    <!-- Sidebar -->
    <?php include 'components/employer_aside.php'; ?>

    <main class="dashboard-container">
        <section class="header-container">
            <div class="saved-ctn">
                <a href="#" class="saved-btn">
                    <i class="fa-solid fa-book-bookmark"></i>
                </a>
            </div>
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="userprofile.php" class="prfl">Profile Settings</a>
                    <a href="index.php">Logout</a>
                </div>
            </div>
        </section>
        <section class="profile-setup-container">
            <section>
                <div class="premium-account-container">
                    <nav class="premium-account">
                        <ul class="progress-steps">
                            <li class="step completed"> <!-- THIS IS FOR SUBSCRIPTION -->
                                <span class="circle">&#10003;</span>
                                <span class="label">Subscription</span>
                            </li>
                            <li class="step active"> <!-- THIS IS FOR PAYMENT -->
                                <span class="circle"><i class="fa-solid fa-hourglass-half"></i></span>
                                <span class="label">Payment</span>
                            </li>
                            <li class="step"> <!-- THIS IS FOR CONFIRM -->
                                <span class="circle"></span>
                                <span class="label">Confirm</span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </section>
        
            <section class="job-list payment-section">
                <article class="payment-container">
                    <div class="how-to-pay-box">
                        <!-- THIS IS for how to payment box on the left -->
                        <h2 class="section-title">How to pay?</h2>
                        <ol class="payment-steps">
                            <li>
                                Click here <strong><a href="#" class="bank-link" data-bs-toggle="modal" data-bs-target="#bank-account">PinoySeamanBankDetails</a></strong> to redirect
                                to the Bank details for manual payment.
                            </li>
                            <li>
                                Send the payment thru the bank that is given bank details.
                            </li>
                            <li>
                                Send the Screenshot of payment via PinoySeaman Email or upload the screenshot/image of the receipt.
                                <ul>
                                    <li>Email: <a href="mailto:admin@pinoyseaman.com" class="email-link">admin@pinoyseaman.com</a></li>
                                </ul>
                            </li>
                            <li>
                                Wait for 10 to 15 minutes for status approval.
                            </li>
                        </ol>
                        <div class="payment-icons">
                            <img src="icons/document-check.png" alt="Security Icon" class="icon" />
                            <img src="icons/mail-check.png" alt="Email Icon" class="icon" />
                            <img src="icons/approved.png" alt="Approved Stamp" class="icon" />
                        </div>
                    </div>
        
                    <div class="purchase-summary-box">
                        <!-- THIS IS for purchase summary on the right-->
                        <div class="summary-card">
                            <h3 class="summary-title">Purchase Summary</h3>
                            <div class="summary-item">
                                <span>Product name</span>
                                <span>Price</span>
                            </div>
                            <div class="summary-item bold">
                                <span><?= htmlspecialchars($productName) ?></span>
                                <span>₱<?= number_format($productPrice, 2) ?></span>
                            </div>
                            <div class="summary-total">
                                <span>Total:</span>
                                <span class="total-price">₱<?= number_format($productPrice, 2) ?></span>
                            </div>
                            <button class="order-status-btn" data-bs-toggle="modal" data-bs-target="#payment-status">View your order status</button>
                        </div>
                    </div>
                </article>
            </section>
        </section>        
    </main>

   <!-- Modal -->
<section class="modal fade" id="bank-account" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Bank Details</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p>Bank: <strong>EastWestSouthNorth Bank</strong></p>
              <p>Name: <strong>Pinoy Seaman Bank</strong></p>
              <p>Account Number: <strong>0123456789</strong></p>
            </div>
            <div class="ms-3">
              <img src="icons/bill.png" alt="Bills Illustration" style="width: 100px; height: auto;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="modal fade" id="payment-status" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class="approved-box">
            <div class="check-icon">
              ✓
            </div>
            <p class="approved-text">Subscription Success</p>
            <a href="#" id="continueBtn" class="approved-btn">Continue</a>
          </div>
      </div>
    </div>
  </section>

    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script src="script/currency-calendar.js"></script>
    <script src="script/upload-receipt.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>

<script>
    const selectedPlan = "<?= $selectedPlan ?>";

    document.getElementById('continueBtn').addEventListener('click', function (e) {
        e.preventDefault();

        fetch('send_subscription_email.php?plan=' + selectedPlan, {
            method: 'POST',
        })
        .then(response => response.text())
        .then(data => {
            console.log("Email status:", data);
            window.location.href = 'manual-payment-receipt.php'; // Redirect afterwards
        })
        .catch(error => {
            console.error("Error sending email:", error);
            window.location.href = 'manual-payment-receipt.php'; // Proceed anyway
        });
    });
</script>



</body>
</html>