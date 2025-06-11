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


$invoiceNumber = htmlspecialchars($row['code']);
$companyEmail = htmlspecialchars($row['email']);

// At the top of manual-payment-receipt.php, after the existing PHP code:

// Get the selected plan from session or URL
$selectedPlan = $_GET['plan'] ?? $_SESSION['selected_plan'] ?? 'none';
$_SESSION['selected_plan'] = $selectedPlan;

$planDetails = [
    'monthly' => [
        'name' => 'Monthly Subscription', 
        'price' => 20000,
        'description' => 'Monthly Plan - pinoyseaman.com'
    ],
    'yearly' => [
        'name' => '1 Year Package (50% Discount)', 
        'price' => 120000,
        'description' => '1 Year Plan - pinoyseaman.com'
    ],
];

$productName = $planDetails[$selectedPlan]['name'] ?? 'No Plan Selected';
$productPrice = $planDetails[$selectedPlan]['price'];
$productDescription = $planDetails[$selectedPlan]['description'] ?? '';

// Generate dates for the invoice
$currentDate = new DateTime();
$invoiceDate = $currentDate->format('l, F jS, Y');

if ($selectedPlan === 'yearly') {
    $endDate = clone $currentDate;
    $endDate->add(new DateInterval('P1Y')); // Add 1 year
    $dateRange = $currentDate->format('m/d/Y') . ' - ' . $endDate->format('m/d/Y');
    $description = "1 Year Plan - pinoyseaman.com ($dateRange)";
} else {
    // Default to monthly if not yearly
    $endDate = clone $currentDate;
    $endDate->add(new DateInterval('P1M')); // Add 1 month
    $dateRange = $currentDate->format('m/d/Y') . ' - ' . $endDate->format('m/d/Y');
    $description = "Monthly Plan - pinoyseaman.com ($dateRange)";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/manual-payment-receipt.css">
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
                            <li class="step completed"> <!-- THIS IS FOR PAYMENT -->
                                <span class="circle"><i class="fa-solid fa-hourglass-half"></i></span>
                                <span class="label">Payment</span>
                            </li>
                            <li class="step active"> <!-- THIS IS FOR CONFIRM -->
                                <span class="circle">&#10003;</span>
                                <span class="label">Confirm</span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </section>
        
            <section class="job-list payment-details">
                <article class="invoice-box">
                    <div class="header">
                        <div class="paid-badge bg-warning text-dark">PENDING</div>
                    
                        <div class="header-content">
                            <div class="logo">
                                <img src="pinoyseaman-logo/alternativeHeaderLogo.png" alt="pinoyseaman-logo">
                            </div>
                            <div class="company-info">
                                <h2>Pinoy Seaman</h2>
                                <p>Makati City<br>Philippines</p>
                            </div>
                        </div>
                    </div>
            
                    <div class="invoice-info">
                        <h3>Invoice #<?= $invoiceNumber ?></h3>
                        <p><strong>Invoice Date:</strong> Sunday, February 9th, 2025</p>
                    </div>
            
                    <div class="billed-to">
                        <h4>Invoiced To</h4>
                        <p><?= $companyEmail ?></p>
                    </div>
            
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= htmlspecialchars($description) ?></td>
                                <td>₱<?= number_format($productPrice) ?></td>
                            </tr>
                            <tr class="summary">
                                <td><strong>Sub Total</strong></td>
                                <td class="total-amount"><strong>₱<?= number_format($productPrice) ?></strong></td>
                            </tr>
                            <tr class="summary total">
                                <td><strong>Total</strong></td>
                                <td class="total-amount"><strong>₱<?= number_format($productPrice) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
            
                    <!-- <div class="transaction-history">
                        <h4>Transactions</h4>
                        <table class="transaction-table">
                            <thead>
                                <tr>
                                    <th>Transaction Date</th>
                                    <th>Gateway</th>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Monday, February 10th, 2025</td>
                                    <td>Bank Transfer</td>
                                    <td>BDO-02102025</td>
                                    <td>₱4650.00</td>
                                </tr>
                                <tr class="summary">
                                    <td colspan="3"><strong>Balance</strong></td>
                                    <td class="total-amount"><strong>₱0.00</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->
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
  

    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script src="script/currency-calendar.js"></script>
</body>
</html>