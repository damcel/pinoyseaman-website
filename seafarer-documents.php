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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Passport & Seamans book</title>
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
                <a href="saved-jobs.html" class="saved-btn">
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

        <?php
        // Include the database connection
        include 'db.php';

        // Fetch Seaman Book data for the logged-in user
        $seekerId = $_SESSION['seeker_id'];
        $seamanBookQuery = "SELECT sbook_no, sbook_country, sbook_issued, sbook_valid FROM job_seeker WHERE email = ?";
        $stmt = $conn->prepare($seamanBookQuery);
        $stmt->bind_param("s", $seekerId);
        $stmt->execute();
        $seamanBookResult = $stmt->get_result();
        $seamanBook = $seamanBookResult->fetch_assoc();

        // Fetch education details from the database
        $sBookQuery = "SELECT * 
                FROM seaman_documents
                WHERE seaman_email = ? AND type_of_doc = 'Seaman Book'";
        $sBookStmt = $conn->prepare($sBookQuery);
        $sBookStmt->bind_param("s", $seekerId);
        $sBookStmt->execute();
        $sBookResult = $sBookStmt->get_result();
        $sBookAttachment = $sBookResult->fetch_assoc();
        ?>

        <section class="profile-setup-container">
            <section class="profile-settings">
                <div class="tabs-container">
                    <nav class="tabs">
                        <ul>
                            <li class="tab"><a href="userprofile.php">Account Setting</a></li>
                            <li class="tab active"><a href="seafarer-documents.php">Passport & Seamans book</a></li>
                            <li class="tab"><a href="competency-certificate.php">Competency & Certificates</a></li>
                        </ul>
                    </nav>
                </div>
            </section>
            <section class="education-section">
                <h2 class="header-info">Seaman Book</h2>
                <div class="seamans-book-container">
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
                            <?php if (!empty($seamanBook)): ?>
                                <tr>
                                    <td data-label="Type">Seaman Book</td>
                                    <td data-label="Number"><?php echo !empty($seamanBook['sbook_no']) ? htmlspecialchars($seamanBook['sbook_no']) : 'N/A'; ?></td>
                                    <td data-label="Country"><?php echo !empty($seamanBook['sbook_country']) ? htmlspecialchars($seamanBook['sbook_country']) : 'N/A'; ?></td>
                                    <td data-label="Date Issue"><?php echo !empty($seamanBook['sbook_issued']) ? htmlspecialchars($seamanBook['sbook_issued']) : 'N/A'; ?></td>
                                    <td data-label="Expiry Date"><?php echo !empty($seamanBook['sbook_valid']) ? htmlspecialchars($seamanBook['sbook_valid']) : 'N/A'; ?></td>
                                    <td class="attachment-cell" data-label="Actions">
                                        <?php if (!empty($sBookAttachment['doc_url'])): ?>
                                            <a href="Uploads/Seaman/SBook/<?php echo htmlspecialchars($sBookAttachment['doc_url']); ?>" target="_blank" class="text-decoration-none">
                                                View Document
                                            </a>
                                        <?php else: ?>
                                            <span>No Attachment</span>
                                        <?php endif; ?>
                                        <div class="attachment-icons">
                                            <button class="edit-education" type="button" data-bs-toggle="modal" data-bs-target="#edit-seamans-book">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No Seaman Book records found. Add now!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>                
                    </table>      
                    <button class="add-document" data-bs-toggle="modal" data-bs-target="#add-seamans-book">+ Add Document</button>      
                </div>
            </section>

            <?php
            // Include the database connection
            include 'db.php';

            // Fetch Seaman Book data for the logged-in user
            $seekerId = $_SESSION['seeker_id'];
            $passportQuery = "SELECT passport_no, passport_country, passport_issued, passport_valid FROM job_seeker WHERE email = ?";
            $stmt = $conn->prepare($passportQuery);
            $stmt->bind_param("s", $seekerId);
            $stmt->execute();
            $passportResult = $stmt->get_result();
            $passport = $passportResult->fetch_assoc();

            // Fetch education details from the database
            $passportQuery = "SELECT * 
                    FROM seaman_documents
                    WHERE seaman_email = ? AND type_of_doc = 'Seaman Passport'";
            $passportStmt = $conn->prepare($passportQuery);
            $passportStmt->bind_param("s", $seekerId);
            $passportStmt->execute();
            $passportResult = $passportStmt->get_result();
            $passportAttachment = $passportResult->fetch_assoc();
            ?>

            <section class="education-section">
                <h2 class="header-info">Passport</h2>
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
                            <?php if (!empty($passport)): ?>
                                <tr>
                                    <td data-label="Type">Passport</td>
                                    <td data-label="Number"><?php echo !empty($passport['passport_no']) ? htmlspecialchars($passport['passport_no']) : 'N/A'; ?></td>
                                    <td data-label="Country"><?php echo !empty($passport['passport_country']) ? htmlspecialchars($passport['passport_country']) : 'N/A'; ?></td>
                                    <td data-label="Date Issue"><?php echo !empty($passport['passport_issued']) ? htmlspecialchars($passport['passport_issued']) : 'N/A'; ?></td>
                                    <td data-label="Expiry Date"><?php echo !empty($passport['passport_valid']) ? htmlspecialchars($passport['passport_valid']) : 'N/A'; ?></td>
                                    <td class="attachment-cell" data-label="Actions">
                                        <?php if (!empty($passportAttachment['doc_url'])): ?>
                                            <a href="Uploads/Seaman/Passport/<?php echo htmlspecialchars($passportAttachment['doc_url']); ?>" target="_blank" class="text-decoration-none">
                                                View Document
                                            </a>
                                        <?php else: ?>
                                            <span>No Attachment</span>
                                        <?php endif; ?>
                                        <div class="attachment-icons">
                                            <button class="edit-education" type="button" data-bs-toggle="modal" data-bs-target="#edit-passport">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No Seaman Book records found. Add now!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>              
                    </table>            
                    <button class="add-document" data-bs-toggle="modal" data-bs-target="#add-passport">+ Add Document</button>
                </div>
            </section>

            <?php
            // Fetch Visa data for the logged-in user
            $visaQuery = "SELECT svd. id, svd.visa_no, svd.visa_issued, svd.visa_valid, svd.visa_url, svl.visa_type 
                        FROM seaman_visa_docs svd
                        INNER JOIN seaman_visa_list svl ON svd.visa_type_id = svl.id
                        WHERE svd.seaman_email = ?";
            $stmt = $conn->prepare($visaQuery);
            $stmt->bind_param("s", $seekerId);
            $stmt->execute();
            $visaResult = $stmt->get_result();
            $visaRecords = [];
            if ($visaResult && $visaResult->num_rows > 0) {
                while ($row = $visaResult->fetch_assoc()) {
                    $visaRecords[] = $row;
                }
            }
            ?>

            <section class="education-section">
                <h2 class="header-info">Visa</h2>
                <div class="visa-container">
                    <table class="table-content">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Number</th>
                                <th>Country Visa</th>
                                <th>Date Issue</th>
                                <th>Expiry Date</th>
                                <th>Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($visaRecords)): ?>
                                <?php foreach ($visaRecords as $visa): ?>
                                    <tr>
                                        <td data-label="Type">Visa</td>
                                        <td data-label="Number"><?php echo htmlspecialchars($visa['visa_no']); ?></td>
                                        <td data-label="Country"><?php echo htmlspecialchars($visa['visa_type']); ?></td>
                                        <td data-label="Date Issue"><?php echo htmlspecialchars($visa['visa_issued']); ?></td>
                                        <td data-label="Expiry Date"><?php echo htmlspecialchars($visa['visa_valid']); ?></td>
                                        <td class="attachment-cell" data-label="Attachment">
                                            <div class="attachment-content">
                                                <?php if (!empty($visa['visa_url'])): ?>
                                                    <a href="Uploads/Seaman/Visa/<?php echo htmlspecialchars($visa['visa_url']); ?>" target="_blank" class="text-decoration-none">
                                                        View Document
                                                    </a>
                                                <?php else: ?>
                                                    <span>No Attachment</span>
                                                <?php endif; ?>
                                                <div class="attachment-icons">
                                                    <button class="edit-visa" type="button" data-bs-toggle="modal" data-bs-target="#edit-visa"
                                                        data-id="<?php echo htmlspecialchars($visa['id']); ?>"
                                                        data-type="<?php echo htmlspecialchars($visa['visa_type']); ?>"
                                                        data-number="<?php echo htmlspecialchars($visa['visa_no']); ?>"
                                                        data-issued="<?php echo htmlspecialchars($visa['visa_issued']); ?>"
                                                        data-valid="<?php echo htmlspecialchars($visa['visa_valid']); ?>"
                                                        data-url="<?php echo htmlspecialchars($visa['visa_url']); ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No Visa records found. Add now!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>                
                    </table>            
                    <button class="add-document" data-bs-toggle="modal" data-bs-target="#add-visa">+ Add Document</button>
                </div>
            </section>
    </main>

    <!---------SEAMANS BOOK MODAL ---------->
    <section class="modal fade" id="add-seamans-book" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Seamans Book</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/add_SBook.php" method="POST" enctype="multipart/form-data">

                <div class="modal-body">
                
                    <div class="mb-3">
                        <label for="country" class="form-label">Country<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="country" name="country" placeholder="Philippines">
                    </div>
        
                    <div class="mb-3">
                        <label for="seamanNumber" class="form-label">Seamans Book ID:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="seamanNumber" name="seamanNumber" placeholder="123-456-789">
                    </div>
        
                    <div class="row mb-3">
                    <div class="col">
                        <label for="fromDate" class="form-label">Issuance Date: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fromDate" id="fromDate">
                    </div>
                    <div class="col">
                        <label for="toDate" class="form-label">Expiration Date: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="toDate" id="toDate">
                    </div>
                    </div>
        
                    <div class="mb-3">
                        <label for="documentUpload" class="form-label">Add Document (PDF or Word)</label>
                        <input type="file" class="form-control" id="documentUpload" name="documentUpload" accept=".pdf,.doc,.docx">
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

    
    <!---------EDIT SEAMANS BOOK Modal ---------->
    <section class="modal fade" id="edit-seamans-book" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update Seamans Book</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/edit_SBook.php" method="POST" enctype="multipart/form-data">
        
                <div class="modal-body">
                    
                        <div class="mb-3">
                            <label for="editCountry" class="form-label">Country<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editCountry" name="editCountry" placeholder="Philippines" value="<?php echo !empty($seamanBook['sbook_country']) ? htmlspecialchars($seamanBook['sbook_country']) : ''; ?>">
                        </div>
            
                        <div class="mb-3">
                            <label for="editseamanNumber" class="form-label">Seamans Book ID:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editseamanNumber" name="editseamanNumber" placeholder="123-456-789" value="<?php echo !empty($seamanBook['sbook_no']) ? htmlspecialchars($seamanBook['sbook_no']) : ''; ?>">
                        </div>
            
                        <div class="row mb-3">
                        <div class="col">
                            <label for="editfromDate" class="form-label">Start Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editfromDate" name="editfromDate" value="<?php echo !empty($seamanBook['sbook_issued']) ? htmlspecialchars($seamanBook['sbook_issued']) : ''; ?>">
                        </div>
                        <div class="col">
                            <label for="edittoDate" class="form-label">End Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edittoDate" name="edittoDate" value="<?php echo !empty($seamanBook['sbook_valid']) ? htmlspecialchars($seamanBook['sbook_valid']) : ''; ?>">
                        </div>
                        </div>
            
                        <div class="mb-3">
                            <label for="editDocumentUpload" class="form-label">Add Document (PDF or Word)</label>
                            <input type="file" class="form-control" id="editDocumentUpload" name="editDocumentUpload" accept=".pdf,.doc,.docx">
                            <small class="form-text text-muted">
                                <?php if (!empty($sBookAttachment['doc_url'])): ?>
                                    Current File: 
                                    <a href="Uploads/Seaman/SBook/<?php echo htmlspecialchars($sBookAttachment['doc_url']); ?>" target="_blank">View Document</a>
                                <?php else: ?>
                                    No file uploaded.
                                <?php endif; ?>
                            </small>
                        </div>
                                                  
                </div>
                <div class="modal-footer d-flex gap-3">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete" value="1" class="btn btn-outline-danger flex-fill py-2" onclick="return confirmDeletion();">
                        <i class="fa-solid fa-trash me-2"></i>Delete
                    </button>
                </div>  

                </form> 

            </div>
        </div>
    </section>

     <!---------PASSPORT MODAL ---------->
     <section class="modal fade" id="add-passport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Passport</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/add_passport.php" method="POST" enctype="multipart/form-data">

                <div class="modal-body">
                
                    <div class="mb-3">
                        <label for="passportCountry" class="form-label">Country<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="passportCountry" name="passportCountry" placeholder="Philippines">
                    </div>
        
                    <div class="mb-3">
                        <label for="passportID" class="form-label">Passport ID:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="passportID" name="passportID" placeholder="123-456-789">
                    </div>
        
                    <div class="row mb-3">
                    <div class="col">
                        <label for="passportFromDate" class="form-label">Start Date: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="passportFromDate" name="passportFromDate">
                    </div>
                    <div class="col">
                        <label for="passportToDate" class="form-label">End Date: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="passportToDate" name="passportToDate">
                    </div>
                    </div>
        
                    <div class="mb-3">
                        <label for="passportDocumentUpload" class="form-label">Add Document (PDF or Word)</label>
                        <input type="file" class="form-control" id="passportDocumentUpload" name="passportDocumentUpload" accept=".pdf,.doc,.docx">
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

    
    <!---------EDIT EDUCATION Modal ---------->
    <section class="modal fade" id="edit-passport" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update Passport</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/edit_passport.php" method="POST" enctype="multipart/form-data">
        
                <div class="modal-body">
                    
                        <div class="mb-3">
                            <label for="editPassCountry" class="form-label">Country<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editPassCountry" name="editPassCountry" placeholder="Philippines" value="<?php echo !empty($passport['passport_country']) ? htmlspecialchars($passport['passport_country']) : ''; ?>">
                        </div>
            
                        <div class="mb-3">
                            <label for="editPassportID" class="form-label">Passport ID:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editPassportID" name="editPassportID" placeholder="123-456-789" value="<?php echo !empty($passport['passport_no']) ? htmlspecialchars($passport['passport_no']) : ''; ?>">
                        </div>
            
                        <div class="row mb-3">
                        <div class="col">
                            <label for="editPassFromDate" class="form-label">Start Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editPassFromDate" name="editPassFromDate" value="<?php echo !empty($passport['passport_issued']) ? htmlspecialchars($passport['passport_issued']) : ''; ?>">
                        </div>
                        <div class="col">
                            <label for="editPassToDate" class="form-label">End Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editPassToDate" name="editPassToDate" value="<?php echo !empty($passport['passport_valid']) ? htmlspecialchars($passport['passport_valid']) : ''; ?>">
                        </div>
                        </div>
            
                        <div class="mb-3">
                            <label for="editPassDocumentUpload" class="form-label">Add Document (PDF or Word)</label>
                            <input type="file" class="form-control" id="editPassDocumentUpload" name="editPassDocumentUpload" accept=".pdf,.doc,.docx">
                            <small class="form-text text-muted">
                                <?php if (!empty($passportAttachment['doc_url'])): ?>
                                    Current File: 
                                    <a href="Uploads/Seaman/Passport/<?php echo htmlspecialchars($passportAttachment['doc_url']); ?>" target="_blank">View Document</a>
                                <?php else: ?>
                                    No file uploaded.
                                <?php endif; ?>
                            </small>
                        </div>
                                                
                </div>
                <div class="modal-footer d-flex gap-3">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete" value="1" class="btn btn-outline-danger flex-fill py-2" onclick="return confirmDeletion();">
                        <i class="fa-solid fa-trash me-2"></i>Delete
                    </button>
                </div> 
                
                </form>   

            </div>
        </div>
    </section>

    <!---------VISA MODAL ---------->
    <section class="modal fade" id="add-visa" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Visa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                <form action="includes/add_visa.php" method="POST" enctype="multipart/form-data">

                    <?php
                    // Fetch visa types from the seaman_visa_list table
                    $visaTypesQuery = "SELECT id, visa_type FROM seaman_visa_list";
                    $visaTypesResult = $conn->query($visaTypesQuery);
                    $visaTypes = [];
                    if ($visaTypesResult && $visaTypesResult->num_rows > 0) {
                        while ($row = $visaTypesResult->fetch_assoc()) {
                            $visaTypes[] = $row;
                        }
                    }
                    ?>
        
                    <div class="mb-3">
                        <label for="visaType" class="form-label">Visa Type<span class="text-danger">*</span></label>
                        <select class="form-control" id="visaType" name="visaType" required>
                            <option value="" disabled selected>Select Visa Type</option>
                            <?php foreach ($visaTypes as $visa): ?>
                                <option value="<?php echo htmlspecialchars($visa['id']); ?>">
                                    <?php echo htmlspecialchars($visa['visa_type']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="visaNumber" class="form-label">Visa Number<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="visaNumber" name="visaNumber" placeholder="123-456-789">
                    </div>
        
                    <div class="row mb-3">
                    <div class="col">
                        <label for="visaFromDate" class="form-label">Start date: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="visaFromDate" name="visaFromDate">
                    </div>
                    <div class="col">
                        <label for="visaToDate" class="form-label">End Date: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="visaToDate" name="visaToDate">
                    </div>
                    </div>
        
                    <div class="mb-3">
                        <label for="visaDocumentUpload" class="form-label">Add Document (PDF or Word)</label>
                        <input type="file" class="form-control" id="visaDocumentUpload" name="visaDocumentUpload" accept=".pdf,.doc,.docx">
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

    
    <!---------EDIT VISA Modal ---------->
    <section class="modal fade" id="edit-visa" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update Visa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="includes/edit_visa.php" method="POST" enctype="multipart/form-data">
        
                <div class="modal-body">

                    <input type="hidden" id="editVisaId" name="visaId">
                    <div class="mb-3">
                        <label for="editVisaType" class="form-label">Visa Type<span class="text-danger">*</span></label>
                        <select class="form-control" id="editVisaType" name="visaType" required>
                            <option value="" disabled>Select Visa Type</option>
                            <?php foreach ($visaTypes as $visa): ?>
                                <option value="<?php echo htmlspecialchars($visa['visa_type']); ?>">
                                    <?php echo htmlspecialchars($visa['visa_type']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
            
                    <div class="mb-3">
                        <label for="editVisaNumber" class="form-label">Visa Number:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editVisaNumber" name="visaNumber" placeholder="123-456-789">
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="editVisaFromDate" class="form-label">Start Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editVisaFromDate" name="visaFromDate">
                        </div>
                        <div class="col">
                            <label for="editVisaToDate" class="form-label">End Date: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editVisaToDate" name="visaToDate">
                        </div>
                    </div>
            
                    <div class="mb-3">
                        <label for="editVisaDocumentUpload" class="form-label">Add Document (PDF or Word)</label>
                        <input type="file" class="form-control" id="editVisaDocumentUpload" name="visaDocumentUpload" accept=".pdf,.doc,.docx">
                        <small class="form-text text-muted">
                            Current File: <span id="currentVisaFile"></span>
                        </small>
                    </div>                     
                </div>
                <div class="modal-footer d-flex gap-3">
                    <button type="submit" class="btn btn-primary flex-fill py-2">Save</button>
                    <button type="button" class="btn btn-outline-secondary flex-fill py-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-outline-danger flex-fill py-2" name="delete" value="1" onclick="return confirmDeletion();">
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
     <!-- Bootstrap JS with Popper (near the end of body) -->
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <script>
        function confirmDeletion() {
            return confirm("Are you sure you want to delete this record? This action cannot be undone.");
        }

        document.addEventListener("DOMContentLoaded", function () {
            const editVisaButtons = document.querySelectorAll(".edit-visa");
            const editVisaModal = document.getElementById("edit-visa");

            editVisaButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const visaId = this.getAttribute("data-id");
                    const visaType = this.getAttribute("data-type");
                    const visaNumber = this.getAttribute("data-number");
                    const visaIssued = this.getAttribute("data-issued");
                    const visaValid = this.getAttribute("data-valid");
                    const visaUrl = this.getAttribute("data-url");

                    // Populate modal fields
                    document.getElementById("editVisaId").value = visaId;
                    document.getElementById("editVisaType").value = visaType;
                    document.getElementById("editVisaNumber").value = visaNumber;
                    document.getElementById("editVisaFromDate").value = visaIssued;
                    document.getElementById("editVisaToDate").value = visaValid;
                    document.getElementById("currentVisaFile").textContent = visaUrl ? visaUrl : "No file uploaded";
                });
            });
        });

    </script>
</body>
</html>