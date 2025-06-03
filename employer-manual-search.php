<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

// Fetch the verification status and member_type from the database
$employerEmail = $_SESSION['employer_email'];
$query = "SELECT * FROM employer WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employerEmail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$verifyStatus = $row['verify'] ?? 'n'; // Default to 'n' if not found
$memberType = $row['member_type'];
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
    <title>Dashboard</title>

    <style>

      .summary-table {
        table-layout: fixed;
        width: 100%;
      }

      .experience-cell p{
        width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .experience-cell {
        width: 250px;
        white-space: normal;
        word-wrap: break-word;
        vertical-align: top;
      }

      .view-applicant-btn {
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .view-applicant-btn button {
        padding: 6px 12px;
        background-color: #007bff;
        border: none;
        color: white;
        border-radius: 4px;
        cursor: pointer;
      }

      .search-bar-container {
        position: relative;
        width: 100%;
        max-width: 400px;
        margin: 10px 0 20px 0;
      }

      #jobSearchInput {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #ccc;
        border-radius: 25px;
        font-size: 14px;
        outline: none;
        transition: 0.3s ease;
      }

      #jobSearchInput:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
      }

      .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        pointer-events: none;
      }

      #pagination-jobposted {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        gap: 12px;
        flex-wrap: wrap;
      }

      #pagination-jobposted .pagination-btn {
        background-color: #ffffff;
        border: 1px solid #ccc;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        font-size: 16px;
        color: #333;
      }

      #pagination-jobposted .pagination-btn:hover {
        background-color: #f0f0f0;
        border-color: #999;
      }

      #pageNumbers-jobposted {
        display: flex;
        gap: 8px; /* controls spacing between page numbers */
        align-items: center;
        justify-content: center;
      }

      #pagination-jobposted .page-number {
        background-color: #ffffff;
        border: 1px solid #ccc;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        font-size: 16px;
        color: #333;
      }

      #pagination-jobposted .page-number:hover {
        background-color: #f0f0f0;
        border-color: #999;
      }

      #pagination-jobposted .page-number.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
      }


      /* Make table responsive on small screens */
      @media (max-width: 768px) {

          .dashboard-job-container, .job-list-container{
              padding: 0;
              margin: 10px 0;
          }
        .table-responsive {
          overflow-x: auto;
        }

        .summary-table {
          border-collapse: collapse;
          width: 100%;
          display: block;
        }
        .summary-table thead {
          display: none;
        }

        .summary-table tbody,
        .summary-table tr,
        .summary-table td {
          display: block;
          width: 100%;
        }

        .summary-table tr {
          margin-bottom: 15px;
          border: 1px solid #ddd;
          border-radius: 8px;
          padding: 10px;
          background-color: #f9f9f9;
        }

        .summary-table td {
          text-align: left;
          padding: 8px 10px;
          border: none;
          font-size: 14px;
        }

        .summary-table td::before {
          content: attr(data-label);
          font-weight: bold;
          display: block;
          margin-bottom: 5px;
          color: #333;
        }

        .experience-cell p {
          white-space: normal;
          overflow: hidden;
          text-overflow: ellipsis;
          display: -webkit-box;
          -webkit-line-clamp: 3;
          -webkit-box-orient: vertical;
          max-height: 4.5em; /* limit to 3 lines */
        }

        .view-applicant-btn {
          justify-content: flex-start;
          margin-top: 10px;
        }

        .view-applicant-btn button {
          width: 100%;
        }
      }


    </style>
 
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
                <div class="company-profile-card">
                  <img src="company-logo/scanmar_big.jpg" alt="company-logo">
                </div>
              <li>
                <a href="employer-dashboard.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-briefcase"></i><span>Dashboard</span>
                </a>
              </li>
              <li>
                <a href="employer-posting.php">
                  <!-- SVG Icon -->
                  <i class="fa-regular fa-user"></i><span>Job Post</span>
                </a>
              </li>
              <li class="separator">
                <a href="employer-manual-search.php">
                  <!-- SVG Icon -->
                  <i class="fa-regular fa-user"></i><span>Manual Search</span>
                </a>
              </li>
            </ul>
        </nav>
    </aside>
    
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
                <a href="includes/logout_employer.php">Logout</a>
              </div>
            </div>
        </section>

        <?php
// Pagination settings
$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Check employer's member type (case insensitive)
$empMemberType = isset($memberType) ? strtolower($memberType) : 'free';
$isFreeMember = in_array($empMemberType, ['free', 'free', 'free']);

// Adjust limit for free members
$displayLimit = $isFreeMember ? 10 : $limit;

// Search term from GET or POST
$searchTerm = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';

// Base query conditions
$baseConditions = "WHERE email IS NOT NULL AND email != '' 
                  AND first_name IS NOT NULL AND first_name != ''";

// Add search conditions if search term exists
$searchConditions = '';
if (!empty($searchTerm)) {
    $searchTermLike = "%" . $conn->real_escape_string($searchTerm) . "%";
    $searchConditions = " AND (CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) LIKE '$searchTermLike' 
                        OR rank LIKE '$searchTermLike' 
                        OR seagoing_work LIKE '$searchTermLike')";
}

// Fetch total count of job seekers matching search
$countQuery = "SELECT COUNT(*) as total FROM job_seeker 
              $baseConditions $searchConditions";
$countResult = $conn->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch job seeker data with search and pagination
$query = "SELECT first_name, middle_name, last_name, email, cellphone, seagoing_work, rank, id,
          CASE
              WHEN (middle_name IS NOT NULL AND middle_name != '' 
                    AND last_name IS NOT NULL AND last_name != ''
                    AND cellphone IS NOT NULL AND cellphone != ''
                    AND seagoing_work IS NOT NULL AND seagoing_work != '') THEN 1
              ELSE 0
          END as is_complete
          FROM job_seeker
          $baseConditions $searchConditions
          ORDER BY is_complete DESC, date DESC
          LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $jobSeekers = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $jobSeekers = [];
}
?>

<section class="job-list-container">
    <div class="job-search-container">                  
        <section class="dashboard-job-container">
            <div class="display-job-posted">
                <div class="project-summary">
                    <div class="search-bar-container">
                        <form id="searchForm" method="get" action="">
                            <input type="text" id="jobSearchInput" name="search" placeholder="Search by Fullname or Rank..." 
                                   value="<?= htmlspecialchars($searchTerm) ?>" />
                            <i class="fa fa-search search-icon" onclick="document.getElementById('searchForm').submit()"></i>
                            <input type="hidden" name="page" value="1" id="hiddenPageInput">
                            <input type="hidden" id="isFreeMember" name="is_free_member" value="<?= $isFreeMember ? 'true' : 'false' ?>">
    <input type="hidden" id="displayLimit" name="display_limit" value="<?= $displayLimit ?>">
                        </form>
                    </div>
            
                    <div class="table-responsive">
                        <table class="summary-table" id="projectTable">
                            <thead class="job-posted-header">
                                <tr>
                                    <th>Fullname</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Experience</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php foreach ($jobSeekers as $seeker): ?>
                                    <tr class="applicant-row job-posted">
                                        <td data-label="Fullname">
                                            <?= htmlspecialchars($seeker['first_name'] . ' ' . 
                                                ($seeker['middle_name'] ? $seeker['middle_name'] . ' ' : '') . 
                                                $seeker['last_name']) ?>
                                        </td>
                                        <td data-label="email"><?= htmlspecialchars($seeker['email']) ?></td>
                                        <td data-label="contact"><?= htmlspecialchars($seeker['cellphone']) ?></td>
                                        <td data-label="Experience" class="experience-cell">
                                            <p><?= htmlspecialchars($seeker['seagoing_work']) ?></p>
                                        </td>
                                        <td data-label="information" class="view-applicant-btn" 
                                            data-bs-toggle="modal" data-bs-target="#applicant-profile-modal" data-applicant-id="<?= htmlspecialchars($seeker['id']) ?>">
                                            <button>view</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if ($isFreeMember && count($jobSeekers) > $displayLimit): ?>
                            <div class="upgrade-notice alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> 
                                Free members can only view <?= $displayLimit ?> profiles. 
                                <a href="membership.php">Upgrade your account</a> to see all profiles.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (count($jobSeekers) === 0): ?>
                            <div class="no-results">No job seekers found matching your search criteria.</div>
                        <?php endif; ?>

                        <div class="pagination-container <?= $isFreeMember ? 'free-member-pagination' : '' ?>" id="pagination-jobposted">
                            <?php if ($totalPages > 1): ?>
                                <button id="prevPage-jobposted" class="pagination-btn" 
                                    onclick="navigateToPage(<?= max(1, $page - 1) ?>)"
                                    <?= $page <= 1 ? 'disabled' : '' ?>>
                                    <i class="fa fa-arrow-left"></i>
                                </button>
                                
                                <div id="pageNumbers-jobposted" class="pagination-numbers">
                                    <?php 
                                    // Show first page and ellipsis if not in first range
                                    if ($page > 3) {
                                        echo '<a href="javascript:void(0)" onclick="navigateToPage(1)" class="page-number">1</a>';
                                        if ($page > 4) echo '<span class="ellipsis">...</span>';
                                    }
                                    
                                    // Show page numbers around current page
                                    for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) {
                                        $active = $i == $page ? ' active' : '';
                                        echo '<a href="javascript:void(0)" onclick="navigateToPage('.$i.')" class="page-number'.$active.'">'.$i.'</a>';
                                    }
                                    
                                    // Show last page and ellipsis if not in last range
                                    if ($page < $totalPages - 2) {
                                        if ($page < $totalPages - 3) echo '<span class="ellipsis">...</span>';
                                        echo '<a href="javascript:void(0)" onclick="navigateToPage('.$totalPages.')" class="page-number">'.$totalPages.'</a>';
                                    }
                                    ?>
                                </div>
                                
                                <button id="nextPage-jobposted" class="pagination-btn" 
                                    onclick="navigateToPage(<?= min($totalPages, $page + 1) ?>)"
                                    <?= $page >= $totalPages ? 'disabled' : '' ?>>
                                    <i class="fa fa-arrow-right"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>                  
        </section>
    </div>
</section>

        <!-- Modal for viewing applicant profile -->
        <?php include 'components/show_applicant_profile.php'; ?>        
        
        <style>
        .blurred-row {
            filter: blur(3px);
            opacity: 0.6;
            pointer-events: none;
        }
        .upgrade-notice {
            text-align: center;
            padding: 10px;
            border-radius: 4px;
        }
        .upgrade-notice a {
            font-weight: bold;
            text-decoration: underline;
        }
        .free-member-pagination {
    position: relative;
}

.free-member-pagination::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    z-index: 1;
}

.free-member-pagination .pagination-btn,
.free-member-pagination .page-number {
    position: relative;
    z-index: 2;
    opacity: 0.5;
    pointer-events: none;
}

.free-member-pagination .ellipsis {
    position: relative;
    z-index: 2;
}
        </style>

    </main>       
          
    <script src="script/dashboard-drop-jobslist.js"></script>
    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <script src="script/employer-manual-search.js"></script>
<script>
function navigateToPage(page) {
    document.getElementById('hiddenPageInput').value = page;
    document.getElementById('searchForm').submit();
}
</script>


</body>
</html>