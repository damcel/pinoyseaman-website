<?php
session_start();
include 'connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch employer records with pagination
$query = "SELECT company_code, company, email, fax, website FROM employer WHERE verify = ''
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
    <link rel="stylesheet" href="css/admin-panel-verification.css">
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
                            <th>Email</th>
                            <th>License</th>
                            <th>website</th>
                            <th>Approve</th>
                            <th>Decline</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr data-id="<?php echo $row['company_code']; ?>">
                                <td data-label="Company"><?php echo htmlspecialchars($row['company']); ?></td>
                                <td data-label="Email"><?php echo htmlspecialchars($row['email']) ?></td>
                                <td data-label="license"><?php echo htmlspecialchars($row['fax']); ?></td>
                                <td data-label="website"><?php echo htmlspecialchars($row['website']); ?></td>
                                <td><button class="approve-btn" data-id="<?php echo $row['company_code']; ?>">Approve</button></td>
                                <td><button class="delete-btn" data-id="<?php echo $row['company_code']; ?>">Decline</button></td>
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
        const rowsPerPage = 8;
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
      
          // ← Previous Arrow (only if currentPage > 1)
          if (currentPage > 1) {
            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prevBtn.addEventListener("click", () => displayRows(currentPage - 1));
            paginationContainer.appendChild(prevBtn);
          }
      
          // Page Number Buttons
          for (let i = 1; i <= pageCount; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            if (i === currentPage) btn.classList.add("active");
      
            btn.addEventListener("click", () => displayRows(i));
            paginationContainer.appendChild(btn);
          }
      
          // → Next Arrow (only if not on last page)
          if (currentPage < pageCount) {
            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            nextBtn.addEventListener("click", () => displayRows(currentPage + 1));
            paginationContainer.appendChild(nextBtn);
          }
        }
      
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
            const email = row.querySelector("td[data-label='Email']").textContent.toLowerCase();
            const company = row.querySelector("td[data-label='Company']").textContent.toLowerCase();
            const isMatch = email.includes(term) || company.includes(term);

            row.style.display = isMatch ? "" : "none";

            if (isMatch) {
                const name = `${company}, ${email}`;
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

    <script>
        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                if (confirm("Approve this employer?")) {
                    handleEmployerAction(id, 'approve', btn);
                }
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                if (confirm("Are you sure you want to decline this employer?")) {
                    handleEmployerAction(id, 'decline', btn);
                }
            });
        });

        function handleEmployerAction(id, action, button) {
            fetch('employer_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${id}&action=${action}`
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'approved' || result === 'declined') {
                    // Remove the row from the table
                    const row = button.closest('tr');
                    row.remove();
                } else {
                    alert("Action failed.");
                }
            });
        }
    </script>

      
</body>
</html>