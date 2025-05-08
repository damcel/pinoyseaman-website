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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Competency & Certificate</title>
    <style>
        .content-editIcon{
            display: flex;
            justify-content: space-between;
        }

        .uploaded-file-box {
        background-color: #f8f9fa;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        width: 30%;
        }

        .uploaded-file-box a:hover {
        color: #0d6efd;
        text-decoration: underline;
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
            <div class="saved-ctn">
                <a href="saved-jobs.html" class="saved-btn">
                    <i class="fa-solid fa-book-bookmark"></i>
                </a>
            </div>
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="userprofile.php" class="prfl">Profile Settings</a>
                    <a href="#">Logout</a>
                </div>
            </div>
        </section>

        <section class="profile-setup-container">
            <section class="profile-settings">
                <div class="tabs-container">
                    <nav class="tabs">
                        <ul>
                            <li class="tab"><a href="userprofile.php">Account Setting</a></li>
                            <li class="tab"><a href="seafarer-documents.php">Passport & Seamans book</a></li>
                            <li class="tab active"><a href="competency-certificate.php">Competency & Certificates</a></li>
                        </ul>
                    </nav>
                </div>
            </section>

            <!-- certificate section -->

            <?php

            // Database connection
            require_once "db.php";

            // Fetch the certificate type from the database
            $seekerEmail = $_SESSION['seeker_id'];
            $certQuery = "SELECT sc.*, ct.type AS cert_type_name 
                          FROM seaman_certificates sc
                          JOIN certificate_types ct ON sc.cert_type_id = ct.id
                          WHERE sc.seaman_email = ? LIMIT 1";
            $stmt = $conn->prepare($certQuery);
            $stmt->bind_param("s", $seekerEmail);
            $stmt->execute();
            $certResult = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            ?>

            <section class="education-section">
                <h2 class="header-info">Certificate</h2>
                <div class="passport-container">
                    <table class="table-content">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Number</th>
                                <th>Country</th>
                                <th>Date Issue</th>
                                <th>Expiry Date</th>
                                <th>Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($certResult)):  ?>

                                <tr>
                                    <td data-label="Type"><?php echo htmlspecialchars($certResult['cert_type_name']) ?></td>
                                    <td data-label="Number"><?php echo htmlspecialchars($certResult['cert_number']) ?></td>
                                    <td data-label="Country"><?php echo htmlspecialchars($certResult['country']) ?></td>
                                    <td data-label="Date Issue"><?php echo htmlspecialchars($certResult['start_date']) ?></td>
                                    <td data-label="Expiry Date">
                                        <?php echo is_null($certResult['end_date']) ? 'No Expiry' : htmlspecialchars($certResult['end_date']); ?>
                                    </td>
                                    <td class="attachment-cell" data-label="Attachment">
                                        <?php if (!empty($certResult['file_path'])): ?>
                                            <a href="Uploads/Seaman/Certificate/<?php echo htmlspecialchars($certResult['file_path']); ?>" target="_blank" class="text-decoration-none">
                                                View Document
                                            </a>
                                        <?php else: ?>
                                            <span>No Attachment</span>
                                        <?php endif; ?>
                                        <div class="attachment-icons">
                                            <button class="edit-education" type="button" data-bs-toggle="modal" data-bs-target="#edit-certification">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No certificates found.</td>
                            </tr>
                            <?php endif; ?>

                            
                        </tbody>                
                    </table>            
                    <button class="add-document" data-bs-toggle="modal" data-bs-target="#add-certification">+ Add Document</button>
                </div>
            </section>

            <?php

            // Database connection
            require_once "db.php";

            // Fetch the certificate type from the database
            $seekerEmail = $_SESSION['seeker_id'];
            $competenceQuery = "SELECT js.competence, sd.*
                          FROM job_seeker js
                          JOIN seaman_documents sd ON js.email = sd.seaman_email
                          WHERE js.email = ? AND sd.type_of_doc = 'Competence Document' LIMIT 1";
            $stmt = $conn->prepare($competenceQuery);
            $stmt->bind_param("s", $seekerEmail);
            $stmt->execute();
            $competenceResult = $stmt->get_result()->fetch_assoc();

            ?>

            <section class="compe-cert-container">
                <div class="box-container">  
                    <h2 class="header-info">Competence</h2> 
                    <div class="competency-box">
                        <div>
                            <div class="content-editIcon">
                                <p class="experience-content">
                                    <?php echo !empty($competenceResult['competence']) ? htmlspecialchars($competenceResult['competence']) : 'N/A'; ?>
                                </p>
                                <span class="edit-wrapper">
                                    <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#edit-competence">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </span>
                            </div>
                    
                            <!-- Styled uploaded file box -->
                            <div class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                <a href="Uploads/Seaman/Competence/<?php echo htmlspecialchars($competenceResult['doc_url']) ?>" download class="text-decoration-none fw-medium text-dark">
                                    <?php echo !empty($competenceResult['doc_url']) ? htmlspecialchars($competenceResult['doc_url']) : 'Upload file now!'; ?>
                                </a>
                            </div>
                        </div>
                        <hr>
                        <button class="add-cv-btn" data-bs-toggle="modal" data-bs-target="#add-competence">+ Add Document</button>
                    </div>
                </div>
            </section>


            <?php

            // Database connection
            require_once "db.php";

            // Fetch the certificate type from the database
            $seekerEmail = $_SESSION['seeker_id'];
            $meritsQuery = "SELECT js.merits, sd.*
                        FROM job_seeker js
                        JOIN seaman_documents sd ON js.email = sd.seaman_email
                        WHERE js.email = ? AND sd.type_of_doc = 'Merits Document' LIMIT 1";
            $stmt = $conn->prepare($meritsQuery);
            $stmt->bind_param("s", $seekerEmail);
            $stmt->execute();
            $meritsResult = $stmt->get_result()->fetch_assoc();

            ?>

            <section class="compe-cert-container">
                <div class="box-container">  
                    <h2 class="header-info">Merits</h2> 
                    <div class="mertis-box">
                        <div>
                            <div class="content-editIcon">
                                <p class="experience-content">
                                    <?php echo !empty($meritsResult['merits']) ? htmlspecialchars($meritsResult['merits']) : 'N/A'; ?>
                                </p>
                                <span class="edit-wrapper">
                                    <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#edit-merits">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </span>
                            </div>
                    
                            <!-- Styled uploaded file box -->
                            <div class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                <a href="Uploads/Seaman/Merits/<?php echo htmlspecialchars($meritsResult['doc_url']) ?>" download class="text-decoration-none fw-medium text-dark">
                                    <?php echo !empty($meritsResult['doc_url']) ? htmlspecialchars($meritsResult['doc_url']) : 'Upload file now!'; ?>
                                </a>
                            </div>
                        </div>
                        <hr>
                        <button class="add-cv-btn" data-bs-toggle="modal" data-bs-target="#add-merits">+ Add Document</button>
                    </div>
                </div>
            </section>
        </section>

    </main>
    
    <!------------------------- ADD COMPETENCE  ----------------------> 
    <section class="modal fade" id="add-competence" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">COMPETENCE</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/add_seaman_competence.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- LEFT side -->
                            <div class="col-md-6">
                                <label for="competence" class="form-label">Insert Competence</label>
                                <textarea id="competence" name="competence" class="form-control" rows="10" placeholder="Enter notes..."></textarea>
                            </div>

                            <!-- RIGHT side -->
                            <div class="col-md-6 d-flex align-items-center justify-content-center">
                                <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                                    <div class="text-muted mb-2">
                                        <i class="fa fa-upload fa-2x mb-2"></i><br>
                                        Drag and drop files here
                                    </div>
                                    <div>or</div>
                                    <button type="button" class="btn btn-primary mt-2" id="browse-btn">Browse File</button>
                                    <!-- File input for file upload -->
                                    <input type="file" id="file-upload" name="file_upload" class="d-none" accept=".pdf,.doc,.docx">
                                    <!-- Display the selected file name -->
                                    <div id="file-name" class="mt-2 text-muted">No file selected</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                        <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </section>  

    <!------------------------ EDIT COMPETENCE  ---------------------->
    <section class="modal fade" id="edit-competence" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">UPDATE COMPETENCE</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/edit_seaman_competence.php" method="POST" enctype="multipart/form-data">
        
                <div class="modal-body">
                
                    <div class="row g-3">
                    <!-- LEFT side -->
                    <div class="col-md-6">
                        <label for="editCompetence" class="form-label">Edit Competence</label>
                        <textarea id="editCompetence" name="editCompetence" class="form-control" rows="10" placeholder="Enter notes..."><?php echo !empty($competenceResult['competence']) ? htmlspecialchars($competenceResult['competence']) : 'N/A'; ?></textarea>
                    </div>

                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                            <div class="text-muted mb-2">
                                <i class="fa fa-upload fa-2x mb-2"></i><br>
                                Drag and drop files here
                            </div>
                            <div>or</div>
                            <button type="button" class="btn btn-primary mt-2" id="edit-browse-btn">Browse File</button>
                            <!-- File input for file upload -->
                            <input type="file" id="edit_file_upload" name="edit_file_upload" class="d-none" accept=".pdf,.doc,.docx">
                            <!-- Display the selected file name -->
                            <div id="edit-file-name" class="mt-2 text-muted"><?php echo htmlspecialchars($competenceResult['doc_url']) ?></div>
                        </div>
                    </div>
                    </div>
                
                </div>
                <div class="modal-footer d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-outline-danger flex-fill py-2" name="delete" value="1" onclick="return confirmDeletion()">
                        <i class="fa-solid fa-trash me-2"></i>Delete
                    </button>
                </div>

                </form>

            </div>
        </div>
    </section>

     <!------------------------- ADD MERITS  ---------------------->
     <section class="modal fade" id="add-merits" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">MERITS</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/add_seaman_merits.php" method="POST" enctype="multipart/form-data">
        
                <div class="modal-body">
                
                    <div class="row g-3">
                    <!-- LEFT side -->
                    <div class="col-md-6">
                        <label for="merits" class="form-label">Insert Merits</label>
                        <textarea id="merits" name="merits" class="form-control" rows="10" placeholder="Enter notes..."></textarea>
                    </div>

                    <!-- RIGHT side -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                            <div class="text-muted mb-2">
                                <i class="fa fa-upload fa-2x mb-2"></i><br>
                                Drag and drop files here
                            </div>
                            <div>or</div>
                            <button type="button" class="btn btn-primary mt-2" id="merits-browse-btn">Browse File</button>
                            <!-- File input for file upload -->
                            <input type="file" id="merits-file-upload" name="merits_file_upload" class="d-none" accept=".pdf,.doc,.docx">
                            <!-- Display the selected file name -->
                            <div id="merits-file-name" class="mt-2 text-muted">No file selected</div>
                        </div>
                    </div>
                
                </div>
                <div class="modal-footer d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                </div>

                </form>

            </div>
        </div>
    </section>  

    <!------------------------ EDIT MERITS  ---------------------->
    <section class="modal fade" id="edit-merits" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">UPDATE MERITS</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/edit_seaman_merits.php" method="POST" enctype="multipart/form-data">
        
                <div class="modal-body">
                
                    <div class="row g-3">
                    <!-- LEFT side -->
                    <div class="col-md-6">
                        <label for="editMerits" class="form-label">Edit merits</label>
                        <textarea id="editMerits" name="editMerits" class="form-control" rows="10" placeholder="Enter notes..."><?php echo !empty($meritsResult['merits']) ? htmlspecialchars($meritsResult['merits']) : 'N/A'; ?></textarea>
                    </div>
        
                    <!-- RIGHT side -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                            <div class="text-muted mb-2">
                                <i class="fa fa-upload fa-2x mb-2"></i><br>
                                Drag and drop files here
                            </div>
                            <div>or</div>
                            <button type="button" class="btn btn-primary mt-2" id="merits-edit-browse-btn">Browse File</button>
                            <!-- File input for file upload -->
                            <input type="file" id="merits_edit_file_upload" name="merits_edit_file_upload" class="d-none" accept=".pdf,.doc,.docx">
                            <!-- Display the selected file name -->
                            <div id="merits-edit-file-name" class="mt-2 text-muted"><?php echo htmlspecialchars($meritsResult['doc_url']) ?></div>
                        </div>
                    </div>
                    </div>
                
                </div>
                <div class="modal-footer d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-outline-danger flex-fill py-2" name="delete" value="1" onclick="return confirmDeletion()">
                        <i class="fa-solid fa-trash me-2"></i>Delete
                    </button>
                </div>

                </form>

            </div>
        </div>
    </section>

    <!-------- CERTIFICATE NEW ---------->
    <section class="modal fade" id="add-certification" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Certification</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/add_seaman_cert.php" method="POST" enctype="multipart/form-data">

                <div class="modal-body">
                
                    <div class="mb-3">
                        <label for="cert_type" class="form-label">Document Type<span class="text-danger">*</span></label>
                        <select class="form-select" id="cert_type" name="cert_type" required>
                            <option value="" selected disabled>Select document type...</option>
                            <?php

                            // Database connection
                            require_once "db.php";

                            // Fetch certificate types from database
                            $certificateQuery = "SELECT id, type FROM certificate_types ORDER BY type ASC";
                            $certificateResult = $conn->query($certificateQuery);
                            
                            if ($certificateResult && $certificateResult->num_rows > 0) {
                                while ($row = $certificateResult->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row['id']) . '">' 
                                    . htmlspecialchars($row['type']) . '</option>';
                                }
                            } else {
                                echo '<option value="" disabled>No certificate types found</option>';
                            }
                            ?>
                        </select>
                    </div>
        
                    <div class="mb-3">
                        <label for="certNumber" class="form-label">Document Number:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="certNumber" name="certNumber" placeholder="123-456-789">
                    </div>

                    <div class="mb-3">
                        <label for="certCountry" class="form-label">Country<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="certCountry" name="certCountry" placeholder="pelepens">
                    </div>
        
                    <div class="row mb-3">
                        <div class="col">
                            <label for="certfromDate" class="form-label">Start Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="certfromDate" name="certfromDate">
                        </div>
                        <div class="col">
                            <label for="certtoDate" class="form-label">End Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="certtoDateAdd" name="certtoDateAdd">
                        </div>
                        <div class="col-12 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="unlimitedCheckboxAdd" name="unlimitedCheckboxAdd">
                                <label class="form-check-label" for="unlimitedCheckboxAdd">No Expiry</label>
                            </div>
                        </div>
                    </div>
        
                    <div class="mb-3">
                        <label for="certUpload" class="form-label">Add Document (PDF or Word)</label>
                        <input type="file" class="form-control" id="certUpload" name="certUpload" accept=".pdf,.doc,.docx">
                    </div>
                                
                </div>
                <div class="modal-footer d-flex gap-3">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                </div> 

                </form> 

            </div>
        </div>
    </section>

    <!---------EDIT CERTIFICATE Modal ---------->
    <section class="modal fade" id="edit-certification" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update Certificate</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/edit_seaman_cert.php" method="POST" enctype="multipart/form-data">
        
                <div class="modal-body">
                    
                    <?php
                    require_once "db.php";

                    $selectedCertTypeId = null;

                    if (isset($_SESSION['seeker_id'])) {
                        $seaman_email = $_SESSION['seeker_id'];

                        // Fetch the current cert_type_id for this seaman
                        $stmt = $conn->prepare("SELECT cert_type_id FROM seaman_certificates WHERE seaman_email = ? LIMIT 1");
                        $stmt->bind_param("s", $seaman_email);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $row = $result->fetch_assoc()) {
                            $selectedCertTypeId = $row['cert_type_id'];
                        }

                        $stmt->close();
                    }

                    // Fetch all certificate types
                    $certificateQuery = "SELECT id, type FROM certificate_types ORDER BY type ASC";
                    $certificateResult = $conn->query($certificateQuery);
                    ?>

                    <div class="mb-3">
                        <label for="edit_cert_type" class="form-label">Document Type<span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_cert_type" name="edit_cert_type" required>
                            <option value="" disabled <?= $selectedCertTypeId === null ? 'selected' : '' ?>>Select document type...</option>
                            <?php
                            if ($certificateResult && $certificateResult->num_rows > 0) {
                                while ($row = $certificateResult->fetch_assoc()) {
                                    $selected = ($row['id'] == $selectedCertTypeId) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($row['id']) . '" ' . $selected . '>'
                                        . htmlspecialchars($row['type']) . '</option>';
                                }
                            } else {
                                echo '<option value="" disabled>No certificate types found</option>';
                            }
                            ?>
                        </select>
                    </div>

        
                    <div class="mb-3">
                        <label for="editcertNumber" class="form-label">Document Number:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editcertNumber" name="editcertNumber" placeholder="123-456-789" value="<?php echo htmlspecialchars($certResult['cert_number']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="editcertCountry" class="form-label">Country<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editcertCountry" name="editcertCountry" placeholder="pelepens" value="<?php echo htmlspecialchars($certResult['country']) ?>">
                    </div>
        
                    <div class="row mb-3">
                        <div class="col">
                            <label for="editcertfromDate" class="form-label">Start Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editcertfromDate" name="editcertfromDate" value="<?php echo htmlspecialchars($certResult['start_date']) ?>">
                        </div>
                        <div class="col">
                            <label for="editcerttoDateAdd" class="form-label">End Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editcerttoDateAdd" name="editcerttoDateAdd" value="<?php echo htmlspecialchars($certResult['end_date']) ?>">
                        </div>
                        <div class="col-12 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editunlimitedCheckboxAdd" name="editunlimitedCheckboxAdd" 
                                    <?php echo is_null($certResult['end_date']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="editunlimitedCheckboxAdd">No Expiry</label>
                            </div>
                        </div>
                    </div>
        
                    <div class="mb-3">
                        <label for="editcertUpload" class="form-label">Add Document (PDF or Word)</label>
                        <input type="file" class="form-control" id="editcertUpload" name="editcertUpload" accept=".pdf,.doc,.docx">
                        <small class="form-text text-muted">
                            Current File: <span><?php echo htmlspecialchars($certResult['file_path']) ?></span>
                        </small>
                    </div>
                                                  
                </div>
                <div class="modal-footer d-flex gap-3">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-outline-danger flex-fill py-2" name="delete" value="1" onclick="return confirmDeletion()">
                        <i class="fa-solid fa-trash me-2"></i>Delete
                    </button>
                </div>  

                </form> 

            </div>
        </div>
    </section>

    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script src="script/cert-unlibtn.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>

    <script>
        function confirmDeletion() {
            return confirm("Are you sure you want to delete this record? This action cannot be undone.");
        }

        document.getElementById("browse-btn").addEventListener("click", function() {
            document.getElementById("file-upload").click(); // Trigger file input
        });

        document.getElementById("file-upload").addEventListener("change", function() {
            var fileName = this.files.length > 0 ? this.files[0].name : "No file selected";
            document.getElementById("file-name").textContent = fileName; // Display the selected file name
        });

        document.getElementById("merits-browse-btn").addEventListener("click", function() {
            document.getElementById("merits-file-upload").click(); // Trigger file input
        });

        document.getElementById("merits-file-upload").addEventListener("change", function() {
            var fileName = this.files.length > 0 ? this.files[0].name : "No file selected";
            document.getElementById("merits-file-name").textContent = fileName; // Display the selected file name
        });

        document.getElementById("edit-browse-btn").addEventListener("click", function() {
            document.getElementById("edit_file_upload").click(); // Trigger file input
        });

        document.getElementById("edit_file_upload").addEventListener("change", function() {
            var fileName = this.files.length > 0 ? this.files[0].name : "No file selected";
            document.getElementById("edit-file-name").textContent = fileName; // Display the selected file name
        });

        document.getElementById("merits-edit-browse-btn").addEventListener("click", function() {
            document.getElementById("merits_edit_file_upload").click(); // Trigger file input
        });

        document.getElementById("merits_edit_file_upload").addEventListener("change", function() {
            var fileName = this.files.length > 0 ? this.files[0].name : "No file selected";
            document.getElementById("merits-edit-file-name").textContent = fileName; // Display the selected file name
        });

    </script>

</body>
</html>