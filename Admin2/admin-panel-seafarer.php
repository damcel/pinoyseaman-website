<?php
session_start();
include 'connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pagination setup
$limit = 10; // rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total job_seekers
$count_query = "SELECT COUNT(*) as total FROM job_seeker";
$count_result = mysqli_query($link, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch current page records
$query = "SELECT last_name, first_name, rank, email FROM job_seeker ORDER BY date DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($link, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="css/admin-panel-seafarer.css">
    <link rel="icon" href="../Pinoyseaman.ico" type="image/x-icon"> 
    <title>Admin Panel</title>

    <style>
        .pagination-controls {
            margin-top: 1rem;
            text-align: center;
        }

        .pagination-controls a {
            padding: 6px 12px;
            margin: 2px;
            border: 1px solid #ccc;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }

        .pagination-controls a.active {
            background-color: #0B1C33;
            color: #fff;
            border-color: #0B1C33;
        }

    </style>

</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="admin-sidebar">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="../pinoyseaman-logo/admin-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
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
              <li class="span-style">
                <a href="admin-panel-verification.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-user-check"></i><span>EMPLOYER VERIFICATION</span>
                </a>
              </li>
              <li  class="separator">
                <a href="admin-panel-seafarer.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-users"></i><span>SEAFARER</span>
                </a>
              </li>
              <li>
                <a href="admin-panel-employer.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-users-line"></i><span>EMPLOYER</span>
                </a>
              </li>
              <li class="separator">
                <a href="admin-panel-statistics.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-chart-simple"></i><span>STATISTICS</span>
                </a>
              </li>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-container">
        <section class="header-ctn">
            <div class="search-ctn">
                <div class="search-ctn">
                    <div class="search-wrapper">
                      <i class="fa fa-search search-icon"></i>
                      <input type="text" id="searchInput" placeholder="Search by firstname, lastname, or rank..." />
                      <ul id="searchResults" class="dropdown-list"></ul>
                    </div>
                </div>
            </div>
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="userprofile.php" class="prfl">Profile Settings</a>
                    <a href="includes/logout.php">Logout</a>
                </div>
            </div>
        </section>

        <section class="job-list-container">
            <div class="education-container">
                <table class="table-content">
                    <thead>
                        <tr>
                            <th>lastname</th>
                            <th>firstname</th>
                            <th>Rank</th>
                            <th>Email</th>
                            <th>Account Deletion</th>
                        </tr>
                    </thead>
                    <tbody>
<?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td data-label="Lastname"><?= htmlspecialchars($row['last_name']) ?></td>
        <td data-label="Firstname"><?= htmlspecialchars($row['first_name']) ?></td>
        <td data-label="Rank"><?= htmlspecialchars($row['rank']) ?></td>
        <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
        <td><button class="delete-btn">Delete</button></td>
    </tr>
<?php endwhile; ?>
</tbody>
             
                </table>    
                <div id="pagination" class="pagination-controls">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">&larr;</a>
    <?php endif; ?>

    <?php
    // Always show first 3 pages
    for ($i = 1; $i <= min(3, $total_pages); $i++):
    ?>
        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($total_pages > 4 && $page > 4): ?>
        ... 
    <?php endif; ?>

    <?php
    // Show current page if it's in the middle
    if ($page > 3 && $page < $total_pages - 2):
    ?>
        <a href="?page=<?= $page ?>" class="active"><?= $page ?></a>
        ...
    <?php endif; ?>

    <?php
    // Show only the last page
    if ($total_pages > 4): ?>
        <a href="?page=<?= $total_pages ?>" class="<?= ($page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
<?php endif; ?>


    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">&rarr;</a>
    <?php endif; ?>
</div>


            </div>      
        </section>

    </main>

    <script src="../script/sidenav.js"></script>
    <script src="../script/profile-dropdown-menu.js"></script>
    
      <script>
        const searchInput = document.getElementById("searchInput");
        const searchResults = document.getElementById("searchResults");
        const allRows = document.querySelectorAll(".table-content tbody tr");

        searchInput.addEventListener("input", function () {
            const term = this.value.toLowerCase();
            searchResults.innerHTML = "";
            searchResults.style.display = "none";

            if (term.length === 0) {
                allRows.forEach(row => row.style.display = "");
                return;
            }

            let matchFound = false;
            allRows.forEach(row => {
                const firstname = row.querySelector("td[data-label='Firstname']").textContent.toLowerCase();
                const lastname = row.querySelector("td[data-label='Lastname']").textContent.toLowerCase();
                const rank = row.querySelector("td[data-label='Rank']").textContent.toLowerCase();
                const isMatch = firstname.includes(term) || lastname.includes(term) || rank.includes(term);
                row.style.display = isMatch ? "" : "none";

                if (isMatch) {
                    const name = `${lastname}, ${firstname} (${rank})`;
                    const li = document.createElement("li");
                    li.textContent = name;
                    li.addEventListener("click", () => {
                        searchInput.value = name;
                        searchResults.style.display = "none";
                        allRows.forEach(r => r.style.display = r === row ? "" : "none");
                    });
                    searchResults.appendChild(li);
                    matchFound = true;
                }
            });

            if (matchFound) {
                searchResults.style.display = "block";
            }
        });

        document.addEventListener("click", (e) => {
            if (!e.target.closest(".search-wrapper")) {
                searchResults.style.display = "none";
            }
        });
    </script>
      
</body>
</html>