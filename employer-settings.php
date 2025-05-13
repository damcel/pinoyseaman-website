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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Profile Settings</title>
</head>
<body>
    
    <!-- Sidebar -->
    <?php include 'components/employer_aside.php'; ?>
    
    <main class="dashboard-container">
        <?php if (!$isVerified): ?>
            <div class="text-center mt-5">
                <h1>Your account is not yet verified.</h1>
                <p>Please wait for the admin to verify your account. You will be notified via email once your account is verified.</p>
            </div>
        <?php else: ?>

        <?php include 'components/employer_header.php'; ?>

        <section class="job-list-container">
            <div class="job-search-container">
                <section class="company-profile-container">
                    <div class="company-cover">
                        <div class="company-profile-box">
                            <img src="<?php echo $logoPath; ?>" alt="image">
                        </div>
                    </div>
                
                    <article class="company-info-box">
                        <!-- Moved Company Name, Rating, and Review Button inside company-info-box -->
                        <header class="company-header">
                            <div class="company-name">
                                <h2><?php echo htmlspecialchars($row['company']) ?></h2>
                                
                            </div>
                            <button class="company-edit-icon" aria-label="Edit Tanker" data-bs-toggle="modal" data-bs-target="#edit-company-profile"><i class="fa-solid fa-pen-to-square"></i></button>
                        </header>
                
                        <h3>Company Overview</h3>
                
                        <dl class="company-details">
                            <div class="info-item">
                                <dt>Website</dt>
                                <dd><a href="<?php echo htmlspecialchars($row['website']) ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($row['website']) ?></a></dd>
                            </div>

                            <div class="info-item">
                                <dt>Contact Person</dt>
                                <dd><?php echo !empty($row['contact']) ? htmlspecialchars($row['contact']) : 'No assigned'; ?></dd>
                            </div>
                
                            <div class="info-item">
                                <dt>Phone</dt>
                                <dd><?php echo !empty($row['phone']) ? htmlspecialchars($row['phone']) : 'No assigned'; ?></dd>
                            </div>
            
                            <div class="info-item">
                                <dt>Email</dt>
                                <dd><?php echo !empty($row['email']) ? htmlspecialchars($row['email']) : 'No assigned'; ?></dd>
                            </div>
                
                            <div class="info-item">
                                <dt>Address</dt>
                                <dd><?php echo !empty($row['address']) ? htmlspecialchars($row['address']) : 'No assigned'; ?></dd>
                            </div>
                        </dl>
                
                        <section class="company-description">
                            <p>
                                <?php echo !empty($row['company_profile']) ? nl2br(htmlspecialchars($row['company_profile'])) : 'No description available'; ?>
                            </p>
                        </section>
                    </article>
                </section>
              
                <section class="account-settings-container">
                    <div class="settings-card">
                        <h2>Account Settings</h2>
                        <ul>
                          <li>
                              <span class="modal-link" data-bs-toggle="modal" data-bs-target="#email-modal">
                              Change Email
                              </span>
                          </li>
                          <li>
                              <span class="modal-link" data-bs-toggle="modal" data-bs-target="#pwd-modal">
                              Change Password     
                              </span>
                          </li>
                        </ul>
                      <div class="settings-item">
                        <div>
                          <h2>Pinoyseaman updates</h2>
                          <p>Updates and news from Pinoyseaman</p>
                        </div>
                        <label class="switch">
                          <input type="checkbox">
                          <span class="slider"></span>
                        </label>
                      </div>
                  
                      <!-- <div class="settings-item">
                        <div>
                          <h2>Job list updates</h2>
                          <p>Your subscription to new jobs on the site</p>
                          <p>No alerts available.</p>
                        </div>
                      </div> -->
                    </div>
                </section>

                <section class="terms-conditions-section">
                    <div class="terms-card">
                      <h2>Terms and Conditions</h2>
                      <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">User Agreement</a></li>
                      </ul>
                    </div>
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

    <!-- Modal -->
    <section class="modal fade" id="email-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="includes/employer_update_email.php" method="POST">
                    <h2 class="text-center mb-4">Change Email</h2>
                  
                    <div class="mb-3">
                      <label for="currentEmail" class="form-label fw-semibold">Current Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="currentEmail" name="currentEmail" placeholder="Current Email" required>
                    </div>
                  
                    <div class="mb-3">
                      <label for="newEmail" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="newEmail" name="newEmail" placeholder="New Email" required>
                    </div>
                  
                    <div class="mb-3">
                      <label for="confirmEmail" class="form-label fw-semibold">Email confirmation <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="confirmEmail" name="confirmEmail" placeholder="Email Confirmation" required>
                    </div>
                  
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form> 
            </div>
        </div>
        </div>
    </section>

    <!-- EDIt COMPANY PROFILE MODAL -->
    <section class="modal fade" id="edit-company-profile" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Edit Company Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/employer_update_profile.php" method="POST" auto-complete="off" enctype="multipart/form-data">

                <section class="modal-body">
                    <div class="container-fluid">
                        
                            <div class="mb-3 text-center">
                                <label for="jobImage" class="form-label d-block">Company Profile</label>
                                <div class="upload-image">
                                    <!-- File input -->
                                    <input type="file" id="jobImage" name="company_logo" class="form-control d-none" accept=".jpg, .jpeg, .png">
                                    <div class="upload-box" onclick="document.getElementById('jobImage').click();">
                                        <p id="fileName">Upload Vessel or Company Image</p>
                                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <!-- Name Row -->
                                <div class="col-md-4 col-sm-12">
                                    <label for="companyName" class="form-label">Company Name:</label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" value="<?php echo htmlspecialchars($row['company']) ?>">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label for="companyWebsite" class="form-label">Website:</label>
                                    <input type="text" class="form-control" id="companyWebsite" name="companyWebsite" value="<?php echo htmlspecialchars($row['website']) ?>">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label for="contactPerson" class="form-label">Contact Person:</label>
                                    <input type="text" class="form-control" id="contactPerson" name="contactPerson" value="<?php echo htmlspecialchars($row['contact']) ?>">
                                </div>
                        
                                <!-- Second Row -->
                                <div class="col-md-6 col-sm-12">
                                    <label for="companyPhone" class="form-label">Phone:</label>
                                    <input type="number" class="form-control" id="companyPhone" name="companyPhone" value="<?php echo htmlspecialchars($row['phone']) ?>">
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label for="companyAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="companyAddress" name="companyAddress" value="<?php echo htmlspecialchars($row['address']) ?>">
                                </div>
                        
                                <div class="mb-3">
                                    <label for="aboutCompany" class="form-label">About Company</label>
                                    <textarea class="form-control" id="aboutCompany" name="aboutCompany" rows="4" placeholder="Company profile and story"></textarea>
                                </div>
                            </div>
                        
                    </div>                  
                </section>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <section class="modal fade" id="pwd-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="includes/employer_update_password.php" method="POST">
                    <h2 class="text-center mb-4">Change Password</h2>
                  
                    <div class="mb-3">
                      <label for="currentPassword" class="form-label fw-semibold">Current password <span class="text-danger">*</span></label>
                      <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Current Password" required>
                    </div>
                  
                    <div class="mb-3">
                      <label for="newPassword" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                      <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="New Password" required>
                    </div>
                  
                    <div class="mb-3">
                      <label for="confirmPassword" class="form-label fw-semibold">Password confirmation <span class="text-danger">*</span></label>
                      <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Password Confirmation" required>
                    </div>
                  
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>                  
            </div>
        </div>
        </div>
    </section>

    
    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('jobImage');
            const fileNameDisplay = document.getElementById('fileName');

            // Listen for file selection
            fileInput.addEventListener('change', function () {
                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = fileInput.files[0].name; // Display the selected file name
                } else {
                    fileNameDisplay.textContent = 'Upload Vessel or Company Image'; // Reset if no file is selected
                }
            });
        });
    </script>
</body>
</html>