<?php
session_start();
include 'connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch employer records with pagination
$query = "SELECT company_code, company, email, phone, secret, member_type FROM employer WHERE verify = 'y'
        ORDER BY date_registered DESC";
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
    <link rel="stylesheet" href="css/admin-panel-employer.css">
    <link rel="icon" href="../Pinoyseaman.ico" type="image/x-icon"> 
    <title>Admin Panel</title>
    
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
                      <input type="text" id="searchInput" placeholder="Search by email, Company, or rank..." />
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
                            <th>Company Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Member Type</th>
                            <th>Premium Duration</th>
                            <th>Account Deletion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td data-label="Company"><?= htmlspecialchars($row['company']) ?></td>
                                <td data-label="Phone"><?= htmlspecialchars($row['phone']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                                <td data-label="password"><?= htmlspecialchars($row['secret']) ?></td>
                                <td data-label="Member-type"><?= htmlspecialchars($row['member_type']) ?></td>
                                <td data-label="Premium-duration">TBA</td> <!-- Replace with actual duration if you store it -->
                                <td>
                                    <form action="delete_employer.php" method="post" onsubmit="return confirm('Are you sure you want to delete this employer?');">
                                        <input type="hidden" name="company_code" value="<?= htmlspecialchars($row['company_code']) ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
           
                </table>    
                <div id="pagination" class="pagination-controls"></div>
            </div>      
        </section>

    </main>

    <script src="../script/sidenav.js"></script>
    <script src="../script/profile-dropdown-menu.js"></script>
    <script>
        const rowsPerPage =8;
        const table = document.querySelector(".table-content tbody");
        const rows = Array.from(table.querySelectorAll("tr"));
        const paginationContainer = document.getElementById("pagination");
      
        let currentPage = 1;
        const pageCount = Math.ceil(rows.length / rowsPerPage);
      
        function displayRows(page) {
          const start = (page - 1) * rowsPerPage;
          const end = start + rowsPerPage;
      
          rows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? "" : "none";
          });
      
          currentPage = page;
          updatePaginationUI();
        }
      
        function updatePaginationUI() {
    paginationContainer.innerHTML = "";

    // Previous Button
    if (currentPage > 1) {
        const prevBtn = document.createElement("button");
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        prevBtn.addEventListener("click", () => displayRows(currentPage - 1));
        paginationContainer.appendChild(prevBtn);
    }

    const maxVisiblePages = 5; // Current ± 1, first & last
    const ellipsis = document.createElement("span");
    ellipsis.textContent = "...";
    ellipsis.className = "ellipsis";

    for (let i = 1; i <= pageCount; i++) {
        if (
            i === 1 || 
            i === pageCount || 
            i === currentPage || 
            i === currentPage - 1 || 
            i === currentPage + 1
        ) {
            const btn = document.createElement("button");
            btn.textContent = i;
            if (i === currentPage) btn.classList.add("active");
            btn.addEventListener("click", () => displayRows(i));
            paginationContainer.appendChild(btn);
        } else if (
            (i === 2 && currentPage > 3) || 
            (i === pageCount - 1 && currentPage < pageCount - 2)
        ) {
            paginationContainer.appendChild(ellipsis.cloneNode(true));
        }
    }

    // Next Button
    if (currentPage < pageCount) {
        const nextBtn = document.createElement("button");
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        nextBtn.addEventListener("click", () => displayRows(currentPage + 1));
        paginationContainer.appendChild(nextBtn);
    }
}

      
        // Initialize on load
        displayRows(1);
    </script>
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
                const email = row.querySelector("td[data-label='email']").textContent.toLowerCase();
                const Company = row.querySelector("td[data-label='Company']").textContent.toLowerCase();
                const isMatch = email.includes(term) || Company.includes(term) || rank.includes(term);
                row.style.display = isMatch ? "" : "none";

                if (isMatch) {
                    const name = `${Company}, ${email}`;
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