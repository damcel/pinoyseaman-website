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
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Job Post</title>
</head>
<body>
    
    <!-- // Include your sidebar here -->
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

        <section class="job-list-container">
            <div class="job-search-container">
                <section class="job-posting-container">
                     <!-- Header Tabs -->
                    <div class="post-header">
                        <h3>Post Job</h3>
                        <p class="subtext">What seafaring rank or position are you hiring for? Post it here!</p>
                        <ul class="tab-list">
                        <li class="active-tab"><a href="employer-posting.php">Published</a></li>
                        <li><a href="applicant-list.html">Applicant</a></li>
                        </ul>
                    </div>
                </section>

                <section class="dashboard-job-container">
                    <!-- Call to Action Box -->
                    <div class="cta-card">
                        <div class="cta-text">
                        <h4>Post quickly, hire smarter.</h4>
                        <p>Aplikanteng Seaman, Isang Post Lang</p>
                        <button class="cta-button" data-bs-toggle="modal" data-bs-target="#jobPostModal">Post Job</button>
                        </div>
                        <div class="cta-image">
                        <img src="https://img.icons8.com/color/96/megaphone.png" alt="Megaphone" />
                        </div>
                    </div>
                </section>
                
                <section class="dashboard-job-container">
                    <!-- related job list -->
                    <article class="job-details-container">
                        <!-- cards for related job list -->
                        <section class="employer-related-job">
                            <div class="job-card">
                                <div class="card-left">
                                    <label class="job-title">Tanker</label>
                                    <p class="posting-job-detail"><i class="fas fa-ship"></i>Cargo Ship</p>
                                    <p class="posting-job-detail"><i class="fa-solid fa-calendar"></i>Contract Length</p>
                                    <p class="posting-job-detail"><i class="fa-solid fa-file-word"></i>Job Requirement</p>
                                    <label>Description</label>
                                    <p class="job-posting-description">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo 
                                    laboriosam numquam molestiae perspiciatis, eius ut exercitationem at?
                                    </p>
                                </div>
                                <div class="card-right">
                                    <button class="job-edit-icon" aria-label="Edit Tanker" data-bs-toggle="modal" data-bs-target="#edit-recent-job"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <img src="company-logo/marsa.jpg" alt="">
                                    <button class="boost-btn">Boost Post</button>
                                </div>
                            </div>
                        </section>
                    </article>

                    <!-- related job list -->
                    <article class="job-details-container">
                        <!-- cards for related job list -->
                        <section class="employer-related-job">
                            <div class="job-card">
                                <div class="card-left">
                                    <label class="job-title">Chief Engineer</label>
                                    <p class="posting-job-detail"><i class="fas fa-ship"></i>Cargo Ship</p>
                                    <p class="posting-job-detail"><i class="fa-solid fa-calendar"></i>Contract Length</p>
                                    <p class="posting-job-detail"><i class="fa-solid fa-file-word"></i>Job Requirement</p>
                                    <label>Description</label>
                                    <p class="job-posting-description">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo 
                                    laboriosam numquam molestiae perspiciatis, eius ut exercitationem at?
                                    </p>
                                </div>
                                <div class="card-right">
                                    <button class="job-edit-icon" aria-label="Edit Tanker" data-bs-toggle="modal" data-bs-target="#edit-recent-job"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <img src="company-logo/marsa.jpg" alt="">
                                    <button class="boost-btn">Boost Post</button>
                                </div>
                            </div>
                        </section>
                    </article>

                </section>                                                  
            </div>
            
            <div class="currency-date-aside">
                <aside class="currency-container">
                    <div class="highlight-box">
                        <h5>Post highlights</h5>
                        <p class="subtext">In the last 30 days</p>
                        <img class="highlight-img" src="https://img.icons8.com/office/80/laptop.png" alt="No highlights" />
                        <p class="highlight-empty">No highlights</p>
                        <p class="highlight-sub">No recent post to highlight.</p>
                      </div>
                </aside>
                <aside class="job-post-container"> 
                    <h2 class="job-post-h2">Recent Job Posted</h2>
                    <div class="job-item">
                        <div class="job-information">
                            <p class="employer-post-job-title">Tanker</p>
                            <button class="job-edit-icon" aria-label="Edit Tanker" data-bs-toggle="modal" data-bs-target="#edit-recent-job"><i class="fa-solid fa-pen-to-square"></i></button>
                        </div>
                        <div class="job-meta">
                            <time class="job-date">12 Sept 2022</time>
                            <div class="job-status">Completed</div>
                        </div>
                    </div>
                
                    <div class="job-item">
                        <div class="job-information">
                            <p class="employer-post-job-title">Chief Cook</p>
                            <button class="job-edit-icon" aria-label="Edit Tanker"><i class="fa-solid fa-pen-to-square"></i></button>
                        </div>
                        <div class="job-meta">
                            <time class="job-date">12 Sept 2022</time>
                            <div class="job-status">Completed</div>
                        </div>
                    </div>
                
                    <div class="job-item">
                        <div class="job-information">
                            <p class="employer-post-job-title">Bosun</p>
                            <button class="job-edit-icon" aria-label="Edit Tanker"><i class="fa-solid fa-pen-to-square"></i></button>
                        </div>
                        <div class="job-meta">
                            <time class="job-date">12 Sept 2022</time>
                            <div class="job-status">Completed</div>
                        </div>
                    </div>
                
                    <div class="job-item">
                        <div class="job-information">
                            <p class="employer-post-job-title">Chief Engineer</p>
                            <button class="job-edit-icon" aria-label="Edit Tanker"><i class="fa-solid fa-pen-to-square"></i></button>
                        </div>
                        <div class="job-meta">
                            <time class="job-date">12 Sept 2022</time>
                            <div class="job-status">Completed</div> 
                        </div>
                    </div>
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

    </main>

    <!-- JOB POST Modal -->
    <section class="modal fade" id="jobPostModal" tabindex="-1" aria-labelledby="jobPostModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="jobPostModalLabel">Create Job</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- HERE -->
                    <form>
                        <div class="mb-3 text-center">
                            <label for="jobImage" class="form-label d-block">Job post image</label>
                            <div class="upload-image">
                                <input type="file" id="jobImage" class="form-control d-none">
                                <div class="upload-box">
                                    <p>Upload Vessel or Company Image</p>
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="jobPostName" class="form-label">Job Title</label>
                                <select class="form-select searchable-select" id="jobPostName">
                                    <option disabled selected>Select job post</option>
                                    <option value="Chief Engineer">Chief Engineer</option>
                                    <option value="Messman">Messman</option>
                                    <option value="Deck Man">Deck Man</option>
                                    <option value="IT">IT</option>
                                    <option value="Offshore Vessel">Offshore Vessel</option>
                                    <option value="Fishing Vessel">Fishing Vessel</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="rank" class="form-label">Rank*</label>
                                <input type="text" class="form-control" id="rank" value="Cadet">
                            </div>
                            <div class="col">
                                <label for="contractLength" class="form-label">Contract Length*</label>
                                <input type="text" class="form-control" id="contractLength" value="9 months">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="vesselType" class="form-label">Vessel type*</label>
                                <input type="text" class="form-control" id="vesselType" placeholder="Vessel Type">
                            </div>
                            <div class="col">
                                <label for="jobRequirements" class="form-label">Job requirements*</label>
                                <input type="text" class="form-control job-requirements-input" id="jobRequirements"
                                    value="SSS, PAG-IBIG, PHILHEALTH, PASSBOOK">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="jobDescription" class="form-label">Job Description*</label>
                            <textarea class="form-control" id="jobDescription" rows="4">lorem ipsum........</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter password" style="width: 50%;">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Post Job</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit recent job Modal -->
    <section class="modal fade" id="edit-recent-job" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">  
        <!-- update / Delete recent job Modal -->
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header justify-content-between align-items-center">
                    <h1 class="modal-title fs-5" id="jobPostModalLabel">Edit Job</h1>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn text-danger p-0" title="Delete Job" id="deleteJobBtn">
                            <i class="fa-solid fa-trash-can fs-5"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <!-- HERE -->
                    <form>
                        <div class="mb-3 text-center">
                            <label for="jobImage" class="form-label d-block">Job post image</label>
                            <div class="upload-image">
                                <input type="file" id="jobImage" class="form-control d-none">
                                <div class="upload-box">
                                    <p>Upload Vessel or Company Image</p>
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="jobPostName" class="form-label">Job Title</label>
                                <select class="form-select searchable-select" id="jobPostName">
                                    <option disabled selected>Select job post</option>
                                    <option value="Chief Engineer">Chief Engineer</option>
                                    <option value="Messman">Messman</option>
                                    <option value="Deck Man">Deck Man</option>
                                    <option value="IT">IT</option>
                                    <option value="Offshore Vessel">Offshore Vessel</option>
                                    <option value="Fishing Vessel">Fishing Vessel</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="rank" class="form-label">Rank*</label>
                                <input type="text" class="form-control" id="rank" value="Cadet">
                            </div>
                            <div class="col">
                                <label for="contractLength" class="form-label">Contract Length*</label>
                                <input type="text" class="form-control" id="contractLength" value="9 months">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="vesselType" class="form-label">Vessel type*</label>
                                <input type="text" class="form-control" id="vesselType" placeholder="Vessel Type">
                            </div>
                            <div class="col">
                                <label for="jobRequirements" class="form-label">Job requirements*</label>
                                <input type="text" class="form-control job-requirements-input" id="jobRequirements"
                                    value="SSS, PAG-IBIG, PHILHEALTH, PASSBOOK">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="jobDescription" class="form-label">Job Description*</label>
                            <textarea class="form-control" id="jobDescription" rows="4">lorem ipsum........</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </section>
    
    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    
</body>
</html>