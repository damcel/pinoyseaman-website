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

$applications = [];
$sql = "SELECT ja.*, e.address FROM job_applicants AS ja
        INNER JOIN employer AS e ON ja.company_code = e.company_code
        WHERE ja.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $seekerEmail);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Applied History</title>
    <style>
.custom-menu-toggle {
    position: relative;
    all: unset;
    cursor: pointer;
}

.custom-dropdown {
  display: none;
  position: absolute;
  margin-top: 8px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 180px; 
}

.custom-dropdown.show {
  display: block;
}

.custom-dropdown .dropdown-item {
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  padding: 12px 16px;
  cursor: pointer;
  font: inherit;
  transition: background-color 0.2s;
}

.custom-dropdown .dropdown-item:hover {
  background-color: #f0f0f0;
}

.custom-dropdown .dropdown-item.delete {
  background-color: #ffecec;
  color: red;
}

.custom-dropdown .dropdown-item.delete:hover {
  background-color: #ffdada;
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
        <section class="profile-setup-container">
            <section>
                <div class="tabs-container">
                    <nav class="tabs">
                        <ul>
                            <li class="tab active"><i class="fa-regular fa-bookmark"></i><a href="history.php">Applied</a></li>
                            <!-- <li class="tab"><i class="fa-regular fa-circle-check"></i><a href="saved.html">Saved</a></li> -->
                        </ul>
                    </nav>
                </div>
            </section>
            <p class="job-count"><?= count($applications) ?> jobs</p>
            <section class="job-list">
            <?php if (count($applications) > 0): ?>
                <?php foreach ($applications as $application): ?>
                    <article class="job-history-card" data-code="<?= $application['code'] ?>">
                        <div>
                            <h3><?= htmlspecialchars($application['job_hiring']) ?></h3>
                            <p><?= htmlspecialchars($application['company']) ?></p>
                            <p><?= htmlspecialchars($application['address']) ?></p>
                            <p><?= htmlspecialchars(date("F j, Y", strtotime($application['date']))) ?></p>
                            <?php if ($application['mark'] === 'Viewed'): ?>
                                <button class="viewed-btn">Viewed by employer <i class="fa-solid fa-circle-check"></i></button>
                            <?php else: ?>
                                <button class="not-viewed-btn">Not viewed yet <i class="fa-solid fa-clock"></i></button>
                            <?php endif; ?>
                        </div>
                        <div class="menu-container">
                            <button class="custom-menu-toggle" onclick="toggleMenu(this)"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="custom-dropdown">
                                <button class="dropdown-item delete" onclick="deleteApplication(this)">Cancel application</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have not applied to any jobs yet.</p>
            <?php endif; ?>
            </section>

            <!-- <p class="job-count">8 jobs</p>
            <section class="job-list">
                    <article class="job-history-card">
                        <div>
                            <h3>Tanker Vessel</h3>
                            <p>Company</p>
                            <p>Address</p>
                            <p>Applied date</p>
                            <button class="viewed-btn">Viewed by employer <i class="fa-solid fa-circle-check"></i></i></button>
                        </div>
                        <div class="menu-container">
                            <button class="custom-menu-toggle" onclick="toggleMenu()"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="custom-dropdown" id="dropdownMenu">
                                <button class="dropdown-item delete">Delete application</button>
                            </div>
                        </div>
                    </article>
                    <article class="job-history-card">
                        <div>
                            <h3>Tanker Vessel</h3>
                            <p>Company</p>
                            <p>Address</p>
                            <p>Applied date</p>
                            <button class="viewed-btn">Viewed by employer <i class="fa-solid fa-circle-check"></i></i></button>
                        </div>
                        <div class="menu-container">
                            <button class="custom-menu-toggle" onclick="toggleMenu()"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="custom-dropdown" id="dropdownMenu">
                                <button class="dropdown-item delete">Delete application</button>
                            </div>
                        </div>
                    </article>
                    <article class="job-history-card">
                        <div>
                            <h3>Tanker Vessel</h3>
                            <p>Company</p>
                            <p>Address</p>
                            <p>Applied date</p>
                            <button class="viewed-btn">Viewed by employer <i class="fa-solid fa-circle-check"></i></i></button>
                        </div>
                        <div class="menu-container">
                            <button class="custom-menu-toggle" onclick="toggleMenu()"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="custom-dropdown" id="dropdownMenu">
                                <button class="dropdown-item delete">Delete application</button>
                            </div>
                        </div>
                    </article>
                    <article class="job-history-card">
                        <div>
                            <h3>Tanker Vessel</h3>
                            <p>Company</p>
                            <p>Address</p>
                            <p>Applied date</p>
                            <button class="viewed-btn">Viewed by employer <i class="fa-solid fa-circle-check"></i></i></button>
                        </div>
                        <div class="menu-container">
                            <button class="custom-menu-toggle" onclick="toggleMenu()"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="custom-dropdown" id="dropdownMenu">
                                <button class="dropdown-item delete">Delete application</button>
                            </div>
                        </div>
                    </article>
                    <article class="job-history-card">
                        <div>
                            <h3>Tanker Vessel</h3>
                            <p>Company</p>
                            <p>Address</p>
                            <p>Applied date</p>
                            <button class="viewed-btn">Viewed by employer <i class="fa-solid fa-circle-check"></i></i></button>
                        </div>
                        <div class="menu-container">
                            <button class="custom-menu-toggle" onclick="toggleMenu()"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="custom-dropdown" id="dropdownMenu">
                                <button class="dropdown-item delete">Delete application</button>
                            </div>
                        </div>
                    </article>
                    <article class="job-history-card">
                        <div>
                            <h3>Tanker Vessel</h3>
                            <p>Company</p>
                            <p>Address</p>
                            <p>Applied date</p>
                            <button class="viewed-btn">Viewed by employer <i class="fa-solid fa-circle-check"></i></i></button>
                        </div>
                        <div class="menu-container">
                            <button class="custom-menu-toggle" onclick="toggleMenu()"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                            <div class="custom-dropdown" id="dropdownMenu">
                                <button class="dropdown-item delete">Delete application</button>
                            </div>
                        </div>
                    </article>
                </section> -->
        </section>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
          document.querySelectorAll(".custom-menu-toggle").forEach(button => {
            button.addEventListener("click", function (e) {
              e.stopPropagation(); // Prevent click bubbling
              const menu = this.nextElementSibling;
      
              // Hide all other custom dropdowns first
              document.querySelectorAll(".custom-dropdown").forEach(d => {
                if (d !== menu) d.classList.remove("show");
              });
      
              // Toggle current dropdown
              menu.classList.toggle("show");
            });
          });
      
          document.addEventListener("click", () => {
            document.querySelectorAll(".custom-dropdown").forEach(menu => {
              menu.classList.remove("show");
            });
          });
        });

        function deleteApplication(button) {
            const card = button.closest(".job-history-card");
            const applicationCode = card.getAttribute("data-code");

            if (!confirm("Are you sure you want to delete this application?")) return;

            fetch("includes/seaman_delete_application.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `code=${encodeURIComponent(applicationCode)}`,
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    card.remove();
                } else {
                    alert("Failed to delete application.");
                }
            })
            .catch(error => {
                console.error("Error deleting:", error);
                alert("Something went wrong.");
            });
        }

      </script>
    <script src="script/sidenav.js"></script>
    <script src="script/progress-bar.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script src="script/currency-calendar.js"></script>
</body>
</html>