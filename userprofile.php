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
$seekerId = $_SESSION['seeker_id'];
$userQuery = "SELECT * FROM job_seeker WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $seekerId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch seaman documents from the database
$documentsQuery = "SELECT * FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = 'Seagoing Experience File'";
$documentsStmt = $conn->prepare($documentsQuery);
$documentsStmt->bind_param("s", $seekerId);
$documentsStmt->execute();
$documentsResult = $documentsStmt->get_result();
$documentsCount = $documentsResult->num_rows;
$document = $documentsResult->fetch_assoc();
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
                    <a href="saved.html" class="prfl">Profile Settings</a>
                    <a href="includes/logout.php">Logout</a>
                </div>
            </div>
        </section>

        <section class="profile-setup-container">
            <section class="profile-settings">
                <div class="tabs-container">
                    <nav class="tabs">
                        <ul>
                            <li class="tab active"><a href="userprofile.php">Account Setting</a></li>
                            <li class="tab"><a href="seafarer-documents.php">Passport & Seamans book</a></li>
                            <li class="tab"><a href="competency-certificate.php">Competency & Certificates</a></li>
                        </ul>
                    </nav>
                </div>
            </section>
            <section class="profile-section">
                <!-- Added Header Section -->
                <div class="profile-header">
                    <i class="fa-solid fa-user"></i>
                    <h3>
                        <?php 
                        echo htmlspecialchars($user['first_name']); 
                        if (!empty($user['middle_name'])) {
                            echo ' ' . htmlspecialchars(substr($user['middle_name'], 0, 1)) . '.';
                        }
                        echo ' ' . htmlspecialchars($user['last_name']); 
                        ?>
                    </h3>
                    <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>">
                        <i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?>
                    </a>
                    <a href="">
                        <i class="fa-solid fa-phone"></i> 
                        <span><?php echo htmlspecialchars($user['cellphone']) ?></span>
                    </a>
                </div>
            
                <div class="profile-container">
                    <div class="profile-picture">
                        <?php if (!empty($user['user_photo'])): ?>
                            <!-- Display the user's photo -->
                            <img src="Uploads/Seaman/User-Photo/<?php echo htmlspecialchars($user['user_photo']); ?>" alt="User Photo" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <!-- Add a button to change the photo -->
                            <button class="btn btn-outline-primary mt-2" onclick="document.getElementById('upload-photo').click()">Change Photo</button>
                            <form id="uploadPhotoForm" action="includes/upload_user_photo.php" method="POST" enctype="multipart/form-data" style="display: none;">
                                <input type="file" id="upload-photo" name="userPhoto" onchange="document.getElementById('uploadPhotoForm').submit();">
                            </form>
                        <?php else: ?>
                            <!-- Show the upload form if no photo exists -->
                            <form id="uploadPhotoForm" action="includes/upload_user_photo.php" method="POST" enctype="multipart/form-data">
                                <label for="upload-photo">
                                    <div class="upload-box">
                                        <p>Upload your photo</p>
                                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                    </div>
                                </label>
                                <input type="file" id="upload-photo" name="userPhoto" hidden onchange="document.getElementById('uploadPhotoForm').submit();">
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-details">
                        <div class="details-section">
                            <h4>Details</h4>
                            <p><strong>Address:</strong> <?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : 'N/A'; ?></p>
                            <p><strong>Gender:</strong> <?php echo !empty($user['gender']) ? htmlspecialchars($user['gender']) : 'N/A'; ?></p>
                            <?php
                            $dob = !empty($user['birthday']) ? $user['birthday'] : null;
                            if ($dob) {
                                $birthDate = new DateTime($dob);
                                $currentDate = new DateTime();
                                $age = $currentDate->diff($birthDate)->y;
                                echo '<p><strong>Date of birth:</strong> ' . htmlspecialchars($birthDate->format('d M Y')) . ' (' . $age . ' years old)</p>';
                            } else {
                                echo '<p><strong>Date of birth:</strong> N/A</p>';
                            }
                            ?>
                            <p><strong>Place of birth:</strong> <?php echo !empty($user['place_of_birth']) ? htmlspecialchars($user['place_of_birth']) : 'N/A'; ?></p>
                            <p><strong>Marital status:</strong> <?php echo !empty($user['marital_status']) ? htmlspecialchars($user['marital_status']) : 'N/A'; ?></p>
                            <p><strong>Religion:</strong> <?php echo !empty($user['religion']) ? htmlspecialchars($user['religion']) : 'N/A'; ?></p>
                            <p><strong>Nationality:</strong> <?php echo !empty($user['nationality']) ? htmlspecialchars($user['nationality']) : 'N/A'; ?></p>
                            <p><strong>Level of English:</strong> <?php echo !empty($user['english_level']) ? htmlspecialchars($user['english_level']) : 'N/A'; ?></p>
                        </div>
                        <div class="details-section">
                            <h4>Last Employment</h4>
                            <p><strong>Rank:</strong> <?php echo !empty($user['rank']) ? htmlspecialchars($user['rank']) : 'N/A'; ?></p>
                            <p><strong>Vessel:</strong> N/A</p>
                            <p><strong>Type:</strong> N/A</p>
                            <p><strong>Duration:</strong> N/A</p>
                        </div>
                        <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </div>
                </div>
            </section>

            <?php
            // Fetch education details from the database
            $educationQuery = "SELECT id, school_name, field_of_study, educ_level, from_date, to_date, attachment_url 
                            FROM seaman_educ 
                            WHERE email = ?";
            $educationStmt = $conn->prepare($educationQuery);
            $educationStmt->bind_param("s", $seekerId);
            $educationStmt->execute();
            $educationResult = $educationStmt->get_result();
            $educationCount = $educationResult->num_rows;
            ?>

            <section class="education-section">
                <h2 class="header-info">Education</h2>
                <div class="education-container">
                    <table class="table-content">
                        <thead>
                            <tr>
                                <th>School</th>
                                <th>Field of Study</th>
                                <th>Educational Level</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($educationResult->num_rows > 0): ?>
                            <?php while ($education = $educationResult->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="School"><?php echo htmlspecialchars($education['school_name']); ?></td>
                                    <td data-label="Field of Study"><?php echo htmlspecialchars($education['field_of_study']); ?></td>
                                    <td data-label="Educational Level"><?php echo htmlspecialchars($education['educ_level']); ?></td>
                                    <td data-label="Start Date"><?php echo htmlspecialchars($education['from_date']); ?></td>
                                    <td data-label="End Date"><?php echo htmlspecialchars($education['to_date']); ?></td>
                                    <td class="attachment-cell" data-label="Attachment">
                                        <div class="attachment-content">
                                            <?php if (!empty($education['attachment_url'])): ?>
                                                <a href="Uploads/Seaman/Education/<?php echo htmlspecialchars($education['attachment_url']); ?>" target="_blank" class="text-decoration-none">
                                                    View Document
                                                </a>
                                            <?php else: ?>
                                                <span>No Attachment</span>
                                            <?php endif; ?>
                                            <div class="attachment-icons">
                                                <button 
                                                    class="edit-education btn btn-outline-primary btn-sm" 
                                                    type="button" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#edit-education"
                                                    data-id="<?php echo $education['id']; ?>"
                                                    data-school="<?php echo htmlspecialchars($education['school_name']); ?>"
                                                    data-education-level="<?php echo htmlspecialchars($education['educ_level']); ?>"
                                                    data-field-of-study="<?php echo htmlspecialchars($education['field_of_study']); ?>"
                                                    data-from-date="<?php echo htmlspecialchars($education['from_date']); ?>"
                                                    data-to-date="<?php echo htmlspecialchars($education['to_date']); ?>"
                                                    data-attachment-url="<?php echo htmlspecialchars($education['attachment_url']); ?>"
                                                >
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No education records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>                
                    </table>          
                    <button type="button" class="add-document" data-bs-toggle="modal" data-bs-target="#add-education"
                        <?php if ($educationCount >= 4) echo 'disabled style="opacity:0.5;pointer-events:none;"'; ?>>
                        + Add Education
                    </button>
                </div>
            </section>

            <?php


            ?>

            <section class="experience-container">
                <section class="box-container">  
                    <h2 class="header-info">Seafaring Experience</h2> 
                    <div class="experience-box">
                        <div>
                            <div class="content-editIcon">
                                <p class="experience-content">
                                    <?php echo !empty($user['seagoing_work']) ? nl2br(htmlspecialchars($user['seagoing_work'])) : 'N/A'; ?>
                                </p>
                                <span class="edit-wrapper">
                                    <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#edit-experience">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </span>
                            </div>
                    
                            <!-- Styled uploaded file box -->
                            <div class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                <a href="Uploads/Seaman/Seagoing/<?php echo htmlspecialchars($document['doc_url']) ?>" download class="text-decoration-none fw-medium text-dark">
                                    <?php echo htmlspecialchars($document['doc_url']) ?>
                                </a>
                            </div>
                        </div>
                        <button class="add-work-exp-btn" data-bs-toggle="modal" data-bs-target="#add-experience"
                            <?php if ($documentsCount >= 1) echo 'disabled style="opacity:0.5;pointer-events:none;"'; ?>>
                            + Add work experience
                        </button>
                    </div>
                </section>

                <?php
                // Fetch land-based work experience documents
                $landDocumentsQuery = "SELECT doc_url FROM seaman_documents WHERE seaman_email = ? AND type_of_doc = 'Land-Based Experience File'";
                $landDocumentsStmt = $conn->prepare($landDocumentsQuery);
                $landDocumentsStmt->bind_param("s", $seekerId);
                $landDocumentsStmt->execute();
                $landDocumentsResult = $landDocumentsStmt->get_result();
                $landDocumentsCount = $landDocumentsResult->num_rows;
                $landDocument = $landDocumentsResult->fetch_assoc();
                ?>
                
        
                <section class="box-container">
                    <h2 class="header-info">Land-Based Work Experience</h2>
                    <div class="experience-box">
                        <div>
                            <div class="content-editIcon">
                                <p class="experience-content">
                                    <?php echo !empty($user['non_seagoing_work']) ? nl2br(htmlspecialchars($user['non_seagoing_work'])) : 'N/A'; ?>
                                </p>
                                <span class="edit-wrapper">
                                    <button class="edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#edit-land-experience">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </span>
                            </div>
                    
                            <!-- Styled uploaded file box -->
                            <div class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                <a href="Uploads/Seaman/Land-Based-Exp/<?php echo htmlspecialchars($document['doc_url']) ?>" download class="text-decoration-none fw-medium text-dark">
                                    <?php echo htmlspecialchars($landDocument['doc_url']) ?>
                                </a>
                            </div>
                        </div>
                        <button class="add-work-exp-btn" data-bs-toggle="modal" data-bs-target="#add-land-experience"
                            <?php if ($landDocumentsCount >= 1) echo 'disabled style="opacity:0.5;pointer-events:none;"'; ?>>
                            + Add work experience
                        </button>
                    </div>
                </section>
            </section>
        </section>

    </main>

    <!-- ✅ Bootstrap Modal -->
    <section class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Edit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editForm" action="includes/update_profile.php" method="POST" enctype="multipart/form-data" autocomplete="off">

                <section class="modal-body">
                    <div class="container-fluid">
                        <div class="row g-3">
                            <!-- Name Row -->
                            <div class="col-md-4 col-sm-12">
                                <label for="firstName" class="form-label">First name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" autocomplete="off" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="middleName" class="form-label">Middle name</label>
                                <input type="text" class="form-control" name="middleName" id="middleName" value="<?php echo htmlspecialchars($user['middle_name']); ?>">
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="lastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                            </div>
                    
                            <!-- Second Row -->
                            <div class="col-md-4 col-sm-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="placeOfBirth" class="form-label">Place of birth</label>
                                <input type="text" class="form-control" id="placeOfBirth" name="placeOfBirth" value="<?php echo htmlspecialchars($user['place_of_birth']); ?>">
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="dob" class="form-label">Date of birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user['birthday']); ?>">
                            </div>
                    
                            <!-- Third Row -->
                            <div class="col-md-4 col-sm-12">
                                <label for="englishLevel" class="form-label">Level of English</label>
                                <select class="form-select" id="englishLevel" name="englishLevel">
                                    <option value="" disabled>Select from options</option>
                                    <option value="Beginner" <?php echo ($user['english_level'] === 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="Intermediate" <?php echo ($user['english_level'] === 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="Advanced" <?php echo ($user['english_level'] === 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                                    <option value="Fluent" <?php echo ($user['english_level'] === 'Fluent') ? 'selected' : ''; ?>>Fluent</option>
                                </select>
                            </div>
                    
                            <!-- Fourth Row -->
                            <div class="col-md-4 col-sm-12">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option selected disabled>Select from option</option>
                                    <option value="M" <?php echo ($user['gender'] === 'M') ? 'selected' : ''; ?>>Male</option>
                                    <option value="F" <?php echo ($user['gender'] === 'F') ? 'selected' : ''; ?>>Female</option>
                                    <option value="D" <?php echo ($user['gender'] === 'D') ? 'selected' : ''; ?>>Diverse</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="maritalStatus" class="form-label">Marital status</label>
                                <select class="form-select" id="maritalStatus" name="maritalStatus">
                                    <option selected disabled>Select marital status</option>
                                    <option value="Single" <?php echo ($user['marital_status'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                                    <option value="Married" <?php echo ($user['marital_status'] === 'Married') ? 'selected' : ''; ?>>Married</option>
                                    <option value="Divorced" <?php echo ($user['marital_status'] === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                                    <option value="Widowed" <?php echo ($user['marital_status'] === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" value="<?php echo htmlspecialchars($user['nationality']); ?>">
                            </div>
                    
                            <!-- Fifth Row -->
                            <div class="col-md-4 col-sm-12">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion" value="<?php echo htmlspecialchars($user['religion']); ?>">
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label for="rank" class="form-label">Rank</label>
                                <select class="form-select" id="rank" name="rank">
                                    <option selected disabled>Select Rank</option>
                                    <?php
                                    $rankQuery = "SELECT rank_name_shortcut FROM seaman_ranks";
                                    $rankResult = $conn->query($rankQuery);
                                    if ($rankResult->num_rows > 0) {
                                        while ($rank = $rankResult->fetch_assoc()) {
                                            $selected = ($user['rank'] === $rank['rank_name_shortcut']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($rank['rank_name_shortcut']) . '" ' . $selected . '>' . htmlspecialchars($rank['rank_name_shortcut']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                    
                            <!-- Job Status Row with Smaller Toggleable Buttons -->
                            <!-- <div class="col-md-6 col-sm-12">
                                <label for="jobStatus" class="form-label">Job status</label>
                                <div class="btn-group" role="group" aria-label="Job Status">
                                    <button type="button" class="btn btn-outline-primary btn-sm <?php echo ($user['job_status'] === 'Interested') ? 'active' : ''; ?>" id="interestedBtn" onclick="toggleJobStatus(this)">Interested</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm <?php echo ($user['job_status'] === 'Not Interested') ? 'active' : ''; ?>" id="notInterestedBtn" onclick="toggleJobStatus(this)">Not Interested</button>
                                </div>
                            </div> -->
                    
                            <!-- Email Row -->
                            <div class="col-12">
                                <label for="password" class="form-label">Password (Leave blank if unchanged)</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" autocomplete="off">
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

    <!---------EDUCATION Modal ---------->
    <section class="modal fade" id="add-education" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Education Information</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="educationForm" action="includes/add_education.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        
                <div class="modal-body">
                
                    <div class="mb-3">
                        <label for="school" class="form-label">School <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="school" name="school" placeholder="Enter school name">
                    </div>
        
                    <div class="mb-3">
                        <label for="educationLevel" class="form-label">Education level <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="educationLevel" name="educationLevel" placeholder="e.g. Certification, Bachelor's">
                    </div>
        
                    <div class="mb-3">
                        <label for="fieldOfStudy" class="form-label">Field of Study <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fieldOfStudy" name="fieldOfStudy" placeholder="e.g. Information Technology">
                    </div>
        
                    <div class="row mb-3">
                    <div class="col">
                        <label for="fromDate" class="form-label">From <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fromDate" name="fromDate">
                    </div>
                    <div class="col">
                        <label for="toDate" class="form-label">To <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="toDate" name="toDate">
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

    
    <!---------EDIT EDUCATION Modal ---------->
    <section class="modal fade" id="edit-education" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Education Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editEducationForm" action="includes/edit_education.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="educationId" id="educationId" value="">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editSchool" class="form-label">School <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editSchool" name="school" value="">
                        </div>

                        <div class="mb-3">
                            <label for="editEducationLevel" class="form-label">Education Level <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editEducationLevel" name="educationLevel" value="">
                        </div>

                        <div class="mb-3">
                            <label for="editFieldOfStudy" class="form-label">Field of Study <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editFieldOfStudy" name="fieldOfStudy" value="">
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="editFromDate" class="form-label">From <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="editFromDate" name="fromDate" value="">
                            </div>
                            <div class="col">
                                <label for="editToDate" class="form-label">To <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="editToDate" name="toDate" value="">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="editDocumentUpload" class="form-label">Add Document (PDF or Word)</label>
                            <input type="file" class="form-control" id="editDocumentUpload" name="documentUpload" accept=".pdf,.doc,.docx">
                            <small id="currentAttachment" class="form-text text-muted"></small>
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

    <!--add sea going experience modal -->
    <section class="modal fade" id="add-experience" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Seaman Experience</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="experienceForm" action="includes/add_experience.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        
                <div class="modal-body">
                
                    <div class="row g-3">
                    <!-- LEFT side -->
                    <div class="col-md-6">
                        <label for="seagoinExp" class="form-label">Sea Going Experience</label>
                        <textarea id="seagoinExp" name="seagoingExp" class="form-control" rows="10" placeholder="Enter notes..."></textarea>
                    </div>
        
                    <!-- RIGHT side -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                        <div class="text-muted mb-2">
                            <i class="fa fa-upload fa-2x mb-2"></i><br>
                            Drag and drop files here
                        </div>
                        <div>or</div>
                        <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('fileInput').click()">Browse File</button>
                        <input type="file" id="fileInput" name="documentUpload" style="display: none;" onchange="updateFileName()">
                        <p id="selectedFileName" class="mt-2 text-muted"></p>
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

    <!--edit seagoing experience modal -->
    <section class="modal fade" id="edit-experience" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update Sea Going Experience</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editExperienceForm" action="includes/edit_experience.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        
                <div class="modal-body">
                
                    <div class="row g-3">
                        <!-- LEFT side -->
                        <div class="col-md-6">
                            <label for="editSeagoingExp" class="form-label">SeaFaring Experience</label>
                            <textarea id="editSeagoingExp" name="editSeagoingExp" class="form-control" rows="10" placeholder="Enter notes..."><?php echo !empty($user['seagoing_work']) ? htmlspecialchars($user['seagoing_work']) : ''; ?></textarea>
                        </div>
        
                        <!-- RIGHT side -->
                        <div class="col-md-6 d-flex align-items-center justify-content-center">
                            <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                                <div class="text-muted mb-2">
                                    <i class="fa fa-upload fa-2x mb-2"></i><br>
                                    Drag and drop files here
                                </div>
                                <div>or</div>
                                <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('editFileInput').click()">Browse File</button>
                                <input type="file" id="editFileInput" name="documentUpload" style="display: none;" onchange="updateEditFileName()">
                                <p id="editSelectedFileName" class="mt-2 text-muted">
                                    <?php if (!empty($document['doc_url'])): ?>
                                        Current File: <?php echo htmlspecialchars($document['doc_url']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                
                </div>
                <div class="modal-footer d-flex gap-3 mt-4">
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

    
    <!--landbase experience modal -->
    <section class="modal fade" id="add-land-experience" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Land-base Experience</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="landExperienceForm" action="includes/add_land_experience.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        
                <div class="modal-body">
                
                    <div class="row g-3">
                    <!-- LEFT side -->
                    <div class="col-md-6">
                        <label for="landBasedExp" class="form-label">Land-base Experience</label>
                        <textarea id="landBasedExp" name="landBasedExp" class="form-control" rows="10" placeholder="Enter notes..."></textarea>
                    </div>
        
                    <!-- RIGHT side -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                        <div class="text-muted mb-2">
                            <i class="fa fa-upload fa-2x mb-2"></i><br>
                            Drag and drop files here
                        </div>
                        <div>or</div>
                        <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('landfileInput').click()">Browse File</button>
                        <input type="file" id="landfileInput" name="landdocumentUpload" style="display: none;" onchange="updatelandFileName()">
                        <p id="selectedlandFileName" class="mt-2 text-muted"></p>
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

    <!--edit landbase experience modal -->
    <section class="modal fade" id="edit-land-experience" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update Land-base Experience</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editLandExperienceForm" action="includes/edit_land_experience.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        
                <div class="modal-body">
                
                    <div class="row g-3">
                    <!-- LEFT side -->
                    <div class="col-md-6">
                        <label for="editlandBasedExp" class="form-label">Land-base Experience</label>
                        <textarea id="editlandBasedExp" name="editlandBasedExp" class="form-control" rows="10" placeholder="Enter notes..."><?php echo !empty($user['non_seagoing_work']) ? htmlspecialchars($user['non_seagoing_work']) : ''; ?></textarea>
                    </div>
        
                    <!-- RIGHT side -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="border border-2 border-dashed rounded p-4 text-center w-100" style="min-height: 220px;">
                            <div class="text-muted mb-2">
                                <i class="fa fa-upload fa-2x mb-2"></i><br>
                                Drag and drop files here
                            </div>
                            <div>or</div>
                            <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('editlandFileInput').click()">Browse File</button>
                            <input type="file" id="editlandFileInput" name="landdocumentUpload" style="display: none;" onchange="updateEditFileName()">
                            <p id="editSelectedlandFileName" class="mt-2 text-muted">
                                <?php if (!empty($landDocument['doc_url'])): ?>
                                    Current File: <?php echo htmlspecialchars($landDocument['doc_url']); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    </div>
                
                </div>
                <div class="modal-footer d-flex gap-3 mt-4">
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
  

    <script>
        function toggleJobStatus(button) {
            // Toggle the active class to change the button's appearance
            const buttons = document.querySelectorAll('.btn-group .btn');
            buttons.forEach((btn) => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
    
            // Update the job status text based on the selected button
            const jobStatusText = document.getElementById("jobStatusText");
            if (button.id === "interestedBtn") {
                jobStatusText.textContent = "Interested";
            } else {
                jobStatusText.textContent = "Not Interested";
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const editButtons = document.querySelectorAll(".edit-education");
            const educationIdInput = document.getElementById("educationId");
            const schoolInput = document.getElementById("editSchool");
            const educationLevelInput = document.getElementById("editEducationLevel");
            const fieldOfStudyInput = document.getElementById("editFieldOfStudy");
            const fromDateInput = document.getElementById("editFromDate");
            const toDateInput = document.getElementById("editToDate");
            const currentAttachment = document.getElementById("currentAttachment");

            editButtons.forEach(button => {
                button.addEventListener("click", () => {
                    // Populate the modal fields with data from the button's data attributes
                    educationIdInput.value = button.getAttribute("data-id");
                    schoolInput.value = button.getAttribute("data-school");
                    educationLevelInput.value = button.getAttribute("data-education-level");
                    fieldOfStudyInput.value = button.getAttribute("data-field-of-study");
                    fromDateInput.value = button.getAttribute("data-from-date");
                    toDateInput.value = button.getAttribute("data-to-date");

                    // Show the current attachment if it exists
                    const attachmentUrl = button.getAttribute("data-attachment-url");
                    if (attachmentUrl) {
                        currentAttachment.textContent = `Current Attachment: ${attachmentUrl}`;
                    } else {
                        currentAttachment.textContent = "No attachment uploaded.";
                    }
                });
            });
        });

        function confirmDeletion() {
            return confirm("Are you sure you want to delete this record? This action cannot be undone.");
        }

        function updateFileName() {
            const fileInput = document.getElementById('fileInput');
            const fileNameDisplay = document.getElementById('selectedFileName');

            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = `Selected File: ${fileInput.files[0].name}`;
            } else {
                fileNameDisplay.textContent = ''; // Clear the text if no file is selected
            }
        }

        function updatelandFileName() {
            const fileInput = document.getElementById('landfileInput');
            const fileNameDisplay = document.getElementById('selectedlandFileName');

            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = `Selected File: ${fileInput.files[0].name}`;
            } else {
                fileNameDisplay.textContent = ''; // Clear the text if no file is selected
            }
        }

        // Set the session timeout duration (in milliseconds)
        const sessionTimeout = 900000; // 15 minutes
        const warningTime = 840000; // 14 minutes (1 minute before timeout)

        // Show a warning before the session times out
        setTimeout(() => {
            alert("Your session will expire in 1 minute. Please save your work.");
        }, warningTime);

        // Redirect to the login page after the session times out
        setTimeout(() => {
            window.location.href = "user-login-signup.php?type=error&message=Session timed out. Please log in again.";
        }, sessionTimeout);
    </script>
    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
</body>
</html>